<?php

namespace App;

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
use Illuminate\Support\Facades\Log;

trait FindTagFromAI
{
    public function findTagFromIA(string $description, ?array $tags = null, ?int $tagOnSimilar = null): ?int
    {
        if (empty($tags) || !config('bank_sync.ai_api')) {
            return null;
        }
        $nbTags = count($tags);

        $prompt = "I have {$nbTags} categories: ";
        $last = array_key_last($tags);
        $tagSimilarLabel = "";
        foreach ($tags as $key => $tag) {
            $prompt .= $tag['name'] . ' with ID ' . '\'' . $tag['id'] . '\'';
            $prompt .= ($last === $key) ? '.' : ', ';
            if ($tagOnSimilar == $tag['id']) {
                $tagSimilarLabel = $tag['name'];
            }
        }

        $prompt .= " Where do you categorize this transaction with this label: {$description}.";
        $prompt .= " Give me only the ID of the category or 0 if you think nothing is good.";
        $prompt .= " This categories can to be in an other language. Translate it before done your choice";
        $prompt .= " Don't give any other text explanation.";
        if ($tagOnSimilar) {
            $prompt .= " For information, a previous similar transaction was assigned to the category {$tagSimilarLabel} with ID {$tagOnSimilar}.";
            $prompt .= " If you wouldn't have categorized it in the same place trust this similar category instead.";
        }
        try {
            $client = new Client(config('bank_sync.ai_api'));
            $response = $client->geminiPro()->generateContent(new TextPart($prompt))->text();
            return (int)$response > 0 ? (int)$response : null;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
        return null;
    }
}
