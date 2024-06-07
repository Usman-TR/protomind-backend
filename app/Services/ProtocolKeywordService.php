<?php

namespace App\Services;

use App\Models\ProtocolKeyword;
use Illuminate\Support\Collection;


class ProtocolKeywordService
{
    public function create(array $data, int $protocolId): Collection
    {
        $currentTime = now();
        $newKeywords = [];

        foreach ($data['keywords'] as $keyword) {
            $newKeywords[] = [
                'protocol_id' => $protocolId,
                'title' => $keyword['title'],
                'phrase' => $keyword['phrase'],
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
        }

        ProtocolKeyword::insert($newKeywords);

        return ProtocolKeyword::where('created_at', $currentTime)
            ->orWhere('updated_at', $currentTime)
            ->get();

    }

    public function update(array $data): Collection
    {
        $currentTime = now();

        $idsToUpdate = collect($data['keywords'])->pluck('id')->filter();

        $protocolKeywords = ProtocolKeyword::whereIn('id', $idsToUpdate)->get()->keyBy('id');

        foreach ($data['keywords'] as $keyword) {
            if (isset($keyword['id']) && isset($protocolKeywords[$keyword['id']])) {
                $protocolKeywords[$keyword['id']]->update([
                    'title' => $keyword['title'],
                    'phrase' => $keyword['phrase'],
                    'updated_at' => $currentTime,
                ]);
            }
        }

        return ProtocolKeyword::orWhere('updated_at', $currentTime)->get();
    }
}
