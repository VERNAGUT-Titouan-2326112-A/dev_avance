# Manuel Utilisateur - EduLearn

Bienvenue sur EduLearn, votre plateforme d'apprentissage moderne assistée par l'IA.
Ce guide détaille les fonctionnalités disponibles pour les enseignants et les étudiants.

## Espace Professeur (Administration)

L'interface professeur est conçue pour simplifier la création de contenu pédagogique et le suivi des élèves. Elle est gérée via l'interface web classique (Symfony).

### 1. Tableau de Bord

En vous connectant avec un compte enseignant, vous accédez directement à votre tableau de bord qui centralise :

- La gestion des contenus (PDF, Vidéos).

- La génération de QCM.

- Le suivi des résultats des étudiants.

### 2. Gestion des Ressources

Vous pouvez enrichir vos cours avec deux types de supports :

- Ajouter un Document PDF :

    - Cliquez sur le bouton "Ajouter un document PDF".

    - Sélectionnez le cours associé via le menu déroulant.

    - Importez votre fichier et donnez-lui un titre.

**Fonctionnalité clé :** Ces documents serviront de base à l'IA pour générer des questions.

- Ajouter une Vidéo :

    - Cliquez sur "Ajouter une vidéo".

    - Sélectionnez le fichier vidéo (MP4) et le cours associé.

La vidéo sera accessible aux élèves via leur lecteur dédié.

### 3. Génération de QCM par Intelligence Artificielle

C'est le cœur d'EduLearn. Vous n'avez pas besoin d'écrire les questions manuellement.

- Depuis un PDF :

    - Dans la liste de vos documents, cliquez sur le bouton jaune "Configurer & Générer QCM".

    - Une fenêtre s'ouvre pour configurer votre quiz :

        - Nombre de questions : Choisissez entre 1 et 20.

        - Type de questions : "QCM Classique" (4 choix) ou "Vrai / Faux".

        - Cliquez sur "Générer". L'IA (Mistral) va lire le document et créer le quiz automatiquement.

- Depuis une Vidéo :

    - Cliquez sur "Configurer & Générer QCM Vidéo" sous une vidéo.

    - Mêmes options de configuration.

    - L'application va d'abord transcrire l'audio (via Groq/Whisper) puis générer les questions sur le contenu parlé.

### 4. Prévisualisation et Tests

Avant de rendre un quiz disponible, vous pouvez le tester vous-même :

Dans la section "Mes QCMs Générés", cliquez sur "Tester le Quiz".

Vous accédez à une interface de simulation avec correction immédiate pour vérifier la pertinence des questions.

### 5. Suivi des Résultats

En bas de votre tableau de bord, le tableau "Résultats des QCM" vous offre une vue d'ensemble :

- Liste des tentatives : Voyez quel étudiant a passé quel quiz.

- Scores et Mentions : Un système de badges (Excellent, Bien, Moyen, À améliorer) permet d'identifier rapidement les élèves en difficulté.

- Détails : Cliquez sur "Voir détails" pour inspecter les réponses exactes d'un étudiant (quelles questions ont été ratées).

- Export CSV : Téléchargez les notes pour vos relevés administratifs.

## Espace Étudiant (Application Interactive)

L'espace étudiant est une application fluide et moderne (React) conçue pour l'apprentissage en autonomie.

### 1. Accès et Navigation

Connexion : Connectez-vous avec vos identifiants ou créez un compte (choix du rôle Étudiant lors de l'inscription).

Navigation : La barre de menu en haut vous permet de basculer entre "Mes Cours" et "Mes Résultats".

### 2. Consulter un Cours

Sur la page "Mes Cours", vous voyez l'ensemble des modules disponibles sous forme de cartes.

Cliquez sur "Accéder au cours" pour entrer dans le module.

Vidéos : Regardez les cours vidéo directement dans la page.

Documents : Lisez les PDF via la visionneuse intégrée ou téléchargez-les pour plus tard.

### 3. Passer un Quiz

Dans la section "Exercices & Quiz" d'un cours :

Cliquez sur "Faire le test".

Répondez aux questions (Vrai/Faux ou Choix Multiples).

Cliquez sur "Terminer le Quiz".

Votre score est calculé instantanément (ex: 15/20) et enregistré définitivement.

### 4. Suivre sa progression

Sur la page "Mes Résultats" :

Retrouvez l'historique de tous vos quiz passés.

Visualisez votre évolution grâce aux badges de performance.

Cliquez sur "Voir détails" pour réviser : cela affiche la correction détaillée question par question (vert pour juste, rouge pour faux).