<?php

namespace App\Enums;

enum ProtocolStageEnum: string
{
    case NO_VIDEO = 'no_video';
    case VIDEO_PROCESS = 'video_process';
    case ERROR_VIDEO_PROCESS = 'error_video_process';
    case SUCCESS_VIDEO_PROCESS = 'success_video_process';
    case FINAL = 'final';
}
