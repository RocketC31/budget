<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Import;
use App\Models\Tag;
use App\Repositories\SpendingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ImportController extends Controller
{
    private SpendingRepository $spendingRepository;

    public function __construct(SpendingRepository $spendingRepository)
    {
        $this->spendingRepository = $spendingRepository;
    }

    public function index(): Response
    {
        return Inertia::render('Imports/Index')->with([
            'imports' => Import::ofSpace(session('space_id'))->latest()->get()
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Imports/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|max:255',
            'file' => 'required|max:200|mimes:csv,txt'
        ]);

        $path = $request->file('file')->store('imports');
        $pathParts = explode('/', $path);

        $import = Import::create([
            'space_id' => session('space_id'),
            'name' => $request->input('name'),
            'file' => end($pathParts)
        ]);

        return redirect()->route('imports.prepare', [ "import" => $import ]);
    }

    public function getPrepare(Import $import): Response
    {
        $this->authorize('modify', $import);

        $headers = [];

        $file = fopen(storage_path('app/imports/' . $import->file), 'r');

        // Detect delimiter
        $firstRowRaw = fgets($file);
        $delimiter = Helper::detectDelimiter($firstRowRaw);

        rewind($file); // Reset file pointer to make sure fgetcsv() starts at first line

        $firstRow = fgetcsv($file, 999, $delimiter);
        foreach ($firstRow as $column) {
            $headers[] = $column;
        }

        return Inertia::render('Imports/Prepare', compact('headers', 'import'));
    }

    public function postPrepare(Request $request, Import $import): RedirectResponse
    {
        $this->authorize('modify', $import);

        $request->validate([
            'column_happened_on' => 'required|integer',
            'column_description' => 'required|integer',
            'column_amount' => 'required|integer',
        ]);

        // Storing which columns in the CSV are used for the date, description and amount of a transaction
        // Setting status to 1 means the preparations (figuring out which columns should be used) are done
        $import->fill([
            'column_happened_on' => $request->input('column_happened_on'),
            'column_description' => $request->input('column_description'),
            'column_amount' => $request->input('column_amount'),
            'status' => 1
        ])->save();

        return redirect()->route('imports.complete', [ "import" => $import ]);
    }

    public function getComplete(Import $import): Response
    {
        $this->authorize('modify', $import);

        $tags = Tag::ofSpace(session('space_id'))->get();

        $file = fopen(storage_path('app/imports/' . $import->file), 'r');

        // Detect delimiter
        $rawHeaderRow = fgets($file);
        $delimiter = Helper::detectDelimiter($rawHeaderRow);

        $rows = [];

        while ($row = fgetcsv($file, 999, $delimiter)) {
            $rows[] = [
                'happened_on' => $row[$import->column_happened_on],
                'description' => $row[$import->column_description],
                'amount' => $row[$import->column_amount],
                'tag_id' => null,
            ];
        }

        return Inertia::render('Imports/Complete', compact('tags', 'rows', 'import'));
    }

    public function postComplete(Request $request, Import $import): RedirectResponse
    {
        $this->authorize('modify', $import);

        $request->validate([
            'date_format' => 'required', // TODO ADD IN
        ]);

        $date_format = $request->input('date_format');

        $errors = [];

        foreach ($request->input('rows') as $i => $row) {
            if (isset($row['selected']) && $row['selected'] == true) {
                $validator = Validator::make($row, [
                    'tag_id' => 'nullable|exists:tags,id', // TODO CHECK IF TAG BELONGS TO USER
                    'happened_on' => 'date|date_format:' . $date_format,
                    'description' => 'max:255',
                    'amount' => 'regex:/^\d*([\,\.]\d{2})?$/'
                ]);

                if ($validator->fails()) {
                    foreach ($validator->getMessageBag()->toArray() as $index => $message) {
                        $errors['rows.' . $i . '.' . $index] = $message;
                    }
                }
            }
        }

        if ($errors) {
            $request->flash();

            return redirect()->route('imports.complete', ['import' => $import->id])->withErrors($errors);
        }

        foreach ($request->input('rows') as $row) {
            if (isset($row['selected']) && $row['selected'] == true) {
                // TODO CHECK HOW THIS WORKS WITH 1k+ AMOUNTS
                $amount = str_replace(',', '.', $row['amount']);

                $this->spendingRepository->create(
                    session('space_id'),
                    $import->id,
                    null,
                    $row['tag_id'],
                    $row['happened_on'],
                    $row['description'],
                    $amount
                );
            }
        }

        $import->fill([
            'status' => 2
        ])->save();

        return redirect()->route('imports.index');
    }

    public function destroy(Request $request, Import $import)
    {
        if (!$import->spendings->count()) {
            $import->delete();
        }

        return redirect()->route('imports.index');
    }
}
