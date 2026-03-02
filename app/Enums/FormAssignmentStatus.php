<?php

namespace App\Enums;

enum FormAssignmentStatus: string
{
    case Pending = 'pending';
    case Submitted = 'submitted';
    case Overdue = 'overdue';
    case Expired = 'expired';
}
