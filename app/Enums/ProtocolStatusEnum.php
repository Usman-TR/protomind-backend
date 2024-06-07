<?php

namespace App\Enums;

enum ProtocolStatusEnum: string
{
    case PROCESS = 'process';
    case SUCCESS = 'success';

    public function label(): string
    {
        return match($this) {
            self::PROCESS => __('protocols.Process'),
            self::SUCCESS => __('protocols.Success'),
        };
    }
}
