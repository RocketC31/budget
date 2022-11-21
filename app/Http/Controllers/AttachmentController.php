<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentController extends Controller
{
    private AttachmentRepository $attachmentRepository;

    public function __construct(AttachmentRepository $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'transaction_type' => 'required|in:earning,spending',
            'transaction_id' => 'required',
            'file' => 'required|mimes:jpeg,png,pdf'
        ]);

        $fileName = Str::random(20) . '.' . $request->file('file')->extension();
        $filePath = $request->file('file')->storeAs('attachments', $fileName);

        $transactionId = $request->transaction_id;

        $this->attachmentRepository->create($transactionId, $filePath);

        return redirect()->intended('/transaction/' . $transactionId);
    }

    public function download(Request $request, Attachment $attachment): ?BinaryFileResponse
    {
        $user = Auth::user();

        if (!$user->spaces->contains($attachment->transaction->space_id)) {
            abort(403);
        }

        if ($attachment->file_type !== 'pdf') {
            return null;
        }

        return response()->download(storage_path() . '/app/' . $attachment->file_path);
    }

    public function delete(Request $request, string $id)
    {
        $attachment = $this->attachmentRepository->getById($id);

        if (!$attachment) {
            abort(404);
        }

        // Memorize some details before we delete it
        $transactionId = $attachment->transaction_id;

        $this->attachmentRepository->delete($id);

        return redirect('/transaction/' . $transactionId);
    }
}
