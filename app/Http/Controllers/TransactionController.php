<?php

namespace App\Http\Controllers;

use App\Models\Earning;
use App\Models\Space;
use App\Models\Spending;
use App\Models\Tag;
use App\Repositories\CurrencyRepository;
use App\Repositories\RecurringRepository;
use App\Repositories\TagRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    private TransactionRepository $repository;
    private CurrencyRepository $currencyRepository;
    private RecurringRepository $recurringRepository;
    private TagRepository $tagRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        CurrencyRepository $currencyRepository,
        RecurringRepository $recurringRepository,
        TagRepository $tagRepository
    ) {
        $this->repository = $transactionRepository;
        $this->currencyRepository = $currencyRepository;
        $this->recurringRepository = $recurringRepository;
        $this->tagRepository = $tagRepository;
    }

    public function index(Request $request): Response
    {
        $filterBy = [];

        if ($request->get('filterBy')) {
            $filterBy = (array) $request->get('filterBy');
        }

        $currentMonthIndex = (int)$request->get("monthIndex", 0);
        $currentDate = new \DateTime();
        $interval = new \DateInterval(("P" . abs($currentMonthIndex) . "M"));
        $currentDate->setDate($currentDate->format("Y"), $currentDate->format("m"), 15);
        if ($currentMonthIndex < 0) {
            $currentDate->sub($interval);
        } else {
            $currentDate->add($interval);
        }
        $year = $currentDate->format("Y");
        $month = $currentDate->format("m");

        $transactions = $this->repository->getTransactionsByYearMonth($month, $year, $filterBy);
        $transactionsChart = $this->getTransactionsForChart($transactions);

        $search = [
            "year" => $year,
            "month" => $month
        ];

        $tagsPrice = $this->tagRepository->getMostExpensiveTags(session('space_id'), $search);

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'tagsPrice' => $tagsPrice,
            'transactionsChart' => $transactionsChart,
            'currentMonthIndex' => $currentMonthIndex,
            'month' => $currentDate->format("n"),
            'year' => $year,
            'tags' => Tag::ofSpace(session('space_id'))->get()
        ]);
    }

    public function create(): Response
    {
        $tags = [];

        foreach (Tag::ofSpace(session('space_id'))->get() as $tag) {
            $tags[] = [
                'key' => $tag->id,
                'label' => '<div class="row"><div class="row__column row__column--compact row__column--middle mr-1"><div style="width: 15px; height: 15px; border-radius: 2px; background: #' . $tag->color . ';"></div></div><div class="row__column row__column--middle">' . $tag->name . '</div></div>' // phpcs:ignore
            ];
        }

        return Inertia::render('Transactions/Create', [
            'tags' => $tags,
            'currencies' => $this->currencyRepository->getIfConversionRatePresent(),
            'defaultTransactionType' => Auth::user()->default_transaction_type,
            'firstDayOfWeek' => Auth::user()->first_day_of_week,
            'defaultCurrencyId' => Space::find(session('space_id'))->currency_id,
            'recurringsIntervals' => $this->recurringRepository->getSupportedIntervals()
        ]);
    }

    public function trash(): Response
    {
        return Inertia::render('Transactions/Trash', [
            'transactions' => $this->repository->getTransactionsRemoved()
        ]);
    }

    public function purgeAll(): RedirectResponse
    {
        //NO need check because if it's in trash, check already ok. And it's linked to space_id
        Earning::ofSpace(session('space_id'))->onlyTrashed()->forceDelete();
        Spending::ofSpace(session('space_id'))->onlyTrashed()->forceDelete();

        return back();
    }

    private function getTransactionsForChart(array $transactions): array
    {
        $transactionsChart = [];
        $earningKey = __('models.earnings');
        $earningColor = "#32a852"; //Green
        $spendingKey = __('general.other');
        $spendingColor = "#DCDCDC"; //Grey
        foreach ($transactions as $transaction) {
            $keyToUse = $earningKey;
            $colorToUse = $earningColor;
            $amount = (float)$transaction->formatted_amount;
            //If spending we take tag name
            if ($transaction instanceof Spending) {
                $keyToUse = $spendingKey;
                $colorToUse = $spendingColor;
                $amount = -abs($amount);
                if (! is_null($transaction->tag_id)) {
                    $tag = $transaction->tag();
                    $keyToUse = $tag->value('name');
                    $colorToUse = (! empty($tag->value('color')) ? "#" . $tag->value('color') : $colorToUse);
                }
            }

            if (!array_key_exists($keyToUse, $transactionsChart)) {
                $transactionsChart[$keyToUse] = [
                    'color' => $colorToUse,
                    'amount' => 0
                ];
            }

            $transactionsChart[$keyToUse]['amount'] += $amount;
        }

        return $transactionsChart;
    }
}
