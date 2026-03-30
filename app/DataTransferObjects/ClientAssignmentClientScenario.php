<?php

namespace App\DataTransferObjects;

/**
 * Scénario du message **client** (e-mail + extrait in-app), pas l’audit interne.
 */
enum ClientAssignmentClientScenario: string
{
    /** Une personne identifiée (nom affiché). */
    case PersonNamed = 'person_named';

    /** Formulation sans nom d’agent. */
    case PersonAnonymous = 'person_anonymous';

    /** Groupe / file (ticket_groups). */
    case Group = 'group';

    /** Fonction métier (pool). */
    case FunctionTeam = 'function';

    /** Plusieurs assignés humains sans cible unique claire. */
    case MultipleMembers = 'multiple';

    /** Mise à jour d’affectation sans détail exploitable. */
    case Generic = 'generic';
}
