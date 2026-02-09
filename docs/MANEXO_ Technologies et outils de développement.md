# Technologies et outils de développement

Pour le développement de l’application de gestion de tickets, une architecture moderne, robuste et adaptée à un développement individuel a été retenue. Les technologies choisies permettent de construire une application riche, évolutive et maintenable, tout en limitant la complexité.

### 

### **1\. Framework Backend : Laravel**

Laravel est un framework PHP moderne utilisé pour le développement d’applications web robustes.

Il sera utilisé pour :

* la gestion de l’authentification et des sessions utilisateurs  
* la gestion des rôles et des permissions  
* la logique métier (tickets, entreprises, formulaires, utilisateurs)  
* la gestion de la base de données via un ORM (Eloquent)  
* la sécurité (validation, protection des données, accès contrôlés)

Laravel permet également une bonne structuration du code et facilite l’évolution future de l’application.

### **2\. Architecture de l’application**

L’application adopte une architecture **monolithique moderne**, dans laquelle :

* le backend et le frontend partagent la même base de code  
* les routes, la logique métier et l’interface sont gérées au sein d’un seul projet

Ce choix est particulièrement adapté à un développement individuel, car il réduit la complexité liée à la gestion de plusieurs applications séparées (API \+ frontend).

### **3\. Interface utilisateur dynamique : Livewire**

Livewire est une bibliothèque Laravel permettant de créer des interfaces dynamiques sans avoir à développer une application JavaScript complète.

Il sera utilisé pour :

* la création d’interfaces interactives (formulaires, tableaux, filtres)  
* la mise à jour dynamique des données sans rechargement de page  
* la gestion des actions utilisateurs (création de tickets, réponses, assignations)

Livewire permet d’obtenir une expérience utilisateur proche d’une application moderne tout en restant dans l’écosystème Laravel.

Il est également utilisé par Filament pour la gestion des interfaces administratives.

### **4\. Interface d’administration : Filament**

Filament est un framework d’administration conçu pour Laravel, permettant de créer rapidement des interfaces de gestion (back-office) modernes et sécurisées.

Dans ce projet, Filament sera utilisé pour implémenter l’interface d’administration destinée aux propriétaires et administrateurs des entreprises. Il permet de générer efficacement des tableaux, formulaires et tableaux de bord sans développement manuel excessif.

Filament sera utilisé pour :

* la gestion des entreprises (création, paramètres, personnalisation visuelle)  
* la gestion des utilisateurs et des membres par entreprise  
* la gestion des rôles et permissions  
* la gestion des catégories et priorités de tickets  
* la gestion des formulaires et des champs du Form Builder  
* l’affichage des statistiques et tableaux de bord

L’utilisation de Filament permet de réduire considérablement le temps de développement des fonctionnalités administratives tout en garantissant une interface cohérente, performante et sécurisée.

Filament s’intègre directement à Laravel et repose sur Livewire et Tailwind CSS, ce qui assure une parfaite compatibilité avec l’architecture globale de l’application.

### **5\. Interactions légères côté navigateur : Alpine.js**

Alpine.js est une bibliothèque JavaScript légère utilisée pour gérer de petites interactions côté interface.

Elle sera utilisée pour :

* l’ouverture et la fermeture de modales  
* les menus déroulants  
* certaines interactions visuelles simples

Alpine.js complète Livewire sans alourdir l’application.

### **6\. Fonctionnalité Drag & Drop : SortableJS**

Pour le module de création de formulaires (Form Builder), la fonctionnalité de glisser-déposer est nécessaire.

SortableJS sera utilisé pour :

* réorganiser les champs d’un formulaire  
* offrir une expérience intuitive lors de la création de formulaires  
* transmettre l’ordre des éléments au backend via Livewire

Cette approche permet d’intégrer le drag & drop sans recourir à un framework JavaScript lourd.

### **7\. Gestion des styles et du design : Tailwind CSS**

Tailwind CSS est un framework CSS utilitaire qui permet de construire rapidement des interfaces modernes et cohérentes.

Il sera utilisé pour :

* concevoir une interface responsive  
* gérer les couleurs et le thème de l’application  
* appliquer une personnalisation visuelle par entreprise (couleur principale)

L’utilisation de variables CSS permet d’adapter dynamiquement l’apparence selon l’entreprise connectée.

### **8\. Base de données : PostgresSQL**

PostgresSQL sera utilisé pour stocker l’ensemble des données de l’application :

* utilisateurs et entreprises  
* tickets et messages  
* formulaires et champs dynamiques  
* permissions et paramètres

La base de données est structurée de manière à garantir une isolation stricte des données entre les entreprises.

### **9\. Gestion des rôles et permissions**

La gestion des rôles et permissions est essentielle pour sécuriser l’application.

Elle permettra :

* de définir des rôles par entreprise (propriétaire, administrateur, agent, membre)  
* de contrôler l’accès aux fonctionnalités selon le rôle  
* de protéger les données sensibles

### **10\. Environnement de développement**

Les outils suivants seront utilisés :

* **Composer** pour la gestion des dépendances PHP  
* **Git** pour le contrôle de version  
* **Vite** pour la compilation des assets frontend

Ces outils facilitent le développement, la maintenance et le déploiement de l’application.

## **Conclusion technique**

Le choix de Laravel associé à Livewire, Alpine.js et Tailwind CSS permet de développer une application complète, moderne et évolutive tout en conservant une complexité maîtrisée. Cette stack est particulièrement adaptée à un projet riche développé par une seule personne, tout en restant suffisamment robuste pour évoluer vers une solution professionnelle à grande échelle.

L’administration de l’application est assurée par Filament, tandis que les interfaces utilisateurs sont développées avec Livewire.