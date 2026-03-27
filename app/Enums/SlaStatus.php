<?php

namespace App\Enums;

/**
 * Computed SLA status (not stored in DB). Used for display.
 */
enum SlaStatus: string
{
    case None = 'none';
    case OnTrack = 'on_track';
    case AtRisk = 'at_risk';
    case Breached = 'breached';
    case Met = 'met';
}
