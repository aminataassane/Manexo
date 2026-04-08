# Charte produit — Ticket, timeline et canaux

Document de référence pour l’organisation du ticketing : fiche ticket vs discussion, types de messages, identités, visibilité et règles de réponse. À utiliser pour cadrer l’UI, le modèle de données et les notifications.

---

## 1. Le ticket n’est pas la discussion

### Règle fondamentale

- **Le ticket = la fiche de travail**  
  Il représente la **demande** et les informations **stables ou semi-stables** :
  - identifiant public ;
  - source ;
  - demandeur ;
  - statut ;
  - priorité ;
  - catégorie ;
  - assignation ;
  - équipe ;
  - dates importantes ;
  - pièces jointes de **contexte** dossier ;
  - champs personnalisés.

  Autrement dit : **le ticket décrit le dossier**.

- **Les messages = la vie du ticket**  
  Ils représentent tout ce qui **se passe autour** du ticket :
  - demande initiale visible ;
  - réponses client ;
  - réponses support ;
  - messages e-mail ;
  - messages API ;
  - événements système ;
  - notes internes.

  Autrement dit : **les messages racontent l’histoire du ticket**.

**Ne jamais confondre** la fiche ticket et la conversation / timeline du ticket.

---

## 2. Le ticket doit répondre à cinq questions immédiatement

À l’ouverture d’un ticket, l’utilisateur doit comprendre tout de suite :

| Question | Contenu attendu |
|----------|------------------|
| De quoi s’agit-il ? | Sujet + contexte (fiche) |
| Qui est le demandeur ? | Personne concernée par la demande |
| D’où vient-il ? | Source : plateforme / formulaire / e-mail / API |
| Qui le traite ? | Assignation actuelle (agent / équipe) |
| Où en est-il ? | Statut (ouvert, en cours, en attente, résolu, fermé, etc.) |

Champs à tenir **lisibles** en permanence (fiche ou en-tête) :

- **Source** : `platform` / `form` / `email` / `api`
- **Demandeur** : la personne concernée par la demande
- **Créateur technique** (si différent) : qui ou quoi a créé l’enregistrement en système
- **Assignation actuelle** : agent / équipe
- **Statut actuel**

Sans cela, on se perd.

---

## 3. Quatre notions à ne jamais mélanger

### A. Le demandeur

C’est la personne **pour qui** la demande existe.

Exemples : le client qui envoie un e-mail ; la personne qui remplit un formulaire ; le membre qui crée un ticket pour lui-même.

### B. Le créateur technique

C’est celui qui a **techniquement** créé l’enregistrement ticket.

Exemples : utilisateur connecté ; expéditeur résolu par e-mail ; compte système / API ; soumission formulaire public (compte guest ou utilisateur).

### C. Le responsable de traitement

C’est **qui porte** le dossier : un agent, une équipe, plusieurs internes si besoin.

### D. L’acteur d’une action

C’est la personne qui a **fait** une action ponctuelle : assignation, changement de statut, réponse, réouverture, etc.

Ces quatre notions doivent être **séparées** partout : base de données, UI, timeline, notifications.

Sinon on produit des phrases fausses du type : « ticket créé par X » alors que X n’est que l’acteur technique, ou « pris en charge par Y » alors qu’il s’agit d’une équipe entière.

---

## 4. Une seule timeline, pas plusieurs vérités

- **Une seule timeline par ticket.**
- Tous les messages et événements vivent dans le **même fil**.

Dans cette timeline on doit pouvoir voir :

- message initial ;
- réponses client ;
- réponses agent ;
- message e-mail reçu ;
- message envoyé par e-mail ;
- message créé via API ;
- événements système ;
- notes internes **selon les droits**.

**Même timeline** ne signifie **pas** même nature de contenu : il faut des **types de messages** clairs (voir §5).

---

## 5. Quatre grands types de contenu dans la timeline

### 1. Message public

Vrai message de **conversation** : client, agent, e-mail reçu / envoyé côté conversation, visible par les parties autorisées au fil public. **Cœur de la discussion.**

### 2. Message interne

Contenu réservé à l’interne : note interne, précision support, échange équipe. **Jamais** visible comme conversation côté externe / client.

### 3. Message système

**Événement** : ticket créé, statut changé, priorité changée, assignation, réouverture, clôture. Ce n’est **pas** un message conversationnel : affichage en **événement**, pas comme bulle de discussion standard.

### 4. Message technique / canal (métadonnée sur un message public)

Message public mais avec **origine** identifiable : reçu par e-mail, envoyé par e-mail, créé via API, issu d’un formulaire. Reste dans le fil ; doit être **identifié** (badge, icône, libellé).

---

## 6. La demande initiale doit toujours exister dans la timeline

Règle forte : **chaque ticket** doit avoir un **premier élément lisible** dans la timeline qui représente la **demande initiale**, quel que soit le canal.

| Canal | Premier élément timeline recommandé |
|--------|-------------------------------------|
| Plateforme | Texte saisi à la création |
| E-mail | Corps du mail initial |
| Formulaire | Contenu initial utile ou résumé lisible |
| API | Résumé structuré ou message « créé via API » avec contenu utile |

Objectif : **la timeline raconte tout depuis le début**, sans fracture entre « description sur la fiche seule » et « discussion qui commence au deuxième message ».

---

## 7. Afficher sur chaque message : nature et origine

Pour chaque entrée de timeline, deux dimensions :

### A. Nature

- public ;
- interne ;
- système.

### B. Origine (canal)

- app ;
- e-mail ;
- api ;
- formulaire ;
- système.

Un bon affichage permet de lire rapidement : **qui parle**, **à qui c’est visible**, **d’où ça vient**.

Exemples de lecture : Client + E-mail ; Support + App ; Système ; Note interne ; API. Pas besoin de surcharger visuellement, mais la **sémantique** doit être disponible.

---

## 8. Pièces jointes : message vs fiche ticket

- Si un fichier est envoyé **avec un message**, il doit être **rattaché visuellement à ce message** (même bloc : texte + image, texte + fichier, galerie, liste de fichiers).
- Si la pièce jointe est un **contexte dossier** (niveau ticket), elle peut vivre au niveau **fiche ticket**, avec libellé explicite en UI : **fichier du ticket** vs **fichier envoyé dans ce message**.

Sinon on perd le contexte.

---

## 9. Statuts et assignations ne doivent pas se faire passer pour des messages

**Conversation** : question / réponse, PJ, e-mail entrant conversationnel.

**Événement** : assignation, changement de statut ou de priorité, réouverture, clôture.

Ces événements sont **dans** la timeline, mais comme **événements système**, pas comme faux messages humains — pour éviter fil bruyant, confusion « quelqu’un a répondu », mélange suivi / conversation.

---

## 10. Règle de réponse simple et stable

- **Personne qui travaille dans la plateforme (interne)** : répond surtout dans l’app ; reçoit surtout des notifications in-app (selon politique produit).
- **Personne externe non connectée** : répond surtout par **e-mail** ; les réponses **reviennent dans le même ticket**.
- **Personne connectée côté client** : peut répondre dans la plateforme ; peut recevoir des e-mails selon les cas.

**Règle centrale** : un seul ticket, une seule discussion, plusieurs canaux possibles, **une seule vérité** dans le fil.

---

## 11. Distinguer « voir le ticket » et « voir l’interne »

Éviter les formulations vagues (« le client ne voit pas l’interne », « l’agent voit tout »). Préciser selon **rôle**, **statut de compte** et **permissions**.

**Public / client (selon périmètre produit)** voit typiquement :

- la fiche ticket utile ;
- les messages publics ;
- ses fichiers / fichiers publics ;
- statuts et notifications utiles.

**Ne voit pas** :

- notes internes ;
- échanges réservés équipe ;
- détails techniques de routage ;
- informations sensibles.

**Interne** voit davantage selon **permissions** : messages publics, e-mail dans le fil, événements système, notes internes, audit.

Tout est **gouverné** par rôle, statut et permissions — pas seulement par l’étiquette « client ».

---

## 12. Points à verrouiller (checklist produit / technique)

1. **Demande initiale** toujours visible comme premier élément clair de timeline.
2. **Séparation nette** : fiche ticket / message / événement système / note interne.
3. **Identification** : source du **ticket** et source du **message** (canal).
4. **Distinction** affichée et en données : demandeur / créateur technique / assigné / acteur d’action.
5. **Règles stables** : qui répond par e-mail, qui dans l’app, qui reçoit quoi (notifications).
6. **Lisibilité timeline** : en quelques secondes, savoir qui parle, d’où vient le message, public ou interne, événement ou vraie réponse.

---

## 13. Synthèse en une phrase

**Le ticket est la fiche du dossier, la timeline est l’histoire du dossier, et chaque élément de cette timeline indique clairement sa nature, son origine, sa visibilité et son auteur.**

---

## 14. Organisation idéale (référence)

### Fiche ticket contient

Identité, source, demandeur, statut, priorité, catégorie, assignation, équipe, métadonnées utiles, PJ de contexte.

### Timeline contient

Demande initiale, messages publics, messages e-mail, messages API, événements système, notes internes.

### Chaque élément de timeline affiche (données + UI)

Auteur, date, type, origine, visibilité, pièces jointes liées.

### Réponses

Interne → plateforme ; externe non connecté → e-mail ; **tout** revient dans le **même** ticket.

---

## Conclusion

Cap produit :

- **un ticket = une fiche claire** ;
- **une timeline = une histoire complète** ;
- pas de mélange entre message, événement et interne ;
- pas de confusion entre créateur, demandeur, assigné et acteur ;
- **une seule vérité** quel que soit le canal.

---

*Document rédigé pour le cahier des charges Manexo — à rapprocher de `explication.md` pour l’état actuel de l’implémentation.*
