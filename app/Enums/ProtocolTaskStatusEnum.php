<?php

namespace App\Enums;

enum ProtocolTaskStatusEnum: string
{
    case SUCCESS = 'success';
    case EXPIRED = 'expired';
    case PROCESS = 'process';

    public function label(): string
    {
        return match($this) {
            self::PROCESS => __('protocol_members.Process'),
            self::SUCCESS => __('protocol_members.Success'),
            self::EXPIRED => __('protocol_members.Expired'),
        };
    }
}
