<?php

namespace App\Services;

use App\Models\Keyword;
use App\Models\ProtocolKeyword;
use Illuminate\Support\Collection;


class KeywordService
{
    public function create(array $data): Collection
    {
        $currentTime = now();
        $newKeywords = [];

        foreach ($data['keywords'] as $keyword) {
            $newKeywords[] = [
                'title' => $keyword['title'],
                'phrase' => $keyword['phrase'],
                'user_id' => auth()->id(),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
        }

        Keyword::insert($newKeywords);

        return Keyword::where('created_at', $currentTime)->get();
    }
}
