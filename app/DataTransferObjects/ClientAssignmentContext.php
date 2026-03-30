<?php

namespace App\DataTransferObjects;

/**
 * Contexte d’une action d’assignation pour le mail client (événement métier).
 * L’audit détaillé (timeline) est porté par les TicketMessage ; ce DTO ne le duplique pas.
 */
readonly class ClientAssignmentContext
{
    public function __construct(
        public ClientAssignmentClientScenario $clientScenario,
        public ?string $targetDisplayName = null,
    ) {}

    public static function generic(): self
    {
        return new self(ClientAssignmentClientScenario::Generic);
    }
}
