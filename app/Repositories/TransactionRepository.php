<?php

namespace App\Repositories;

use App\Helper;
use App\Models\Transaction;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository
{
    public function getValidationRules(bool $forUpdate = false): array
    {
        $rules = [
            'tag_id' => 'nullable|exists:tags,id', // TODO CHECK IF TAG BELONGS TO USER
            'date' => 'required|date|date_format:Y-m-d',
            'description' => 'required|max:255',
            'currency_id' => 'nullable|exists:currencies,id',
            'amount' => 'required|regex:/^\d*(\.\d{2})?$/'
        ];

        if (!$forUpdate) {
            $rules["type"] = 'required|in:earning,spending';
        }

        return $rules;
    }

    public function create(
        int $spaceId,
        string $transactionType,
        ?int $importId,
        ?int $recurringId,
        ?int $tagId,
        string $date,
        string $description,
        int $amount
    ): Transaction {
        return Transaction::create([
            'space_id' => $spaceId,
            'type' => $transactionType,
            'import_id' => $importId,
            'recurring_id' => $recurringId,
            'tag_id' => $tagId,
            'happened_on' => $date,
            'description' => $description,
            'amount' => $amount
        ]);
    }

    public function update(int $transactionId, array $data): void
    {
        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            throw new Exception('Could not find transaction with ID ' . $transactionId);
        }

        $transaction->fill($data)->save();
    }

    public function getWeeklyBalance(string $year): array
    {
        $weeks = [];
        $balance = 0;

        $weekMode = 3;

        if (date('w', strtotime($year . '-01-01')) == 1) {
            $weekMode = 7;
        }

        for ($i = 1; $i <= 53; $i++) { // This used to be 52, IDK what happens after we moved it to 53
            $balance += Transaction::ofSpace(session('space_id'))
                ->whereRaw('YEAR(happened_on) = ? AND WEEK(happened_on, ?) = ?', [$year, $weekMode, $i])
                ->where('type', 'earning')
                ->sum('amount');

            $balance -= Transaction::ofSpace(session('space_id'))
                ->whereRaw('YEAR(happened_on) = ? AND WEEK(happened_on, ?) = ?', [$year, $weekMode, $i])
                ->where('type', 'spending')
                ->sum('amount');

            $weeks[$i] = Helper::formatNumber($balance / 100);
        }

        return $weeks;
    }

    /**
     * Get All Transaction from month and year
     * @param int $month
     * @param int $year
     * @param array $filterBy
     * @return Collection
     */
    public function getTransactionsByYearMonth(int $month, int $year, array $filterBy = []): Collection
    {
        $transactions = Transaction::ofSpace(session('space_id'))
            ->orderBy('happened_on', 'DESC')
            ->whereRaw('YEAR(`happened_on`) = ? AND MONTH(`happened_on`) = ?', [$year, $month]);

        if ($filterBy) {
            if (array_key_exists("tag", $filterBy)) {
                $transactions->whereIn('tag_id', [$filterBy['tag']]);
            }
        }

        return $transactions->get();
    }

    public function getTransactionsRemoved(): Collection
    {
        return Transaction::ofSpace(session('space_id'))
            ->onlyTrashed()
            ->orderBy('happened_on', 'DESC')
            ->get();
    }
}
