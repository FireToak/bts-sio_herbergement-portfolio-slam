# Création des dépôts étudiants dans l'organisation GitHub

![Bannière BTS SIO](../assets/banniere_bts-sio.png)

## Informations

  - **Mainteneur :** Louis MEDO
  - **Date de création :** 29/03/2026

-----

## Contexte

L'objectif de cette procédure est de standardiser la création des environnements de déploiement pour les étudiants en BTS SLAM. En s'appuyant sur l'automatisation via le modèle `template_portfolio`, nous garantissons une base saine et reproductible incluant les workflows de déploiement. L'intégration des secrets d'organisation (`SSH_PRIVATE_KEY`, `VPS_HOST`) permet une connexion sécurisée de la chaîne CI/CD vers le serveur VPS cible sans exposer de données sensibles dans le code source.

-----

## 1. Création d'un dépôt GitHub depuis l'organisation

1.  **Création depuis le modèle standardisé**. Cette action permet de cloner l'architecture de base et la pipeline CI/CD de manière automatisée afin d'éviter toute erreur de configuration manuelle.

      - Naviguez sur la page d'accueil de l'organisation GitHub.
      - Cliquez sur le bouton **New** depuis l'onglet *Repositories*.
      - Dans la section **Repository template**, sélectionnez `template_portfolio` dans le menu déroulant.
      - Renseignez le nom du dépôt en respectant une nomenclature claire : `portfolio-nom-prenom`.
      - Laissez la visibilité sur **Public** pour que l'étudiant puisse montrer son travail.
      - Cliquez sur **Create repository**.

## 2. Injection des secrets pour la pipeline CI/CD

1.  **Attribution des accès aux secrets d'organisation**. Afin de respecter les normes de sécurité en vigueur, les identifiants de connexion au VPS sont gérés au niveau de l'organisation. Il faut explicitement autoriser ce nouveau dépôt privé à les lire lors de l'exécution de sa pipeline.

      - Naviguez dans les **Settings** (Paramètres) de l'organisation GitHub.
      - Dans le menu latéral gauche, allez dans **Secrets and variables** > **Actions**.
      - Localisez le secret `SSH_PRIVATE_KEY`.
      - Cliquez sur l'icône d'engrenage (*Update*) à côté du secret.
      - Dans la section *Repository access*, assurez-vous que **Selected repositories** est actif, cliquez sur l'icône d'engrenage, puis recherchez et cochez le nouveau dépôt de l'étudiant.
      - Répétez l'opération complète pour le secret `VPS_HOST`.
      - Sauvegardez les modifications.

## 3. Vérification de fonctionnement

1.  **Validation de la chaîne de livraison continue (CI/CD)**.

      - Depuis la page principale du dépôt de l'étudiant, cliquez sur **Add file** > **Create new file**.
      - Nommez le fichier `index.php`.
      - Insérez le code de test suivant : [Test CI/CD - Lycée Paul-Louis Courier](./test-ci-cd_index.php)
      - Cliquez sur **Commit changes** pour pousser le fichier sur la branche principale.
      - Rendez-vous dans l'onglet **Actions** du dépôt pour observer l'exécution du workflow.
      - Une fois la pipeline au vert, vérifiez la présence du message sur l'URL publique allouée à l'étudiant sur le VPS.
      - Le test étant validé, supprimez le fichier `index.php` depuis l'interface GitHub et validez la suppression (Commit).

## 4. Ajouter l'étudiant à son dépôt

1.  **Configuration des accès via le principe de moindre privilège**. L'étudiant doit pouvoir travailler de manière autonome sur son code sans risquer d'altérer la configuration système du dépôt (workflows, secrets, ou règles de branche).

      - Naviguez dans les **Settings** du dépôt de l'étudiant.
      - Allez dans la section **Collaborators** (ou *Manage access*).
      - Cliquez sur **Add people**.
      - Recherchez le nom d'utilisateur GitHub exact de l'étudiant.
      - Assignez le rôle **Write** (Écriture). Ce rôle lui octroie les droits de déposer son code (`push`), de le récupérer (`pull`) et de gérer le suivi de projet, tout en bloquant l'accès aux paramètres critiques.
      - Cliquez sur **Add to repository**. L'étudiant devra accepter l'invitation reçue par email.