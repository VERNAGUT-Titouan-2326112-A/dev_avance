# Documentation Technique - EduLearn

Ce document détaille les étapes d'installation, de configuration et les commandes utiles pour le développement et le déploiement du projet.

## 1. Configuration de l'Environnement (.env)

À la racine du projet, dupliquez le fichier .env en .env.local (ce fichier ne doit pas être commité).
Renseignez les variables suivantes :

```dotenv
# --- Base de Données ---
# Adaptez l'utilisateur (root), le mot de passe et le port (3306) selon votre config locale
DATABASE_URL="mysql://root:@127.0.0.1:3306/edulearn_db?serverVersion=8.0.32&charset=utf8mb4"

# --- Intelligence Artificielle (Génération de QCM) ---
# Clé pour la génération de texte (Questions/Réponses)
MISTRAL_API_KEY=votre_cle_mistral_ici

# Clé pour la transcription vidéo (Whisper via Groq)
GROQ_API_KEY=votre_cle_groq_ici
```

## 2. Installation des Dépendances

Le projet est hybride (Symfony pour le Back-office Professeur, React pour le Front-office Étudiant). Il nécessite donc deux gestionnaires de paquets.
```bash
# Backend (PHP / Symfony)
composer install

# Frontend (JavaScript / React / Tailwind)
npm install
# ou
yarn install
```

## 3. Initialisation de la Base de Données

Une fois le .env.local configuré, lancez ces commandes dans l'ordre :
```bash
# Création de la base de données :
php bin/console doctrine:database:create

# Création des tables (Migrations) :
php bin/console doctrine:migrations:migrate
#(Répondre "yes" à la confirmation)

# Chargement des données de test (Fixtures) :
# Crée des utilisateurs (Profs, Étudiants), des Cours et des QCMs par défaut.
php bin/console doctrine:fixtures:load
```

## 4. Compilation des Assets (React & Tailwind)

L'application utilise Webpack Encore pour compiler le code React et le CSS Tailwind.

En développement (Compilation en temps réel) :
À laisser tourner dans un terminal pendant que vous codez.
```bash
npm run watch
```
En production (Build optimisé et minifié) :
À lancer avant le déploiement ou si vous rencontrez des bugs d'affichage.
```bash
npm run build
```

Note importante : Si l'interface Étudiant ne s'affiche pas ou si le CSS semble cassé, lancez npm run build et videz le cache de votre navigateur (Ctrl + F5).

## 5. Architecture du Projet

Comprendre la structure hybride est essentiel pour la maintenance :

### Backend (Symfony)

**src/Entity/ :** Contient les modèles de données (User, Course, Quiz, etc.) configurés avec API Platform pour l'exposition JSON.

**src/Controller/ :**
- TeacherController.php : Gère le tableau de bord Professeur (Vue Twig classique).
- QuizController.php : Gère la logique de génération de QCM via l'IA.
- SecurityController.php : Gère l'authentification et la redirection selon le rôle.

**src/Service/QuizGeneratorService.php :** Le service qui communique avec les API Mistral et Groq.

### Frontend (React)

Le code React se trouve dans le dossier assets/ et non dans src/.

**assets/student/ :** Contient toute l'application React pour les étudiants.

**index.js :** Point d'entrée et Routeur React.

**src/pages/ :** Les vues (Courses, Quiz, Résultats).

**src/components/ :** Les composants réutilisables (Cartes, VideoPlayer, PDFViewer).

**src/api/ :** Configuration Axios pour communiquer avec API Platform.

### Templates (Vues)

**templates/teacherBase.html.twig :** Layout pour l'interface Professeur.

**templates/User/Professor/ :** Vues Twig pour le prof.

**templates/User/student/index.html.twig :** Vue "coquille" vide qui charge l'application React.

## 6. Lancement du Serveur

Pour démarrer le projet en local :

Lancer le serveur Symfony :
```symfony server:start```

## 7. Reconstruire le projet
Pour reconstruire le projet en local :

Côté Symfony :
```symfony console cache:clear```

Côté React :
```npm run build```
