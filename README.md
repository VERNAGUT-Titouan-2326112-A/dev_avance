# 🎓 EduLearn - Plateforme d'Apprentissage Moderne

EduLearn est une application web hybride conçue pour faciliter la gestion et l'apprentissage de cours en ligne. Elle combine la robustesse de **Symfony** pour l'administration (Professeurs) et la fluidité de **React** pour l'expérience utilisateur (Étudiants).

## 🚀 Technologies

* **Backend & Back-office :** Symfony 6/7, Twig, MySQL
* **Frontend Étudiant :** React.js 18, Tailwind CSS
* **Build :** Webpack Encore

## 📋 Prérequis

* PHP 8.2+
* Composer
* Node.js (18+) & NPM/Yarn
* Serveur MySQL/MariaDB
* Symfony CLI (recommandé)

## ⚡ Installation Rapide

1.  **Cloner le projet**
    ```bash
    git clone [https://github.com/votre-username/edulearn.git](https://github.com/votre-username/edulearn.git)
    cd edulearn
    ```

2.  **Backend (Symfony)**
    ```bash
    composer install
    ```

3.  **Frontend (React/Assets)**
    ```bash
    npm install
    # ou
    yarn install
    ```

4.  **Configuration**
    * Dupliquez le fichier `.env` en `.env.local`.
    * Configurez votre base de données (`DATABASE_URL`).

5.  **Lancement**
    * Compilez les assets : `npm run build`
    * Lancez le serveur : `symfony server:start`

---
Pour plus de détails techniques, consultez la [Documentation Technique](DOC_TECHNIQUE.md).
Pour savoir comment utiliser l'application, voir le [Manuel Utilisateur](MANUEL_UTILISATEUR.md).