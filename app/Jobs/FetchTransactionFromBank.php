<?php

namespace App\Jobs;

use App\FindTagFromAI;
use App\Helper;
use App\Models\Bank;
use App\Models\Tag;
use App\Models\Transaction;
use App\Providers\NordigenServiceProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchTransactionFromBank implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use FindTagFromAI;

    protected array $tags = [];

    public function handle(): void
    {
        if (config('bank_sync.available')) {
            $banks = Bank::whereNotNull("account_id")->get();
            try {
                $bankProvider = new NordigenServiceProvider(
                    config('bank_sync.secret_id'),
                    config('bank_sync.secret_key')
                );
                $dateFrom = new \DateTime();
                $dateFrom->sub(new \DateInterval("P1D"));
                foreach ($banks as $bank) {
                    $tags = Tag::ofSpace($bank->space_id)->get()->toArray();
                    try {
                        $data = $bankProvider->getTransactions(
                            $bank->account_id,
                            $dateFrom->format('Y-m-d'),
                            $dateFrom->format('Y-m-d')
                        );
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                        continue;
                    }
                    if (array_key_exists("transactions", $data) && array_key_exists("booked", $data["transactions"])) {
                        foreach ($data["transactions"]["booked"] as $transaction) {
                            $this->createTransactionFromBank($bank, $transaction, $tags);
                        }
                    }
                }
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
    }

    private function createTransactionFromBank(Bank $bank, array $bankData, ?array $tags = null): void
    {
        $description = $this->cleanDescription($bankData['remittanceInformationUnstructuredArray']);
        $tagId = null;
        if ((bool)$bank->ai_active) {
            //We get the similar transation
            $similar = Transaction::ofSpace($bank->space_id);
            $words = explode(" ", $description);
            $similar->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('description', 'LIKE', "%{$word}%");
                }
            });

            //For get transaction matching with number of words
            $sumCases = [];
            foreach ($words as $word) {
                $sumCases[] = "CASE WHEN description LIKE '%" . $word . "%' THEN 1 ELSE 0 END";
            }
            $sumExpression = implode(' + ', $sumCases);
            $similar->select('*', DB::raw("($sumExpression) as relevance_score"))
                ->orderByDesc('relevance_score')
                ->limit(1);
            $result = $similar->first();
            $tagOnSimilar = null;
            if ($result) {
                $tagOnSimilar = $result->tag_id;
            }

            $tagId = $this->findTagFromIA($description, $tags, $tagOnSimilar);
        }
        $params = [
            'type' => floatval($bankData['transactionAmount']['amount']) < 0 ? 'spending' : 'earning',
            'space_id' => $bank->space_id,
            'tag_id' => $tagId,
            'happened_on' => $bankData['valueDate'],
            'description' => $this->cleanDescription($bankData['remittanceInformationUnstructuredArray']),
            'amount' => Helper::rawNumberToInteger(
                str_replace("-", "", $bankData['transactionAmount']['amount'])
            )
        ];
        Transaction::create($params);
    }

    private function cleanDescription(string|array $description): string
    {
        //If it's array, make it as string
        if (is_array($description)) {
            $description = implode(" ", $description);
        }

        //Step 1 remove \n
        $description = str_replace("\n", " ", $description);

        //Step 2, make it as array with separator space
        $description = explode(" ", $description);

        //Step 3,remove empty value
        $description = array_filter($description);

        //Step 4, remake it as string
        return implode(" ", $description);
    }
}
