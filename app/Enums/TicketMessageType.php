<?php

namespace App\Enums;

enum TicketMessageType: string
{
    case Message = 'message';
    case System = 'system';
    case InternalNote = 'internal_note';
}
