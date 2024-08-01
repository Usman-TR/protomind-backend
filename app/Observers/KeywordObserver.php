<?php

namespace App\Observers;

use App\Models\Keyword;
use App\Services\KeywordService;

class KeywordObserver
{
    public function __construct(
        private readonly KeywordService $keywordService
    )
    {
    }

    public function updated(Keyword $keyword): void
    {
        $this->keywordService->updateUserProtocols($keyword);
    }
}
