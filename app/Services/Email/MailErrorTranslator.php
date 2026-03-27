<?php

namespace App\Services\Email;

class MailErrorTranslator
{
    /**
     * Traduit une erreur technique IMAP/SMTP en message clair et actionable.
     */
    public static function translate(string $error, string $protocol = 'IMAP', int $port = 0): string
    {
        $lower = mb_strtolower($error);

        // ── Connexion refusée / impossible ───────────────────────────
        if (str_contains($lower, 'connection refused')) {
            return "Connexion refusée par le serveur. Vérifiez que l'hôte {$protocol} et le port ({$port}) sont corrects. "
                .'Ports courants : IMAP = 993 (SSL) ou 143 (TLS), SMTP = 587 (TLS) ou 465 (SSL).';
        }

        if (str_contains($lower, 'connection timed out') || str_contains($lower, 'timed out') || str_contains($lower, 'timeout')) {
            return 'Le serveur ne répond pas (délai dépassé). Causes possibles : '
                ."le port {$port} est incorrect, un pare-feu bloque la connexion, ou le serveur est hors ligne. "
                .'Ports recommandés : IMAP = 993 (SSL), SMTP = 587 (TLS).';
        }

        if (str_contains($lower, 'connection failed') || str_contains($lower, 'could not connect')) {
            return "Impossible de se connecter au serveur {$protocol}. Vérifiez l'hôte, le port ({$port}) et le type de chiffrement. "
                .'Astuce : pour Gmail, utilisez imap.gmail.com:993 (SSL) et smtp.gmail.com:587 (TLS).';
        }

        if (str_contains($lower, 'getaddrinfo') || str_contains($lower, 'name or service not known') || str_contains($lower, 'no such host')) {
            return "Le serveur « {$protocol} » est introuvable. Vérifiez l'adresse de l'hôte (ex : imap.gmail.com, smtp.outlook.com). "
                .'Une faute de frappe dans le nom du serveur est la cause la plus fréquente.';
        }

        // ── Authentification ─────────────────────────────────────────
        if (str_contains($lower, 'authentication failed') || str_contains($lower, 'login failed')
            || str_contains($lower, 'invalid credentials') || str_contains($lower, 'authenticationfailed')
            || str_contains($lower, 'username and password not accepted') || str_contains($lower, 'auth') && str_contains($lower, 'fail')) {
            return "Identifiant ou mot de passe incorrect. Si vous utilisez Gmail ou Outlook, vous devez utiliser un « mot de passe d'application » "
                ."et non votre mot de passe habituel. Vérifiez aussi que l'accès {$protocol} est activé dans les paramètres de votre fournisseur email.";
        }

        if (str_contains($lower, 'too many login') || str_contains($lower, 'rate limit') || str_contains($lower, 'try again later')) {
            return "Trop de tentatives de connexion. Le serveur bloque temporairement l'accès. Attendez quelques minutes avant de réessayer.";
        }

        // ── SSL/TLS ──────────────────────────────────────────────────
        if (str_contains($lower, 'ssl') || str_contains($lower, 'tls') || str_contains($lower, 'certificate')
            || str_contains($lower, 'handshake') || str_contains($lower, 'crypto')) {
            return 'Erreur de chiffrement SSL/TLS. Le type de chiffrement ne correspond pas au port utilisé. '
                .'Combinaisons correctes : port 993/465 → SSL, port 143/587 → TLS, port 25 → Aucun.';
        }

        // ── Spam / Rejet ─────────────────────────────────────────────
        if (str_contains($lower, 'spam') || str_contains($lower, '550')) {
            return "L'email a été rejeté comme spam par le serveur destinataire. Vérifiez que les enregistrements DNS "
                .'de votre domaine (SPF, DKIM, DMARC) sont correctement configurés. Contactez votre fournisseur email pour obtenir ces informations.';
        }

        if (str_contains($lower, 'relay') || str_contains($lower, 'not permitted') || str_contains($lower, '553') || str_contains($lower, '554')) {
            return "Le serveur SMTP refuse de relayer l'email. L'adresse d'expédition n'est probablement pas autorisée sur ce serveur. "
                ."Vérifiez que l'adresse email configurée correspond au compte SMTP utilisé.";
        }

        if (str_contains($lower, 'recipient') || str_contains($lower, '551') || str_contains($lower, '552')) {
            return "L'adresse du destinataire a été refusée par le serveur. Vérifiez que l'adresse email du destinataire est correcte.";
        }

        // ── Dossier IMAP ─────────────────────────────────────────────
        if (str_contains($lower, 'folder') || str_contains($lower, 'mailbox not found') || str_contains($lower, 'no such mailbox')) {
            return 'Le dossier IMAP spécifié est introuvable. Vérifiez le nom du dossier (par défaut : INBOX). '
                .'Attention aux majuscules/minuscules.';
        }

        // ── Quota / Espace ───────────────────────────────────────────
        if (str_contains($lower, 'quota') || str_contains($lower, 'storage') || str_contains($lower, 'full')) {
            return "La boîte email est pleine. Libérez de l'espace sur le compte email avant de réessayer.";
        }

        // ── Erreur générique avec code ───────────────────────────────
        if (preg_match('/(\d{3})\s/', $error, $m)) {
            $code = (int) $m[1];

            return match (true) {
                $code >= 400 && $code < 500 => "Le serveur a refusé la commande (erreur {$code}). Vérifiez vos identifiants et les paramètres de connexion.",
                $code >= 500 => "Erreur permanente du serveur (erreur {$code}). Contactez votre fournisseur email si le problème persiste.",
                default => "Erreur serveur ({$code}) : {$error}",
            };
        }

        // ── Fallback ─────────────────────────────────────────────────
        return "Erreur {$protocol} : {$error}";
    }
}
