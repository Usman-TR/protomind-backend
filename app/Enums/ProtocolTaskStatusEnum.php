<?php

namespace App\Enums;

enum ProtocolTaskStatusEnum: string
{
    case SUCCESS = 'success';
    case EXPIRED = 'expired';
    case PROCESS = 'process';
}
