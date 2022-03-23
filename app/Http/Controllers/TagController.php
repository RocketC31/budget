<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Repositories\TagRepository;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function index(): Response
    {
        $tags = Tag::ofSpace(session('space_id'))->with('spendings')->with('budgets')->latest()->get();
        return Inertia::render('Tags/Index', ['tags' => $tags]);
    }

    public function create(): Response
    {
        return Inertia::render('Tags/Create');
    }

    public function store(Request $request)
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

    public function update(Request $request, Tag $tag)
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

    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);

        if (!$tag->spendings->count()) {
            $tag->delete();
        }

        return redirect()->route('tags.index');
    }
}
