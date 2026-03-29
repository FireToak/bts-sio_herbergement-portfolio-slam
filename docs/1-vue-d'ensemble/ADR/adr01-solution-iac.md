# ADR 001 : Choix de la solution IaC et Analyse de l'infrastructure

## Informations

  - **Mainteneur :** Louis MEDO
  - **Date de création :** 29/03/2026
  - **Numéro ADR :** 001

---

## Contexte

Ce document justifie le choix de l'outil d'Infrastructure as Code (IaC) utilisé pour automatiser la configuration du serveur web Apache et le déploiement des nouveaux portfolios sur le VPS de l'établissement. L'objectif est de garantir une infrastructure reproductible, sans intervention manuelle risquée, et de maintenir une stricte gestion des configurations systèmes et réseaux.

---

## Sommaire

1.  Analyse de l'infrastructure (Pertinence et Sécurité)
2.  Comparatifs et coûts des solutions IaC
3.  Solution retenue

---

## 1. Présentation des besoins

L'outil de déploiement sélectionné doit permettre de :
- Générer automatiquement les VirtualHosts Apache (fichiers `.conf`) pour chaque sous-domaine étudiant.
- Assurer le cloisonnement strict des dossiers web en appliquant les permissions requises à l'utilisateur `www-data`.
- Automatiser la récupération des dossiers depuis Git.
- S'exécuter de manière économe pour ne pas surcharger la mémoire vive limitée (2GB) du serveur Debian 13.

---

## 2. Comparatifs et coûts des solutions IaC

Pour automatiser la configuration sans intervention manuelle, l'outil IaC doit être léger. Toutes les solutions majeures disposent de versions open source gratuites. Le "coût" se mesure donc en **temps de maintenance** et en **consommation de ressources serveur**.

  - **Scripts Bash personnalisés :**
      - *Coût financier :* 0€.
      - *Coût de maintenance :* Très élevé. Les scripts deviennent vite complexes et ne sont pas nativement **idempotents** (capacité d'un outil à s'exécuter plusieurs fois de suite sans altérer le système s'il est déjà dans l'état souhaité).
  - **Terraform :**
      - *Coût financier :* 0€.
      - *Pertinence :* Inadapté pour ce besoin précis. Terraform est conçu pour provisionner du matériel cloud (créer le VPS lui-même), mais n'est pas fait pour gérer des fichiers de configuration logicielle à l'intérieur de l'OS.
  - **Puppet / Chef :**
      - *Coût financier :* 0€ (versions communautaires).
      - *Coût ressource :* Élevé. Ces solutions nécessitent l'installation d'un "agent" (un programme de surveillance) qui tourne en permanence sur le VPS, ce qui consommerait une partie trop importante des 2GB de RAM disponibles.
  - **Ansible :**
      - *Coût financier :* 0€.
      - *Coût ressource :* Nul sur le VPS. Ansible fonctionne en mode **Agentless** (sans agent). Il se connecte de manière éphémère via le port SSH existant pour exécuter ses tâches, puis se retire.

---

## 3. Solution retenue

**Ansible** est validé comme la solution optimale.

Il garantit un déploiement fiable et automatisé, totalement gratuit, tout en préservant l'intégralité des 2GB de mémoire vive du VPS pour les requêtes web des étudiants. De plus, il s'intègre parfaitement aux restrictions de sécurité déjà en place (passage par le port 22 et authentification Ed25519).

---