<?php

namespace App\Repositories;

use App\Models\Attachment;
use Exception;

class AttachmentRepository
{
    public function getById(int $id): ?Attachment
    {
        return Attachment::find($id);
    }

    public function create(int $transactionId, string $filePath): Attachment
    {
        return Attachment::create([
            'transaction_id' => $transactionId,
            'file_path' => $filePath
        ]);
    }

    public function delete(int $id): void
    {
        $attachment = $this->getById($id);

        if (!$attachment) {
            throw new Exception('Could not find attachment with ID ' . $id);
        }

        $attachment->delete();
    }
}
