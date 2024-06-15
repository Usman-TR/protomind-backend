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
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
        }

        Keyword::insert($newKeywords);

        return Keyword::where('created_at', $currentTime)->get();

    }

    public function update(array $data): Collection
    {
        $currentTime = now();

        $idsToUpdate = collect($data['keywords'])->pluck('id')->filter();

        $protocolKeywords = Keyword::whereIn('id', $idsToUpdate)->get()->keyBy('id');

        foreach ($data['keywords'] as $keyword) {
            if (isset($keyword['id']) && isset($protocolKeywords[$keyword['id']])) {
                $protocolKeywords[$keyword['id']]->update([
                    'title' => $keyword['title'],
                    'phrase' => $keyword['phrase'],
                    'updated_at' => $currentTime,
                ]);
            }
        }

        return Keyword::orWhere('updated_at', $currentTime)->get();
    }
}
