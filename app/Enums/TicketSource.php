<?php

namespace App\Enums;

enum TicketSource: string
{
    case Platform = 'platform';
    case Form = 'form';
    case Email = 'email';
    case Api = 'api';
}
