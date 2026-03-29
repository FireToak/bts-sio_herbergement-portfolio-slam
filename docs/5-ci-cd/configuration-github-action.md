# Procédure : Configuration du pipeline CI/CD GitHub Actions

![Bannière BTS SIO](../assets/banniere_bts-sio.png)

## Informations

  - **Mainteneur :** Louis MEDO
  - **Date de création :** 29/03/2026

---

## Contexte

Ce document décrit la configuration du dépôt pour automatiser le déploiement continu (CI/CD) des portfolios étudiants. Il permet à GitHub d'initier une connexion sécurisée vers le serveur virtuel via une clé SSH restreinte afin de déclencher la mise à jour du code, sans intervention manuelle de l'enseignant ou des étudiants sur le serveur de production.

---

## Sommaire

1.  Configuration des secrets cryptographiques sur GitHub
2.  Création du fichier de workflow d'automatisation (CI/CD)

---

## 1. Configuration des secrets cryptographiques sur GitHub

1.  **Enregistrement des identifiants de connexion.** Afin de ne jamais exposer d'informations sensibles dans le code source du projet, vous devez stocker l'adresse du serveur et la clé de sécurité dans le gestionnaire de secrets de GitHub. Naviguez dans l'interface web de votre dépôt : 

    **Settings \> Secrets and variables \> Actions \> New repository secret**.

    - **VPS_HOST** : Créez ce secret et renseignez l'adresse IP publique du serveur Infomaniak comme valeur.

    - **SSH_PRIVATE_KEY** : Créez ce secret et collez-y l'intégralité de la clé privée Ed25519 (qui correspond à la clé publique préalablement configurée avec le `ForceCommand` sur le compte `deployer` du VPS).

## 2. Création du fichier de workflow d'automatisation (CI/CD)

1.  **Création du pipeline de déploiement YAML.** Dans le dossier racine du projet de l'étudiant, créez l'arborescence requise par GitHub et ajoutez le fichier de configuration définissant les actions à exécuter lors d'un envoi de code.

    ```bash
    mkdir -p .github/workflows
    nano .github/workflows/deploy.yml
    ```

    `mkdir -p` : Crée le répertoire `.github` et son sous-répertoire `workflows`. L'option `-p` (parents) permet de créer toute l'arborescence d'un coup sans générer d'erreur si elle existe déjà.

    `nano` : Ouvre l'éditeur de texte en ligne de commande pour créer ou modifier le fichier `deploy.yml`.

2.  **Déclaration des instructions du pipeline.** Insérez le code suivant dans le fichier `deploy.yml`. Ce code instruit les serveurs de GitHub de se connecter au VPS et de lancer la synchronisation.

    ```yaml
    name: Déploiement du Portfolio
    on:
      push:
        branches:
          - main
    jobs:
      deploy:
        runs-on: ubuntu-latest
        steps:
          - name: Connexion SSH et récupération du code
            uses: appleboy/ssh-action@v1.0.3
            with:
              host: ${{ secrets.VPS_HOST }}
              username: deployer
              key: ${{ secrets.SSH_PRIVATE_KEY }}
              script: |
                echo "Déploiement déclenché. Le ForceCommand du VPS prend le relais."
    ```

    `on: push: branches: - main` : Notion de "Trigger" (Déclencheur). Le pipeline s'exécute automatiquement et uniquement lorsqu'une modification est poussée sur la branche principale (`main`).

    `runs-on: ubuntu-latest` : Alloue un environnement éphémère Linux (Runner) chez GitHub pour exécuter ce script.

    `uses: appleboy/ssh-action` : Fait appel à une action préconçue (open source) simplifiant l'établissement de la connexion SSH depuis le runner GitHub vers un serveur externe.

    `script: |` : Normalement utilisé pour envoyer des commandes au serveur. Ici, en raison du blocage `ForceCommand` strict configuré précédemment sur le VPS, la commande envoyée par GitHub est ignorée. Dès que la connexion SSH réussit, le VPS force l'exécution du `git pull` prévu dans son fichier `authorized_keys`.

---

## Annexe

  - [Documentation officielle d'appleboy/ssh-action](https://github.com/appleboy/ssh-action)

---