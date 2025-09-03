<?php

namespace App\Domain\Enums;

enum ExportStatus: string
{
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case DONE = 'done';
    case FAILED = 'failed';
}
