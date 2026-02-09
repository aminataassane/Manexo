# Application de gestion de ticket

## **Manexo** (management \+ nexus) 

# Introduction  

Dans le cadre de l’amélioration du service d’assistance et de la gestion des demandes, ce projet vise à mettre en place une application de type **Helpdesk / Support Ticket System** (à l’image de TicketGo). Ce type d’application permet de centraliser toutes les requêtes des utilisateurs (problèmes techniques, demandes d’information, incidents, réclamations, suivi de service) au sein d’une plateforme unique, structurée et facilement exploitable.

L’objectif principal est de remplacer les canaux dispersés (messages, appels, e-mails, réseaux sociaux) par un système organisé où chaque demande devient un **ticket**. Chaque ticket est enregistré, classé (catégorie, priorité), suivi (statut, historique) et traité par un agent ou une équipe dédiée. Cela améliore la traçabilité, réduit les oublis et permet de mieux gérer les délais de réponse.

Grâce à cette application, l’organisation peut améliorer la qualité du support, optimiser le travail des équipes, suivre les performances (temps de réponse, nombre de tickets traités, taux de résolution) et offrir une meilleure expérience aux utilisateurs. En résumé, une application de gestion de tickets est un outil essentiel pour assurer un support efficace, professionnel et mesurable.

De plus, l’application intègre un mécanisme de formulaires personnalisables permettant d’adapter la collecte des informations selon le type de demande et le profil des utilisateurs.

L’application est également conçue pour fonctionner dans un environnement multi-entreprises, permettant à plusieurs organisations de coexister sur une même plateforme tout en conservant une séparation stricte de leurs données et une personnalisation de leur interface.

1. # Contexte et problématique

   1. ## Contexte du projet

Avec l’augmentation du nombre d’utilisateurs et de services proposés, la gestion des demandes d’assistance devient de plus en plus complexe. Les organisations reçoivent quotidiennement des requêtes sous différentes formes : e-mails, appels téléphoniques, messages instantanés ou échanges directs. Cette diversité de canaux rend le suivi difficile et peut entraîner des retards, des oublis ou une mauvaise priorisation des demandes.

En l’absence de formulaires adaptés, les informations fournies par les utilisateurs sont souvent incomplètes ou non structurées, ce qui complique le traitement des demandes et allonge les délais de résolution.

Dans ce contexte, il devient nécessaire de disposer d’un outil centralisé capable d’organiser, suivre et gérer efficacement toutes les demandes d’assistance. C’est dans cette optique que s’inscrit le projet de mise en place d’une application de gestion de tickets de type TicketGo.

De plus, plusieurs entreprises peuvent être amenées à utiliser simultanément la même application de support, chacune avec ses propres utilisateurs, agents et règles de gestion. Sans un mécanisme multi-entreprises, cela peut entraîner des problèmes de confidentialité, de sécurité et de personnalisation de l’expérience utilisateur.

2. ## Problématique

L’absence d’un système structuré de gestion des demandes pose plusieurs problèmes :

* perte ou oubli de certaines requêtes,  
* difficulté à suivre l’état d’avancement des demandes,  
* manque de visibilité pour les responsables,  
* temps de réponse élevé,  
* insatisfaction des utilisateurs.  
* informations incomplètes ou non adaptées au type de demande  
* difficulté à gérer plusieurs entreprises de manière isolée au sein d’une même plateforme

La problématique principale est donc la suivante :  
 **Comment mettre en place un système efficace permettant de centraliser les demandes, d’assurer leur suivi et d’améliorer la qualité du support tout en optimisant le travail des équipes ?**

Il est également nécessaire de garantir une séparation des données entre les entreprises et d’offrir une expérience personnalisée adaptée à chaque organisation et de disposer d’un mécanisme flexible permettant de collecter des informations précises et adaptées à chaque type de ticket.

L’application de gestion de tickets apporte une réponse directe à cette problématique en proposant un cadre organisé, traçable et mesurable pour le traitement des demandes.

2. #  Objectifs du projet

   1. ## Objectif général

L’objectif principal de ce projet est de mettre en place une application de gestion de tickets permettant de centraliser, organiser et suivre efficacement toutes les demandes d’assistance des utilisateurs. Cette application vise à améliorer la qualité du support, réduire les délais de traitement et offrir une meilleure visibilité sur l’ensemble des demandes.

2. ## Objectifs spécifiques

Les objectifs spécifiques du projet sont les suivants :

* Centraliser toutes les demandes d’assistance sur une seule plateforme  
* Structurer les demandes sous forme de tickets avec un suivi clair  
* Réduire le temps de réponse et de résolution des tickets  
* Faciliter le travail des agents support grâce à une meilleure organisation  
* Assurer la traçabilité et l’historique des échanges  
* Permettre aux responsables de suivre les performances du support via des statistiques  
* Améliorer la satisfaction des utilisateurs grâce à un support plus rapide et plus fiable  
* Permettre la création de formulaires personnalisés selon les besoins  
* Adapter la collecte d’informations en fonction des catégories et des utilisateurs  
* Permettre à plusieurs entreprises de s’inscrire et d’utiliser la plateforme de manière indépendante  
* Gérer les membres et les rôles au sein de chaque entreprise  
* Offrir une personnalisation de l’interface selon l’entreprise connectée

3. #  Périmètre du projet

   1. ## Portée du projet

Le présent projet concerne la conception et la mise en place d’une application web de gestion de tickets de support, destinée à centraliser et gérer les demandes d’assistance des utilisateurs. L’application couvrira l’ensemble du cycle de vie d’un ticket, depuis sa création jusqu’à sa résolution et son archivage.

Le système permettra aux utilisateurs de soumettre leurs demandes via une interface dédiée, tandis que les agents et administrateurs pourront traiter, suivre et analyser ces demandes à partir d’un tableau de bord centralisé.

2. ## Fonctionnalités incluses dans le périmètre

Le projet inclut notamment :

* La création et la gestion des tickets de support  
* La gestion des utilisateurs et des rôles (utilisateur, agent, administrateur)  
* Le suivi de l’état des tickets (ouvert, en cours, résolu, fermé)  
* La communication entre utilisateurs et agents via un fil de discussion  
* La gestion des catégories, priorités et pièces jointes  
* La génération de statistiques et de rapports de suivi  
* La création et la gestion de formulaires personnalisés pour la soumission de tickets  
* La gestion multi-entreprises (création et sélection d’entreprise)  
* La gestion des membres et des rôles par entreprise  
* La personnalisation de l’interface (couleur principale) par entreprise

  3. ## Hors périmètre

Ne sont pas inclus dans le périmètre de ce projet :

* Le support téléphonique direct  
* Le développement d’une application mobile native (sauf version responsive web)  
* La gestion avancée de la facturation ou des paiements  
* Les intégrations externes complexes non prévues initialement

4. #  Acteurs et rôles du système

   1. ## Utilisateur (Client)

L’utilisateur est la personne qui utilise l’application pour soumettre des demandes d’assistance. Il peut s’agir d’un client externe ou d’un employé interne selon le contexte d’utilisation.

Ses principales responsabilités sont :

* Créer des tickets de support  
* Fournir les informations nécessaires à la résolution du problème  
* Consulter l’état d’avancement de ses tickets  
* Répondre aux messages des agents  
* Joindre des fichiers si nécessaire  
* Remplir les formulaires mis à disposition lors de la création des tickets

  2. ## Agent Support

L’agent support est chargé du traitement des tickets soumis par les utilisateurs. Il assure la communication, l’analyse et la résolution des demandes.

Ses responsabilités incluent :

* Consulter et gérer les tickets assignés  
* Répondre aux demandes des utilisateurs  
* Mettre à jour le statut et la priorité des tickets  
* Ajouter des notes internes  
* Collaborer avec d’autres agents si nécessaire


  3. ## Administrateur

L’administrateur est responsable de la configuration et du bon fonctionnement de l’application.

Ses responsabilités sont :

* Gérer les utilisateurs, agents et rôles  
* Créer et gérer les catégories et départements  
* Configurer les paramètres généraux de l’application  
* Accéder aux statistiques et rapports  
* Assurer la sécurité et la maintenance du système  
* Créer, configurer et gérer les formulaires de soumission des tickets  
* gérer les membres de l’entreprise  
* définir les paramètres visuels de l’entreprise (couleur principale, identité graphique)


  4. ## Entreprise (Organisation)

L’entreprise représente une organisation utilisant l’application. Chaque entreprise dispose de ses propres utilisateurs, tickets, formulaires et paramètres.

Ses caractéristiques principales sont :

* isolation complète des données par entreprise  
* gestion indépendante des utilisateurs et des rôles  
* configuration et personnalisation propres à chaque entreprise

5. #  Fonctionnalités du système

   1. ## Fonctionnalités côté Utilisateur (Client)

L’application doit permettre à l’utilisateur de :

**Créer un ticket de support**  
L’utilisateur peut soumettre une demande en renseignant :

* le sujet du ticket  
* une description détaillée du problème  
* une catégorie  
* une priorité (si autorisée)  
* des pièces jointes (images, documents)

**Consulter ses tickets**  
L’utilisateur peut :

* voir la liste de ses tickets  
* consulter leur statut (ouvert, en cours, résolu, fermé)  
* accéder à l’historique des échanges

**Communiquer avec le support**  
L’utilisateur peut :

* répondre aux messages des agents  
* recevoir des notifications lors des réponses ou mises à jour

**Clôturer un ticket**  
confirmer que le problème est résolu (si autorisé)

2. ##  Fonctionnalités côté Agent Support

L’agent support doit pouvoir :

**Gérer les tickets**

* consulter la liste des tickets  
* filtrer et rechercher les tickets (statut, priorité, catégorie)

**Traiter les tickets**

* répondre aux utilisateurs  
* modifier le statut du ticket  
* définir ou modifier la priorité  
* assigner ou réassigner un ticket 

**Ajouter des informations internes**

* notes internes visibles uniquement par les agents  
* historique des actions effectuées


**Collaborer**

* transférer un ticket à un autre agent  
* travailler sur des tickets partagés


  3. ## Fonctionnalités côté Administrateur

L’administrateur doit disposer de fonctionnalités avancées :

**Gestion des utilisateurs**

* création, modification et suppression des comptes  
* attribution des rôles (utilisateur, agent, administrateur)  
* internes visibles uniquement par les agents

**Gestion des paramètres**

* configuration générale de l’application  
* paramètres de notification  
* gestion du stockage des fichiers

**Gestion des catégories et priorités**

* création et modification des catégories  
* définition des niveaux de priorité

**Suivi et statistiques**

* tableau de bord global  
* nombre de tickets créés et traités  
* temps moyen de réponse et de résolution

  4. ## Fonctionnalité : Form Builder (Création de formulaires)

L’application doit intégrer un outil de création de formulaires dynamiques (Form Builder) permettant aux administrateurs de concevoir des formulaires personnalisés sans développement technique.

**Fonctionnalités du Form Builder :**

* création de champs personnalisés (texte, liste, cases à cocher, fichier, etc.)  
* modification, suppression et réorganisation des champs  
* définition de champs obligatoires

**Visibilité des formulaires :**

* formulaires publics  
* formulaires privés  
* formulaires accessibles à des utilisateurs ou rôles spécifiques

**Utilisation :**

* association à des catégories de tickets  
* adaptation selon le type de demande  
* modification à tout moment par l’administrateur

  5. ## Gestion multi-entreprises

L’application doit permettre la gestion de plusieurs entreprises au sein d’une même plateforme.

**Fonctionnalités associées :**

* création d’une ou plusieurs entreprises par un utilisateur  
* association de plusieurs utilisateurs à une entreprise  
* attribution de rôles spécifiques au sein de chaque entreprise (propriétaire, administrateur, agent, membre)  
* sélection de l’entreprise active lors de la connexion  
* isolation stricte des données entre les entreprises


  6. ## Personnalisation de l’interface par entreprise

Chaque entreprise doit pouvoir personnaliser l’apparence de l’application.

**Fonctionnalités associées :**

* définition d’une couleur principale propre à l’entreprise  
* application automatique du thème lors de la connexion  
* adaptation dynamique de l’interface selon l’entreprise active

6. # Exigences non fonctionnelles

Les exigences non fonctionnelles définissent les critères de qualité et de performance du système, indépendamment des fonctionnalités métier. Le système doit garantir une isolation complète des données entre les entreprises afin d’éviter tout accès non autorisé.

1. ## Sécurité

L’application doit garantir la sécurité des données et des accès :

* authentification sécurisée des utilisateurs  
* gestion des rôles et permissions  
* protection des données sensibles  
* contrôle d’accès aux tickets selon les droits  
* sauvegarde régulière des données  
* L’accès aux formulaires privés ou ciblés doit être strictement contrôlé selon les rôles et permissions.


  2. ## Performance

Le système doit assurer :

* un temps de réponse rapide lors de l’accès aux tickets  
* une bonne gestion d’un volume élevé de tickets  
* une stabilité même en cas de forte utilisation


  3. ## Fiabilité et disponibilité

* disponibilité continue du système  
* mécanisme de sauvegarde et de restauration  
* journalisation des actions importantes


  4. ## Ergonomie et accessibilité

* interface simple et intuitive  
* compatibilité avec les principaux navigateurs  
* interface responsive adaptée aux écrans mobiles  
* facilité de prise en main pour les utilisateurs non techniques  
* Le module de création de formulaires doit être simple à utiliser et accessible aux administrateurs sans compétences techniques avancées.  
* L’interface utilisateur doit s’adapter automatiquement aux paramètres visuels définis par l’entreprise connectée.


  5. ## Évolutivité

* possibilité d’ajouter de nouvelles fonctionnalités  
* capacité à gérer un nombre croissant d’utilisateurs et de tickets  
* architecture permettant des intégrations futures


7. #  Livrables du projet

Les livrables représentent l’ensemble des éléments qui devront être fournis à la fin du projet afin de garantir son bon fonctionnement et son exploitation.

Les livrables attendus sont :

* **L’application de gestion de tickets**

Une application web fonctionnelle permettant la création, le suivi et la résolution des tickets de support.

* **La base de données**

Une base de données structurée contenant les utilisateurs, tickets, historiques et paramètres nécessaires au fonctionnement du système.

* **La documentation utilisateur**

Un guide expliquant comment créer et suivre un ticket, communiquer avec le support et utiliser les principales fonctionnalités.

* **La documentation administrateur**

Un guide destiné aux administrateurs pour la gestion des utilisateurs, des paramètres et des statistiques.

* **Le guide d’installation et de déploiement**

Un document décrivant les prérequis techniques et les étapes d’installation de l’application.

* **Les scénarios de tests**

Des cas de test permettant de vérifier le bon fonctionnement des principales fonctionnalités.

* **Le module de création de formulaires (Form Builder)**

Un outil permettant la création et la gestion de formulaires publics, privés ou ciblés.

* **Le module de gestion multi-entreprises**

Permettant la création, la gestion et l’isolation des entreprises et de leurs membres.

* **Le module de personnalisation de l’interface**

Permettant à chaque entreprise de définir sa couleur principale.

## **Conclusion**

La mise en place d’une application de gestion de tickets de type TicketGo constitue une solution efficace pour répondre aux besoins croissants de gestion et de suivi des demandes d’assistance. En centralisant l’ensemble des requêtes au sein d’une plateforme unique, le système permet d’améliorer l’organisation, la traçabilité et la qualité du support offert aux utilisateurs.

Ce projet met en évidence l’importance d’un outil structuré pour optimiser le travail des équipes de support, réduire les délais de traitement et garantir une meilleure satisfaction des utilisateurs. Grâce à des fonctionnalités adaptées, une gestion claire des rôles et des mécanismes de suivi et d’analyse, l’application contribue à une meilleure performance globale du service d’assistance.

En conclusion, l’adoption d’un système de gestion de tickets représente un atout stratégique pour toute organisation souhaitant professionnaliser son support, améliorer son efficacité opérationnelle et assurer une communication fluide et fiable avec ses utilisateurs.

L’intégration d’un module de création de formulaires dynamiques renforce davantage la flexibilité du système en permettant d’adapter les demandes aux besoins spécifiques de l’organisation et des utilisateurs.

La prise en charge du mode multi-entreprises et de la personnalisation de l’interface par organisation renforce le caractère professionnel, évolutif et adaptable de la solution proposée.