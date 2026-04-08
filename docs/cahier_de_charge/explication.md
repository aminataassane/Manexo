Oui. Voilà **comment j’ai compris MANEXO aujourd’hui**, surtout sur la partie ticketing, messages, emails, rôles et entreprises, **sans encore parler des corrections**.

### 1. MANEXO n’est pas juste un outil de tickets

Je comprends MANEXO comme une **plateforme multi-entreprises de gestion du support**, où chaque entreprise a :

* ses propres membres,
* ses propres rôles,
* ses propres tickets,
* ses propres formulaires,
* ses propres paramètres,
* et sa propre personnalisation visuelle.
  L’isolement entre entreprises est un principe de base du produit. 

### 2. Le cœur du produit, c’est le ticket

Le ticket est l’unité centrale du système.
Autour du ticket, il y a :

* le demandeur,
* les agents,
* les assignations,
* les statuts,
* les fichiers,
* les notifications,
* l’historique,
* et les discussions.
  Autrement dit, tout converge vers le ticket comme “source de vérité”.

### 3. Il y a plusieurs types de personnes dans le système

Il faut combiner **deux axes** (ce n’est pas une seule étiquette « client / interne ») :

* **Statut du compte** (`User`, ex. **`guest`** ou compte « normal ») : un **guest** est souvent créé ou réutilisé pour un demandeur formulaire public ou un expéditeur mail résolu comme invité ; il n’est **pas assignable** aux tickets même s’il est créateur.
* **Rôle dans l’organisation** (liaison org ↔ utilisateur) : **`owner`**, **`admin`**, **`agent`**, **`member`**. Le **member** est un rôle org (collègue, partenaire, portail « client », etc.) ; pour les **notifications mail vs in-app**, le « staff » côté code correspond surtout à **owner / admin / agent** (non-guest), pas au **member**.

En plus :

* **owner / admin / agent** : profils qui portent en général le travail support dans l’outil.
* **member** : membre de l’entreprise sur le papier ; peut être assignable s’il n’est **pas** guest ; les règles de notification ne le traitent pas comme le staff pour tous les événements.
* **guest / externe** : compte souvent créé via formulaire public ou mail ; peut avoir une adhésion à l’org (souvent rôle pivot member) pour le routage, mais reste **guest** au sens assignation.
* **utilisateur multi-entreprises** : une même personne peut appartenir à plusieurs entreprises, avec une **entreprise active** à la fois (session / contexte).

### 4. Les rôles ne sont pas seulement décoratifs

Le **rôle** dans l’entreprise active est associé à des **permissions** configurables (matrice par organisation ; le **propriétaire** a tous les droits et ne peut pas se les retirer via la grille).

* Les permissions déterminent **ce qu’on peut faire** : créer ou éditer des tickets, assigner, voir ou écrire les **notes internes**, gérer l’équipe, les paramètres, etc.
* **Liste des tickets** : en principe **tous les membres** de l’org peuvent voir la liste (le filtrage métier « seulement mes tickets » est une couche produit / UX, pas le seul garde-fou actuel).
* **Page discussion d’un ticket** : l’accès est large au sein de l’org (créateur, assignés, participants, **et** tout membre de la même organisation pour l’ouverture du ticket / fil), sous réserve du contexte org et des droits globaux.
* **Notes internes** : la visibilité ne s’appuie pas sur le mot « client » mais sur la **permission** « voir / écrire les notes internes » ; un **member** qui l’a pourrait les voir, un **agent** qui ne l’a pas ne les verrait pas.

Owner, admin, agent et member ne sont pas équivalents ; tout dépend de l’**entreprise active** et des **permissions** accordées à chaque rôle (sauf owner).

### 5. Tickets et messages : comment ça se passe sur la plateforme

**Une timeline unifiée.** La discussion du ticket est portée par des enregistrements **`TicketMessage`** sur **un seul fil** (timeline), pas par plusieurs « salons » parallèles dans le code. On distingue surtout des **types** de messages :

* **message public** (échange visible côté conversation « normale ») ;
* **note interne** (filtrée pour les utilisateurs sans permission adéquate) ;
* **message système** (événements / historique automatisé).

Il n’existe pas aujourd’hui, dans ce modèle, une **discussion de groupe agents** séparée du fil ticket : si le produit parle de « groupe », c’est plutôt **équipe / groupe de tickets / participants**, pas un second fil de chat isolé.

**Contenu initial vs messages.** Le ticket a des champs propres (**sujet**, **description**, pièces jointes au niveau ticket, **champs personnalisés**). Lors d’une création **formulaire public**, le texte principal va souvent dans **description** (et métadonnées) ; ce n’est **pas** automatiquement le premier **`TicketMessage`**. Les échanges suivants (app, mail, etc.) s’ajoutent comme **messages** dans la même timeline.

**Message depuis l’application.** L’utilisateur est **connecté** ; le message est saisi dans le composer (texte + PJ). L’auteur est le **`user_id`** courant. Les **notes internes** ne notifient que les rôles **owner / admin / agent** de l’org ; les messages publics notifient créateur, assignés et participants (hors auteur), avec accès discussion vérifié.

**Message depuis l’e-mail entrant.** Pas de session web : l’expéditeur est identifié par l’adresse **From** (résolution vers un utilisateur membre de l’org ou création / réutilisation d’un **guest**). Le corps du mail devient le **`body`** du **`TicketMessage`** ; l’identifiant **`Message-ID`** peut être stocké pour le fil RFC. Si l’expéditeur n’avait pas accès, il peut être ajouté en **participant** pour la suite du fil. Les autres acteurs du ticket sont notifiés comme pour un message app (selon les règles mail / in-app).

**E-mail sortant (réponses).** Les notifications peuvent utiliser **Reply-To** en **plus-addressing** (`local+PUBLIC_ID@domaine`), des en-têtes **Message-ID / In-Reply-To / References**, et l’SMTP de la boîte org si configuré — pour que les réponses du client retombent sur le **même ticket**.

**Création du ticket et `created_by`.** Quel que soit le canal (**platform**, **form**, **email**, **api**), le ticket a un **créateur technique** (`created_by` = un **`User`**). Le canal est mémorisé dans **`source`**.

### 6. Email et plateforme doivent alimenter le même ticket

J’ai compris que la logique actuelle va déjà vers une **discussion unifiée** :

* un ticket peut être créé depuis la plateforme,
* depuis un email,
* depuis un formulaire,
* ou via API,
* mais tout doit converger dans le même ticket.
  Le système email sait déjà router les réponses vers le bon ticket grâce au `Reply-To`, au `Message-ID`, à `In-Reply-To`, `References` et au `public_id` du ticket.

### 7. Le ticket a une source

J’ai compris que chaque ticket a une **source claire** :

* `platform`
* `form`
* `email`
* `api`
  Donc le système sait déjà d’où vient le ticket, même si l’interface ne montre pas encore toujours cette origine de façon assez lisible.

### 8. Le comportement email/notif dépend du type d’utilisateur

C’est un point important dans ma compréhension actuelle :

* les **owner / admin / agent** reçoivent surtout des notifications **dans l’app**
* les **externes / guests** reçoivent plus facilement **app + email**
* les **members** semblent être dans une zone intermédiaire selon la notification concernée

Donc la plateforme essaie déjà de distinguer :

* les gens qui travaillent dans l’outil,
* et les gens pour qui l’email reste un vrai canal de communication. 

### 9. La création de ticket ne suit pas exactement le même chemin selon le canal

J’ai compris qu’il y a déjà une différence de comportement entre :

* **ticket créé depuis l’app**
* **ticket créé depuis email**
* **ticket créé depuis formulaire public**

Par exemple, le flux formulaire public crée bien un ticket avec la source `form`, mais ne déclenche pas encore le même accusé de réception ou les mêmes notifications que les autres chemins. Donc fonctionnellement ça marche, mais le comportement produit n’est pas encore complètement harmonisé entre tous les canaux. 

### 10. Le ticketing est déjà assez avancé

Je comprends aussi que le socle technique ticketing est déjà bien en place :

* multi-canal,
* email entrant,
* email sortant,
* routing,
* pièces jointes,
* timeline,
* statuts,
* SLA,
* assignation,
* notes internes,
* permissions.
  Mais il reste encore des écarts sur l’UX, les badges, certaines notifications client, la lisibilité de la timeline, et plusieurs règles produit fines. 

### 11. La logique du produit repose sur des séparations nettes

Voilà la logique que j’ai comprise comme “colonne vertébrale” de MANEXO :

* une entreprise ne voit pas les données d’une autre,
* ce qui est **interne** (notes) est surtout caché par les **permissions**, pas par une seule étiquette « client »,
* un ticket appartient à une seule entreprise,
* la **timeline** du ticket porte **plusieurs types** de messages dans le **même** fil,
* un même ticket peut vivre via plusieurs canaux,
* le tout reste **unifié** autour du ticket et de ses **`TicketMessage`** (en complément des champs « fiche ticket » comme la description).

### 12. Ce que je pense être ton vrai besoin maintenant

Je pense que ce que tu veux, ce n’est pas qu’on t’explique seulement “ce qui existe”.
Tu veux qu’on arrive à une organisation plus propre de tout cela pour éviter :

* les mélanges entre interne et externe,
* les mauvaises notifications,
* les incohérences entre email et plateforme,
* les mauvaises assignations,
* et les incompréhensions dans l’interface.

### En résumé très simple

Voilà comment j’ai compris la plateforme :

**MANEXO est une plateforme multi-entreprises de support où le ticket est l’objet central, les rôles et permissions (réglables par org) déterminent les droits, la discussion est une timeline unifiée de messages typés (public, interne, système), et les canaux plateforme / email / formulaire / API convergent vers un même ticket — la séparation « interne / externe » repose surtout sur les permissions et le type de message, tout en sachant que la visibilité au sein de l’org est large pour les membres.**

