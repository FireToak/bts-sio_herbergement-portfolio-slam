# Création d'un utilisateur nominatif et configuration d'une clé SSH

## Informations

  - **Mainteneur :** Louis MEDO
  - **Date de création :** 28/03/2026

---

## Contexte

Cette procédure explique comment créer un compte administrateur nominatif sur le VPS Debian 13 et sécuriser son accès via une authentification par clé SSH asymétrique avec passphrase. Cette approche garantit la traçabilité des actions et bloque les attaques par force brute (mot de passe).

---

## Sommaire

1.  Création du compte utilisateur et attribution des droits
2.  Génération de la paire de clés cryptographiques (Poste client)
3.  Déploiement de la clé publique (Fichier authorized_keys)

---

## 1. Création du compte utilisateur et attribution des droits

1.  **Créer le nouvel utilisateur.** Connectez-vous au serveur avec un compte disposant des droits administrateur pour générer l'espace de travail du nouvel utilisateur.

    ```bash
    sudo adduser <nom-prenom>
    ```

    `sudo` : Exécute la commande avec les privilèges super-administrateur (root).

    `adduser` : Script interactif qui crée l'utilisateur, son répertoire personnel (`/home/<nomprenom>`) et demande la configuration d'un mot de passe initial.

    `<nomprenom>` : L'identifiant cible (convention : prénom + nom).

2.  **Accorder les privilèges d'administration.** Le nouvel utilisateur doit pouvoir exécuter des tâches de maintenance.

    ```bash
    sudo usermod -aG sudo louismedo
    ```

    `usermod` : Utilitaire de modification d'un compte utilisateur existant.

    `-aG` : L'option `-a` (append) ajoute l'utilisateur au groupe spécifié par `-G` sans le retirer de ses groupes actuels.

    `sudo` : Le nom du groupe accordant les droits d'élévation de privilèges sur Debian.

---

## 2. Génération de la paire de clés cryptographiques (Poste client)

1.  **Générer la clé SSH.** Cette action doit être réalisée sur la machine physique de l'utilisateur (le client), et **non sur le serveur**.

    ```bash
    ssh-keygen -t ed25519 -C "louis.medo@bts-sio.eu"
    ```

    `ssh-keygen` : Outil de création des paires de clés d'authentification.

    `-t ed25519` : Spécifie l'algorithme de chiffrement. Ed25519 est la norme actuelle (plus rapide et sécurisée que RSA).

    `-C` : Ajoute un commentaire à la clé (souvent l'email) pour l'identifier facilement dans les logs.

2.  **Sécuriser la clé.** L'outil demandera où sauvegarder la clé (laissez par défaut) et vous invitera à saisir une **passphrase**. Il s'agit d'un mot de passe chiffrant votre clé privée locale en cas de vol de votre ordinateur.

---

## 3. Déploiement de la clé publique (Fichier authorized_keys)

1.  **Préparer le répertoire SSH sur le serveur.** Basculez sur le compte du nouvel utilisateur pour créer son dossier de sécurité avec les bonnes permissions.

    ```bash
    sudo su - lmedo
    mkdir -p ~/.ssh
    chmod 700 ~/.ssh
    ```

    `su -` : Substitue l'utilisateur actuel par celui spécifié et charge son environnement de travail (`-`).

    `mkdir -p` : Crée le dossier `.ssh` (s'il n'existe pas déjà).

    `~` : Raccourci désignant le répertoire personnel de l'utilisateur (ex: `/home/lmedo`).

    `chmod 700` : Restreint les droits du dossier (Lecture/Écriture/Exécution uniquement pour le propriétaire). Le service SSH exige cette restriction.

2.  **Ajouter la clé publique.** Copiez le contenu de votre fichier local `id_ed25519.pub` et collez-le dans le fichier des clés autorisées du serveur.

    ```bash
    vim ~/.ssh/authorized_keys
    chmod 600 ~/.ssh/authorized_keys
    ```

    `vim` : Ouvre l'éditeur de texte pour coller la clé publique.

    `authorized_keys` : Fichier système lu par le service SSH contenant la liste des clés publiques acceptées pour ce compte.

    `chmod 600` : Restreint les droits du fichier (Lecture/Écriture uniquement pour le propriétaire).

---

## Annexe

  - [Documentation officielle Debian - SSH](https://wiki.debian.org/fr/SSH)