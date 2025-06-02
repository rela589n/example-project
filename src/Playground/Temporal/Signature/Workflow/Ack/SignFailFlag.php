<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow\Ack;

enum SignFailFlag: string
{
    case NONE = 'none';
    case ACK_TIMEOUT = 'timeout';
    case ACK_SERVER_ERROR = 'server_error';
}
