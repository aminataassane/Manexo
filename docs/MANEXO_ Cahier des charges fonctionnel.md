### **Cahier des charges fonctionnel**

**Projet :** Application de gestion de tickets (Helpdesk) multi-entreprises  
**Version :** 1.0  
**Date :** 04/02/2026  
**Auteur :** Aminata Assane ndiaye

---

## **1\. Présentation générale**

### **1.1 Objectif du document**

Ce cahier des charges fonctionnel décrit l’ensemble des besoins, fonctionnalités et règles de fonctionnement de l’application de gestion de tickets. Il servira de référence pour la conception, le développement, les tests et la validation de la solution.

### **1.2 Contexte**

L’application vise à centraliser les demandes d’assistance (tickets) afin d’améliorer l’organisation du support, la traçabilité, la collaboration entre agents, et la satisfaction des utilisateurs. Elle intègre également un module de formulaires dynamiques (Form Builder) et prend en charge un fonctionnement multi-entreprises.

### **1.3 Objectifs de l’application**

* Centraliser et structurer les demandes sous forme de tickets  
* Garantir un suivi complet (statuts, historique, pièces jointes)  
* Faciliter le travail des agents (assignation, filtres, collaboration)  
* Permettre la création de formulaires personnalisés (drag & drop)  
* Gérer plusieurs entreprises avec isolation des données  
* Permettre une personnalisation visuelle (couleur principale) par entreprise

### **1.4 Définition des termes**

* **Ticket :** demande d’assistance créée par un utilisateur.  
* **Entreprise (Organisation) :** entité utilisant la plateforme, avec ses membres.  
* **Membre :** utilisateur appartenant à une entreprise.  
* **Agent :** membre chargé du traitement des tickets.  
* **Administrateur :** membre ayant des droits de configuration.  
* **Form Builder :** outil de création de formulaires dynamiques.

## **2 — Périmètre du projet**

### **2.1 Fonctionnalités incluses (In-scope)**

Le périmètre fonctionnel de l’application inclut les éléments suivants :

* Gestion multi-entreprises (création, sélection et isolation des entreprises)  
* Gestion des utilisateurs et des membres par entreprise  
* Gestion des rôles par entreprise (propriétaire, administrateur, agent, membre)  
* Création, suivi et gestion des tickets de support  
* Système de discussion associé aux tickets

  * discussion privée (utilisateur ↔ agent)  
  * discussion de groupe (agents)  
  * notes internes

* Gestion des catégories et priorités de tickets  
* Upload et gestion des pièces jointes  
* Module Form Builder avec création de formulaires dynamiques (drag & drop)  
* Visibilité des formulaires (public, privé, ciblé)  
* Personnalisation de l’interface par entreprise (couleur principale)  
* Notifications liées aux actions sur les tickets  
* Statistiques et tableaux de bord par entreprise

### **2.2 Fonctionnalités exclues (Out-of-scope)**

Les fonctionnalités suivantes ne sont pas couvertes par ce projet :

* Support téléphonique direct  
* Application mobile native  
* Système de facturation ou de paiement  
* Intégrations externes avancées non prévues (CRM, ERP, etc.)

### **2.3 Hypothèses**

* Les utilisateurs disposent d’un accès Internet  
* L’application est utilisée via un navigateur web moderne  
* Chaque entreprise gère ses propres utilisateurs et agents  
* Les administrateurs sont responsables de la configuration de leur entreprise

### **2.4 Contraintes**

* Développement réalisé par une seule personne  
* Respect de la sécurité et de la confidentialité des données  
* Architecture évolutive permettant l’ajout de nouvelles fonctionnalités

## **3 — Acteurs, rôles et droits d’accès**

### **3.1 Entreprise (Organisation)**

Une entreprise est une entité autonome utilisant la plateforme. Elle regroupe des utilisateurs (membres) et dispose de ses propres données.

**Caractéristiques :**

* une entreprise possède ses propres tickets, formulaires, catégories et paramètres  
* une entreprise est isolée des autres (aucune donnée partagée)

### **3.2 Utilisateur (Membre)**

Un membre est un utilisateur rattaché à une entreprise. Il peut créer et suivre des tickets.

**Responsabilités :**

* créer un ticket via un formulaire de soumission  
* consulter la liste de ses tickets  
* accéder aux détails d’un ticket (statut, messages, pièces jointes)  
* participer à la discussion privée sur ses tickets  
* ajouter des pièces jointes si nécessaire  
* clôturer un ticket (si autorisé)

**Droits :**

* accès uniquement aux tickets créés par lui (sauf règle contraire définie par l’entreprise)  
* aucun accès aux discussions internes (groupe agents / notes internes)

### **3.3 Agent Support**

Un agent support est un membre chargé du traitement des tickets.

**Responsabilités :**

* consulter les tickets à traiter (selon attribution ou règles internes)  
* répondre aux tickets via la discussion privée  
* mettre à jour le statut du ticket  
* définir/modifier la priorité  
* attribuer ou transférer un ticket (si autorisé)  
* collaborer avec d’autres agents via discussion de groupe  
* ajouter des notes internes

**Droits :**

* accès aux tickets de l’entreprise selon son périmètre (assignés, catégorie, ou global selon paramétrage)  
* accès aux discussions internes et notes internes  
* aucun accès aux paramètres globaux de l’entreprise (sauf permission accordée)

### **3.4 Administrateur (Entreprise)**

L’administrateur gère la configuration de l’entreprise et supervise le support.

**Responsabilités :**

* gérer les membres (ajout, suppression, rôles)  
* configurer les catégories et priorités  
* gérer les formulaires (Form Builder \+ règles de visibilité)  
* configurer les notifications  
* consulter les statistiques et tableaux de bord  
* configurer le branding (couleur principale, logo optionnel)

**Droits :**

* accès à tous les tickets de l’entreprise  
* accès complet aux fonctionnalités de configuration  
* accès aux discussions internes et notes internes

### **3.5 Propriétaire (Owner)**

Le propriétaire est le créateur de l’entreprise (ou responsable principal). Il dispose de tous les droits.

**Responsabilités :**

* mêmes responsabilités qu’un administrateur  
* gestion avancée de l’entreprise (ex : transfert de propriété, suppression de l’entreprise si prévu)

**Droits :**

* accès total à toutes les données et paramètres de l’entreprise

### **3.6 Règles d’accès communes (importantes)**

* Un utilisateur peut appartenir à plusieurs entreprises  
* Un utilisateur ne peut consulter que les données de l’entreprise active  
* Aucune donnée d’une entreprise ne doit être visible par une autre  
* Les notes internes et discussions de groupe ne doivent jamais être visibles pour les membres (utilisateurs)

## **4 — Gestion multi-entreprises**

Cette étape définit **comment plusieurs entreprises coexistent** dans la même application, sans conflit ni fuite de données.

### **4.1 Création d’une entreprise**

* Tout utilisateur inscrit peut créer une entreprise  
* Lors de la création, l’utilisateur devient automatiquement **Propriétaire (Owner)** de l’entreprise  
* Les informations minimales à fournir sont :

  * nom de l’entreprise  
  * (optionnel) description  
  * paramètres visuels par défaut (couleur principale)

### **4.2 Appartenance à plusieurs entreprises**

* Un utilisateur peut appartenir à **une ou plusieurs entreprises**  
* Pour chaque entreprise, l’utilisateur dispose d’un **rôle spécifique** (membre, agent, administrateur, propriétaire)  
* Les droits de l’utilisateur dépendent **uniquement** de son rôle dans l’entreprise active

### **4.3 Invitation et gestion des membres**

* Le propriétaire ou l’administrateur peut :

  * inviter de nouveaux membres dans l’entreprise  
  * définir leur rôle lors de l’ajout  
  * modifier ou retirer un membre de l’entreprise

* Un membre retiré perd immédiatement l’accès aux données de l’entreprise

### **4.4 Sélection de l’entreprise active**

* Lors de la connexion, l’utilisateur doit sélectionner une **entreprise active**  
* L’utilisateur peut changer d’entreprise active à tout moment  
* Toutes les données affichées (tickets, formulaires, messages, statistiques) dépendent de l’entreprise active

### **4.5 Isolation des données**

* Chaque donnée (ticket, message, formulaire, catégorie, statistique) est rattachée à **une seule entreprise**  
* Il est strictement interdit qu’un utilisateur accède aux données d’une autre entreprise  
* L’isolation des données est garantie par :

  * des règles d’accès  
  * des contrôles au niveau applicatif  
  * une structuration adaptée de la base de données

### **4.6 Scénarios d’utilisation**

**Scénario 1 :**  
 Un utilisateur possède deux entreprises. Il sélectionne l’entreprise A → seuls les tickets et paramètres de A sont visibles.

**Scénario 2 :**  
 Un agent appartient à une entreprise B. Il ne peut consulter que les tickets liés à B.

## **5 — Gestion des tickets**

Cette étape décrit **le cœur fonctionnel** de l’application : la création, le suivi et le traitement des tickets.

### **5.1 Création d’un ticket**

Un ticket représente une demande d’assistance soumise par un utilisateur.

Lors de la création d’un ticket, l’utilisateur doit pouvoir :

* sélectionner une **catégorie** de ticket  
* remplir un **formulaire de soumission** (standard ou personnalisé via le Form Builder)  
* saisir un **sujet**  
* fournir une **description détaillée**  
* joindre des **pièces jointes** (images, documents, etc.)  
* définir une **priorité** (si cette option est autorisée par l’entreprise)

Chaque ticket est automatiquement associé :

* à l’entreprise active  
* à l’utilisateur créateur  
* à un statut initial (*Ouvert*)

### **5.2 Catégories et priorités**

* Les tickets sont classés par **catégories** (ex. : technique, facturation, support général)  
* Les priorités possibles sont définies par l’entreprise (ex. : faible, normale, élevée, urgente)  
* Les catégories et priorités sont configurables par l’administrateur

### **5.3 Cycle de vie d’un ticket**

Un ticket suit un cycle de vie bien défini :

1. **Ouvert** : ticket créé et non encore traité  
2. **En cours** : ticket pris en charge par un agent  
3. **En attente** : en attente d’une réponse de l’utilisateur ou d’un tiers  
4. **Résolu** : solution proposée par l’agent  
5. **Fermé** : ticket clôturé définitivement

* Le changement de statut est effectué par un agent ou un administrateur  
* Certaines transitions peuvent être restreintes selon le rôle

### **5.4 Consultation et suivi des tickets**

#### **Pour l’utilisateur :**

* voir la liste de ses tickets  
* consulter le détail d’un ticket  
* suivre l’évolution du statut  
* accéder à l’historique des échanges

#### **Pour l’agent :**

* consulter la liste des tickets à traiter  
* filtrer les tickets par :  
  * statut  
  * priorité  
  * catégorie

* accéder aux détails complets d’un ticket

### **5.5 Assignation des tickets**

* Un ticket peut être assigné à :  
  * un agent  
  * ou une équipe d’agents (si configuré)

* L’assignation peut être :  
  * manuelle  
  * modifiée à tout moment par un agent autorisé

### **5.6 Pièces jointes**

* Les utilisateurs et agents peuvent joindre des fichiers aux tickets  
* Les fichiers sont associés au ticket correspondant  
* L’accès aux pièces jointes est limité aux utilisateurs autorisés

### **5.7 Règles métier liées aux tickets**

* Un ticket appartient à **une seule entreprise**  
* Un utilisateur ne peut créer un ticket que pour l’entreprise active  
* Un agent ne peut traiter que les tickets de son entreprise  
* Les tickets clôturés ne peuvent plus être modifiés (sauf par un administrateur)

## **6 — Système de discussion et de communication**

Cette étape décrit **comment les utilisateurs et les agents communiquent** autour d’un ticket, de manière claire et sécurisée.

### **6.1 Principes généraux**

* Chaque ticket dispose d’un **espace de discussion**  
* Les messages sont organisés chronologiquement  
* Les discussions sont liées à un ticket et à une entreprise  
* Les règles de visibilité dépendent du type de discussion

### **6.2 Discussion privée (Utilisateur ↔ Agent)**

La discussion privée est le canal principal de communication avec l’utilisateur.

**Caractéristiques :**

* discussion entre l’utilisateur créateur du ticket et les agents autorisés  
* visible uniquement par l’utilisateur concerné et les agents de l’entreprise  
* utilisée pour poser des questions, demander des informations et proposer des solutions

**Fonctionnalités :**

* envoi de messages texte  
* ajout de pièces jointes  
* notifications lors de nouvelles réponses

### **6.3 Discussion de groupe (Agents uniquement)**

La discussion de groupe permet la collaboration interne entre agents.

**Caractéristiques :**

* discussion entre plusieurs agents assignés au ticket  
* non visible par l’utilisateur  
* utilisée pour échanger des informations techniques ou organisationnelles

**Fonctionnalités :**

* messages texte entre agents  
* possibilité de mentionner d’autres agents  
* historique conservé avec le ticket

### **6.4 Notes internes**

Les notes internes sont des messages non conversationnels utilisés pour le suivi interne.

**Caractéristiques :**

* visibles uniquement par les agents et administrateurs  
* non visibles par les utilisateurs  
* utilisées pour documenter des décisions ou des informations importantes

### **6.5 Règles de visibilité et sécurité**

* Les utilisateurs n’ont jamais accès aux discussions de groupe ni aux notes internes  
* Les agents et administrateurs peuvent consulter l’ensemble des discussions liées aux tickets de leur entreprise  
* Tous les messages sont soumis à des contrôles d’accès stricts

### **6.6 Notifications liées aux discussions**

* Une notification est envoyée :  
  * lorsqu’un agent répond à un utilisateur  
  * lorsqu’un utilisateur répond à un agent

* Les notifications respectent le rôle et la visibilité des messages

### **6.7 Règles métier**

* Chaque message est lié à un ticket et à une entreprise  
* Les messages d’un ticket clôturé ne peuvent plus être modifiés  
* Les discussions sont conservées à des fins de traçabilité

## **7 — Form Builder (Création de formulaires dynamiques)**

Cette étape décrit le **module de création de formulaires**, utilisé pour adapter la collecte d’informations lors de la création des tickets.

### **7.1 Objectif du Form Builder**

Le Form Builder permet aux administrateurs de créer des formulaires personnalisés sans développement technique afin de :

* adapter les informations demandées selon le type de ticket  
* améliorer la qualité des données reçues  
* réduire les échanges inutiles entre utilisateurs et agents

### **7.2 Création et gestion des formulaires**

Les administrateurs peuvent :

* créer un nouveau formulaire  
* modifier un formulaire existant  
* activer ou désactiver un formulaire  
* supprimer un formulaire (si non utilisé)

Chaque formulaire est rattaché à :

* une entreprise  
* une ou plusieurs catégories de tickets

### **7.3 Gestion des champs**

Un formulaire est composé de champs dynamiques.

**Types de champs supportés :**

* champ texte  
* zone de texte  
* liste déroulante  
* cases à cocher  
* boutons radio  
* date  
* fichier

Pour chaque champ, l’administrateur peut définir :

* le libellé du champ  
* le type de champ  
* le caractère obligatoire ou facultatif  
* les options (pour les listes et choix multiples)

### **7.4 Réorganisation des champs (Drag & Drop)**

* Les champs peuvent être réorganisés par **glisser-déposer**  
* L’ordre des champs est sauvegardé automatiquement  
* La modification de l’ordre est visible immédiatement lors de la soumission d’un ticket

### **7.5 Visibilité des formulaires**

Chaque formulaire dispose d’une règle de visibilité :

* **Formulaire public**

 Accessible à tous les utilisateurs de l’entreprise

* **Formulaire privé**

Accessible uniquement à certains rôles (ex. : agents, administrateurs)

* **Formulaire ciblé**

Accessible uniquement à des utilisateurs ou rôles spécifiques définis par l’administrateur

### **7.6 Utilisation des formulaires lors de la création d’un ticket**

* Lors de la création d’un ticket, le formulaire approprié est affiché automatiquement  
* Les champs doivent être remplis selon les règles définies  
* Les réponses sont enregistrées et associées au ticket

### **7.7 Règles métier liées au Form Builder**

* Un formulaire appartient à une seule entreprise  
* Un formulaire ne peut être utilisé que pour les catégories autorisées  
* Les réponses aux formulaires sont conservées avec le ticket  
* Les formulaires privés ou ciblés ne sont jamais visibles par des utilisateurs non autorisés

## **8 — Gestion des utilisateurs et des membres**

Cette étape décrit **comment les utilisateurs sont gérés**, invités et organisés au sein de chaque entreprise.

### **8.1 Création et inscription des utilisateurs**

* Tout utilisateur peut s’inscrire sur la plateforme via un formulaire d’inscription  
* Un compte utilisateur est unique et peut être associé à plusieurs entreprises  
* Un utilisateur peut créer une entreprise ou rejoindre une entreprise existante (sur invitation)

### **8.2 Invitation des membres à une entreprise**

* Le propriétaire ou l’administrateur d’une entreprise peut inviter de nouveaux membres  
* L’invitation est envoyée par e-mail  
* Lors de l’invitation, un rôle est attribué au membre  
* L’utilisateur invité doit accepter l’invitation pour rejoindre l’entreprise

### **8.3 Rôles des membres au sein d’une entreprise**

Chaque membre possède un rôle spécifique dans chaque entreprise :

* **Propriétaire (Owner)**  
  * contrôle total sur l’entreprise  
  * gestion avancée des membres et paramètres

* **Administrateur**  
  * gestion des membres  
  * configuration des catégories, formulaires et paramètres  
  * accès aux statistiques

* **Agent**  
  * traitement des tickets  
  * accès aux discussions internes  
  * mise à jour des statuts et priorités

* **Membre (Utilisateur)**  
  * création et suivi de tickets  
  * participation aux discussions privées

### **8.4 Modification et révocation des accès**

* Le propriétaire ou l’administrateur peut :  
  * modifier le rôle d’un membre  
  * désactiver ou supprimer un membre de l’entreprise

* Un membre supprimé perd immédiatement l’accès :  
  * aux tickets  
  * aux discussions  
  * aux formulaires de l’entreprise

### **8.5 Règles de sécurité liées aux utilisateurs**

* Les permissions sont évaluées en fonction :  
  * du rôle  
  * de l’entreprise active

* Un utilisateur ne peut jamais accéder aux données d’une entreprise à laquelle il n’appartient pas

### **8.6 Scénarios d’utilisation**

**Scénario 1 :**  
 Un administrateur invite un agent → l’agent accepte → il accède aux tickets de l’entreprise.

**Scénario 2 :**  
 Un utilisateur appartient à deux entreprises → il change d’entreprise active → l’interface et les données changent automatiquement.

## **9 — Personnalisation de l’interface (Branding par entreprise)**

Cette étape décrit comment **chaque entreprise peut personnaliser l’apparence** de l’application afin de refléter son identité visuelle.

### **9.1 Objectif de la personnalisation**

La personnalisation de l’interface a pour objectif :

* d’offrir une expérience utilisateur cohérente avec l’identité de chaque entreprise  
* de renforcer le sentiment d’appropriation de la plateforme  
* de différencier visuellement les entreprises utilisant la même application

### **9.2 Paramètres de personnalisation disponibles**

Chaque entreprise peut configurer les éléments suivants :

* **Couleur principale de l’interface**  
  * utilisée pour les boutons, liens, éléments actifs et indicateurs visuels  
  * définie par l’administrateur ou le propriétaire de l’entreprise

* **Logo de l’entreprise** (optionnel)  
  * affiché dans l’interface (ex. : barre de navigation, page d’accueil)

* **Mode d’affichage** (optionnel)  
  * clair ou sombre, selon la configuration choisie

### **9.3 Gestion des paramètres de branding**

* Seuls les rôles **Propriétaire** et **Administrateur** peuvent modifier les paramètres visuels  
* Les modifications sont appliquées immédiatement après enregistrement  
* Chaque entreprise conserve ses propres paramètres de personnalisation

### **9.4 Application dynamique du thème**

* Lorsqu’un utilisateur se connecte ou change d’entreprise active :  
  * la couleur principale de l’interface est mise à jour automatiquement  
  * l’interface s’adapte sans rechargement complet de la page

* Les paramètres visuels sont appliqués à l’ensemble de l’application :  
  * pages tickets  
  * formulaires  
  * tableaux de bord

### **9.5 Règles métier liées à la personnalisation**

* La personnalisation est toujours liée à l’entreprise active  
* Un utilisateur appartenant à plusieurs entreprises voit l’interface changer selon l’entreprise sélectionnée  
* Les paramètres de personnalisation d’une entreprise n’affectent jamais les autres entreprises

### **9.6 Valeur ajoutée**

La personnalisation de l’interface contribue à :

* améliorer l’expérience utilisateur  
* renforcer l’identité de chaque entreprise  
* rendre la plateforme plus professionnelle et adaptable

## **10 — Notifications et suivi des actions**

Cette étape décrit **comment les utilisateurs sont informés** des événements importants et comment le système assure le suivi des actions.

### **10.1 Objectif des notifications**

Le système de notifications a pour objectif de :

* informer rapidement les utilisateurs et agents des actions importantes  
* améliorer la réactivité du support  
* éviter les oublis et les retards de traitement

### **10.2 Types de notifications**

Les notifications peuvent être déclenchées lors des événements suivants :

* création d’un ticket  
* réponse à un ticket (utilisateur ou agent)  
* changement de statut d’un ticket  
* assignation ou réassignation d’un ticket  
* clôture d’un ticket

### **10.3 Canaux de notification**

Les notifications sont envoyées via :

* **notifications internes** dans l’application  
* **notifications par e-mail** (selon configuration)

Chaque utilisateur peut :

* recevoir ou non certaines notifications  
* consulter l’historique de ses notifications

### **10.4 Règles de notification**

* Les notifications respectent les rôles et permissions  
* Un utilisateur ne reçoit que les notifications liées :  
  * à ses tickets  
  * ou à son rôle dans l’entreprise

* Les notifications internes liées aux discussions de groupe ou notes internes ne sont jamais envoyées aux utilisateurs

### **10.5 Suivi des actions (traçabilité)**

Le système conserve un historique des actions importantes :

* création et modification des tickets  
* changements de statut  
* assignations  
* actions administratives

Cet historique permet :

* d’assurer la traçabilité  
* de faciliter les audits internes  
* de résoudre les litiges ou incompréhensions

### **10.6 Règles métier**

* Chaque notification est liée à une entreprise  
* Les notifications sont conservées pendant une durée défini  
* Les utilisateurs ne peuvent pas consulter les notifications d’une autre entreprise

## **11 — Statistiques et tableaux de bord**

Cette étape décrit **les outils de suivi et d’analyse** permettant aux administrateurs et responsables de mesurer la performance du support.

### **11.1 Objectif des statistiques**

Les statistiques ont pour objectif de :

* mesurer l’efficacité du support  
* suivre la charge de travail des agents  
* identifier les points d’amélioration  
* aider à la prise de décision

### **11.2 Tableau de bord administrateur**

Le tableau de bord doit afficher une vue globale de l’activité de l’entreprise, incluant :

* nombre total de tickets créés  
* nombre de tickets par statut (ouverts, en cours, résolus, fermés)  
* répartition des tickets par catégorie  
* répartition des tickets par priorité  
* évolution du nombre de tickets dans le temps

### **11.3 Indicateurs de performance (KPI)**

Les indicateurs suivants doivent être disponibles :

* temps moyen de première réponse  
* temps moyen de résolution  
* nombre de tickets traités par agent  
* taux de résolution des tickets  
* volume de tickets par période

**11.4 Filtres et périodes**

* Les statistiques peuvent être filtrées par :  
  * période (jour, semaine, mois)  
  * catégorie  
  * agent

* Les données affichées concernent uniquement l’entreprise active

### **11.5 Accès aux statistiques**

* Les statistiques sont accessibles :  
  * aux administrateurs  
  * aux propriétaires de l’entreprise

* Les agents peuvent avoir un accès limité selon configuration  
* Les utilisateurs (membres) n’ont pas accès aux statistiques globales

### **11.6 Règles métier liées aux statistiques**

* Les statistiques sont calculées uniquement à partir des données de l’entreprise active  
* Aucune donnée agrégée entre plusieurs entreprises n’est affichée  
* Les indicateurs sont mis à jour automatiquement

## **12 — Sécurité, règles et exigences non fonctionnelles**

Cette étape définit les **exigences de qualité**, de sécurité et de performance indispensables au bon fonctionnement de l’application.

### **12.1 Sécurité des accès**

* L’accès à l’application nécessite une authentification sécurisée  
* Chaque utilisateur doit s’identifier avant d’accéder aux fonctionnalités  
* Les droits d’accès sont définis :  
  * par le rôle de l’utilisateur  
  * par l’entreprise active

### **12.2 Gestion des permissions**

* Les permissions sont évaluées dynamiquement selon :  
  * le rôle dans l’entreprise  
  * l’action demandée

* Un utilisateur ne peut jamais accéder :  
  * aux données d’une autre entreprise  
  * aux fonctionnalités non autorisées par son rôle

### **12.3 Isolation des données (multi-entreprises)**

* Toutes les données sont strictement isolées par entreprise  
* Chaque ticket, message, formulaire et statistique est lié à une seule entreprise  
* Aucune fuite de données entre entreprises n’est autorisée

### **12.4 Sécurité des discussions**

* Les discussions privées sont accessibles uniquement aux participants autorisés  
* Les discussions de groupe et notes internes sont invisibles pour les utilisateurs  
* Les contrôles d’accès sont appliqués à chaque message

### **12.5 Sécurité des fichiers**

* Les pièces jointes sont stockées de manière sécurisée  
* L’accès aux fichiers est limité aux utilisateurs autorisés  
* Les types de fichiers acceptés peuvent être restreints

### **12.6 Performance**

* L’application doit garantir :  
  * un temps de réponse rapide  
  * une navigation fluide  
  * une gestion efficace d’un volume élevé de tickets

* Les opérations lourdes (statistiques, notifications) doivent être optimisées

### **12.7 Disponibilité et fiabilité**

* Le système doit être disponible en continu  
* Des mécanismes de sauvegarde doivent être mis en place  
* Les données doivent pouvoir être restaurées en cas de problème

### **12.8 Ergonomie et accessibilité**

* L’interface doit être :  
  * claire  
  * intuitive  
  * facile à prendre en main

* L’application doit être :  
  * compatible avec les navigateurs modernes  
  * responsive (ordinateur, tablette, mobile)

### **12.9 Évolutivité**

* L’architecture doit permettre :  
  * l’ajout de nouvelles fonctionnalités  
  * l’augmentation du nombre d’utilisateurs et d’entreprises

* Le code doit être structuré et maintenable

### **12.10 Règles métier essentielles (récapitulatif)**

* Un ticket appartient à une seule entreprise  
* Un utilisateur peut appartenir à plusieurs entreprises  
* Les discussions internes ne sont jamais visibles par les utilisateurs  
* Les formulaires ciblés sont accessibles uniquement aux rôles autorisés  
* La personnalisation de l’interface dépend de l’entreprise active

## **13 — Critères de validation et scénarios de tests**

Cette étape définit **comment vérifier que l’application fonctionne correctement** et respecte toutes les exigences fonctionnelles.

### **13.1 Objectif des tests**

Les tests ont pour objectif de :

* vérifier que toutes les fonctionnalités décrites sont opérationnelles  
* s’assurer du respect des règles de sécurité et des permissions  
* valider la stabilité et la fiabilité de l’application

### **13.2 Critères de validation généraux**

L’application sera considérée comme conforme si :

* toutes les fonctionnalités du cahier des charges sont implémentées  
* les rôles et permissions sont respectés  
* les données sont correctement isolées entre les entreprises  
* les formulaires et tickets fonctionnent correctement  
* l’interface s’adapte à l’entreprise active

### **13.3 Scénarios de tests fonctionnels**

#### **Test 1 : Création d’entreprise**

* Un utilisateur crée une entreprise  
* L’utilisateur devient propriétaire  
* L’entreprise apparaît dans la liste des entreprises de l’utilisateur

**Résultat attendu :** entreprise créée et accessible.

#### **Test 2 : Gestion des membres**

* Un administrateur invite un agent  
* L’agent accepte l’invitation  
* L’agent accède aux tickets de l’entreprise

**Résultat attendu :** accès conforme au rôle.

#### **Test 3 : Création d’un ticket**

* Un utilisateur crée un ticket avec formulaire personnalisé  
* Le ticket est enregistré avec le statut *Ouvert*

**Résultat attendu :** ticket visible par l’utilisateur et les agents.

#### **Test 4 : Discussion privée**

* L’utilisateur envoie un message  
* L’agent répond

**Résultat attendu :** messages visibles uniquement par les participants autorisés.

#### **Test 5 : Discussion de groupe**

* Deux agents échangent sur un ticket

**Résultat attendu :** discussion invisible pour l’utilisateur.

#### **Test 6 : Form Builder**

* L’administrateur crée un formulaire  
* Il ajoute et réorganise des champs par drag & drop

**Résultat attendu :** formulaire fonctionnel et correctement affiché.

#### **Test 7 : Multi-entreprises**

* Un utilisateur appartient à deux entreprises  
* Il change d’entreprise active

**Résultat attendu :** les données affichées changent automatiquement.

#### **Test 8 : Personnalisation de l’interface**

* L’administrateur modifie la couleur principale  
* L’utilisateur recharge l’application

**Résultat attendu :** interface mise à jour selon la nouvelle couleur.

### **13.4 Validation finale**

Le projet est validé lorsque :

* tous les tests sont concluants  
* aucune faille de sécurité n’est détectée  
* l’application répond aux objectifs définis

## **14 — Conclusion du cahier des charges fonctionnel**

Le présent cahier des charges fonctionnel définit de manière détaillée et structurée l’ensemble des besoins, fonctionnalités et règles de fonctionnement de l’application de gestion de tickets multi-entreprises.

Il couvre l’intégralité du périmètre fonctionnel, depuis la gestion des entreprises et des utilisateurs jusqu’au traitement des tickets, aux échanges via le système de discussion, à la création de formulaires dynamiques et à la personnalisation de l’interface.

Grâce à une définition claire des acteurs, des rôles, des règles métier et des exigences non fonctionnelles, ce document constitue une référence fiable pour le développement, les tests et l’évolution future de l’application. Il permet de garantir une compréhension commune des attentes et de limiter les ambiguïtés tout au long du projet.

Ce cahier des charges servira ainsi de base officielle pour la mise en œuvre de la solution et assurera la cohérence, la qualité et la pérennité de l’application développée.

