<?php

namespace App\Jobs;

use App\Helper;
use App\Models\Bank;
use App\Models\Transaction;
use App\Providers\NordigenServiceProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchTransactionFromBank implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        if (config('app.bank_sync.available')) {
            $banks = Bank::whereNotNull("account_id")->get();
            try {
                $bankProvider = new NordigenServiceProvider(
                    config('app.bank_sync.secret_id'),
                    config('app.bank_sync.secret_key')
                );
                $dateFrom = new \DateTime();
                $dateFrom->sub(new \DateInterval("P1D"));
                foreach ($banks as $bank) {
                    $data = $bankProvider->getTransactions(
                        $bank->account_id,
                        $dateFrom->format('Y-m-d'),
                        $dateFrom->format('Y-m-d')
                    );
                    if (array_key_exists("transactions", $data) && array_key_exists("booked", $data["transactions"])) {
                        foreach ($data["transactions"]["booked"] as $transaction) {
                            $this->createTransactionFromBank($bank->space_id, $transaction);
                        }
                    }
                }
            } catch (\Exception $exception) {
            }
        }
    }

    private function createTransactionFromBank(int $spaceId, array $bankData)
    {
        $params = [
            'type' => floatval($bankData['transactionAmount']['amount']) < 0 ? 'spending' : 'earning',
            'space_id' => $spaceId,
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
