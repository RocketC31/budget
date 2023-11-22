<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Space;
use App\Models\Tag;
use App\Models\Transaction;
use App\Repositories\ConversionRateRepository;
use App\Repositories\CurrencyRepository;
use App\Repositories\RecurringRepository;
use App\Repositories\TagRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\Exception\NotFoundException;

class TransactionController extends Controller
{
    private TransactionRepository $repository;
    private CurrencyRepository $currencyRepository;
    private RecurringRepository $recurringRepository;
    private TagRepository $tagRepository;
    private ConversionRateRepository $conversionRateRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        CurrencyRepository $currencyRepository,
        RecurringRepository $recurringRepository,
        TagRepository $tagRepository,
        ConversionRateRepository $conversionRateRepository
    ) {
        $this->repository = $transactionRepository;
        $this->currencyRepository = $currencyRepository;
        $this->recurringRepository = $recurringRepository;
        $this->tagRepository = $tagRepository;
        $this->conversionRateRepository = $conversionRateRepository;
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
        $totalSpentInMont = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->type === Transaction::TYPE_SPENDING) {
                $totalSpentInMont += $transaction->amount;
            }
        }

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
            'filterBy' => $filterBy,
            'totalSpent' => Helper::formatNumber($totalSpentInMont / 100),
            'month' => $currentDate->format("n"),
            'year' => $year,
            'tags' => Tag::ofSpace(session('space_id'))->orderBy('name')->get(),
            'dataType' => $this->getDataType($request)
        ]);
    }

    public function edit(Transaction $transaction, Request $request): Response
    {
        $this->authorize('edit', $transaction);
        $transaction->load('attachments');
        $tags = Tag::ofSpace(session('space_id'))->orderBy('name')->get();
        $dataType = $this->getDataType($request);
        return Inertia::render(
            'Transactions/Edit',
            compact(
                'tags',
                'transaction',
                'dataType'
            )
        );
    }

    public function create(Request $request): Response
    {
        $tags = [];

        foreach (Tag::ofSpace(session('space_id'))->orderBy('name')->get() as $tag) {
            $tags[] = [
                'key' => $tag->id,
                'label' => '<div class="row"><div class="row__column row__column--compact row__column--middle mr-1"><div style="width: 15px; height: 15px; border-radius: 2px; background: #' . $tag->color . ';"></div></div><div class="row__column row__column--middle">' . $tag->name . '</div></div>' // phpcs:ignore
            ];
        }

        return Inertia::render('Transactions/Create', [
            'dataType' => $this->getDataType($request),
            'tags' => $tags,
            'currencies' => $this->currencyRepository->getIfConversionRatePresent(),
            'defaultTransactionType' => Auth::user()->default_transaction_type,
            'firstDayOfWeek' => Auth::user()->first_day_of_week,
            'defaultCurrencyId' => Space::find(session('space_id'))->currency_id,
            'recurringsIntervals' => $this->recurringRepository->getSupportedIntervals()
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $request->validate($this->repository->getValidationRules(true));

        $amount = Helper::rawNumberToInteger($request->input('amount'));

        $this->repository->update($transaction->id, [
            'tag_id' => $request->input('tag_id'),
            'happened_on' => $request->input('date'),
            'description' => $request->input('description'),
            'amount' => $amount
        ]);

        return redirect()->route('transactions.index', [ 'dataType' => $this->getDataType($request)]);
    }

    public function store(Request $request)
    {
        $request->validate($this->repository->getValidationRules());

        $amount = Helper::rawNumberToInteger($request->input('amount'));

        // Convert amount if a different currency was selected
        if ($request->has('currency_id') && $request->currency_id !== Space::find(session('space_id'))->currency_id) {
            $amount = $this->conversionRateRepository->convert(
                $request->currency_id,
                Space::find(session('space_id'))->currency_id,
                $amount
            );
        }

        $this->repository->create(
            session('space_id'),
            $request->input('type'),
            null,
            null,
            $request->input('tag'),
            $request->input('date'),
            $request->input('description'),
            $amount
        );

        return redirect()->route('transactions.index', [ 'dataType' => $this->getDataType($request) ]);
    }

    public function restore($id): RedirectResponse
    {
        $transaction = Transaction::withTrashed()->find($id);

        if (!$transaction) {
            throw new NotFoundException();
        }

        $this->authorize('restore', $transaction);

        $transaction->restore();

        return back();
    }

    public function trash(): Response
    {
        return Inertia::render('Transactions/Trash', [
            'transactions' => $this->repository->getTransactionsRemoved()
        ]);
    }

    public function destroy($id, Request $request)
    {
        $transaction = Transaction::find($id);
        $this->authorize('delete', $transaction);

        $transaction->delete();
        return redirect()->route('transactions.index', [ "dataType" => $this->getDataType($request)]);
    }

    public function purge($id): RedirectResponse
    {
        $transaction = Transaction::withTrashed()->find($id);

        if (!$transaction) {
            throw new NotFoundException();
        }

        $this->authorize('delete', $transaction);

        $transaction->forceDelete();

        return back();
    }

    public function purgeAll(): RedirectResponse
    {
        //NO need check because if it's in trash, check already ok. And it's linked to space_id
        Transaction::ofSpace(session('space_id'))->onlyTrashed()->forceDelete();

        return back();
    }

    private function getTransactionsForChart(Collection $transactions): array
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
            if ($transaction->type === Transaction::TYPE_SPENDING) {
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

    private function getDataType(Request $request): string
    {
        if (
            $request->input('dataType')
            && in_array(
                $request->input('dataType'),
                [
                    'resume',
                    'list',
                    'chart',
                    'tags'
                ]
            )
        ) {
            return $request->input('dataType');
        }
        return "resume";
    }
}
