<?php

namespace App\Enums;

enum ProtocolStatusEnum: string
{
    case PROCESS = 'process';
    case SUCCESS = 'success';

    case NO_VIDEO = 'no_video';

}
