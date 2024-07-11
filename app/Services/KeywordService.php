<?php

namespace App\Services;

use App\Models\Keyword;
use Illuminate\Support\Collection;


class KeywordService
{
    public function create(array $data): Collection
    {
        $currentTime = now();
        $newKeywords = [];

        foreach ($data['keywords'] as $keyword) {
            $newKeywords[] = [
                'title' => str_replace("\n", "", $keyword['title']),
                'phrase' => str_replace("\n", "", $keyword['phrase']),
                'user_id' => auth()->id(),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
        }

        Keyword::insert($newKeywords);

        return Keyword::where('created_at', $currentTime)->get();
    }
}
