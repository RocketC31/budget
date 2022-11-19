<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Repositories\TagRepository;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\Exception\NotFoundException;

class TagController extends Controller
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function index(): Response
    {
        $tags = Tag::ofSpace(session('space_id'))
            ->with('spendings')
            ->with('earnings')
            ->with('budgets')
            ->latest()->get();
        return Inertia::render('Tags/Index', ['tags' => $tags]);
    }

    public function create(): Response
    {
        return Inertia::render('Tags/Create');
    }

    public function trash(): Response
    {
        return Inertia::render('Tags/Trash', [
            'tags' => Tag::ofSpace(session("space_id"))->onlyTrashed()->get()
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->tagRepository->getValidationRules());

        $this->tagRepository->create(session('space_id'), $request->input('name'), $request->input('color'));

        return redirect()->route('tags.index');
    }

    public function edit(Tag $tag): Response
    {
        $this->authorize('edit', $tag);

        return Inertia::render('Tags/Edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $this->authorize('update', $tag);

        // phpcs:ignore
        $request->validate(array_slice($this->tagRepository->getValidationRules(), 0, 1, true)); // Get rid of last entry in $validationRules as it's not required for updating

        $this->tagRepository->update($tag->id, [
            'name' => $request->input('name'),
            'color' => $request->input('color')
        ]);

        return redirect()->route('tags.index');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $this->authorize('delete', $tag);

        $tag->delete();

        return redirect()->route('tags.index');
    }

    public function restore($id): RedirectResponse
    {
        $tag = Tag::ofSpace(session('space_id'))->onlyTrashed()->find($id);
        if (!$tag) {
            throw new NotFoundException();
        }

        $tag->restore();

        return back();
    }

    public function purge($id): RedirectResponse
    {
        $tag = Tag::onlyTrashed()->find($id);
        if (!$tag) {
            throw new NotFoundException();
        }

        $this->authorize('delete', $tag);

        //Unlink spendings
        Transaction::withTrashed()->where('tag_id', "=", $tag->id)->update(['tag_id' => null]);

        //Remove budget because without tag it's not usefull (definitively delete)
        Budget::withTrashed()->where('tag_id', "=", $tag->id)->forceDelete();

        $tag->forceDelete();

        return back();
    }

    public function purgeAll(): RedirectResponse
    {
        $tagsIds = Tag::ofSpace(session('space_id'))->onlyTrashed()->pluck('id')->toArray();

        //Unlink for recurring who will be purged
        Transaction::withTrashed()->whereIn('tag_id', $tagsIds)->update(['tag_id' => null]);
        Budget::withTrashed()->whereIn('tag_id', $tagsIds)->forceDelete();

        //Then remove recurrings
        Tag::ofSpace(session('space_id'))->onlyTrashed()->forceDelete();

        return back();
    }
}
