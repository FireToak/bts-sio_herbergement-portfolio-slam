# Cahier des charges : Infrastructure d'hébergement Web

![Bannière BTS SIO](../assets/banniere_bts-sio.png)

## Informations

- **Auteur :** Louis MEDO
- **Date de création :** 25/03/2026

---
## Contexte

Déploiement et sécurisation d'un serveur web Apache2 sur Debian 12 pour héberger les portfolios étudiants. La configuration applique le principe du moindre privilège (fichiers en lecture seule pour le service web), isole chaque projet via des VirtualHosts dédiés, et renforce la surface d'attaque avec des en-têtes HTTP stricts et un chiffrement TLS automatisé.

---
## Sommaire

1. Installation du serveur Web
2. Sécurité applicative (En-têtes HTTP)
3. Création de l'arborescence et cloisonnement des droits
4. Configuration du VirtualHost étudiant
5. Chiffrement TLS (Let's Encrypt)

---
## 1. Installation du serveur Web

1. **Installation d'Apache2.** Mise à jour des paquets et installation du service web principal.

    ```bash
    sudo apt update
    sudo apt install -y apache2
    ```

    `apt update` : Met à jour la liste des paquets disponibles sur les dépôts Debian.

    `apt install` : Installe le paquet spécifié.

    `-y` : Valide automatiquement les demandes de confirmation durant l'installation.

2. **Désactivation du VirtualHost par défaut.** Suppression de la page d'accueil d'installation d'Apache pour éviter d'exposer des informations sur le serveur et s'assurer que seuls les sites configurés répondent.

    ```Bash
    sudo a2dissite 000-default.conf
    sudo systemctl reload apache2
    ```

    `a2dissite (Apache2 Disable Site)` : Désactive un site en supprimant le lien symbolique correspondant dans le répertoire `/etc/apache2/sites-enabled/`.

    `systemctl reload` : Demande au service de recharger sa configuration à chaud, ce qui applique la désactivation sans interrompre les autres sites en cours d'exécution.

---
## 2. Sécurité applicative (En-têtes HTTP)

1. **Activation du module headers.** Permet à Apache de manipuler les en-têtes des requêtes et réponses HTTP.

    ```bash
    sudo a2enmod headers
    ```

    `a2enmod` (Apache2 Enable Module) : Crée un lien symbolique pour activer un module spécifique.

2. **Configuration des en-têtes de sécurité.** Édition du fichier de configuration global pour appliquer les directives de sécurité du cahier des charges.

    ```bash
    sudo nano /etc/apache2/conf-available/security.conf
    ```

    *Ajouter à la fin du fichier :*

    ```apache
    # Force le navigateur à utiliser HTTPS
    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains"
    # Empêche le navigateur de deviner le type MIME (protection contre le sniffing)
    Header always set X-Content-Type-Options "nosniff"
    # Restreint les sources autorisées à charger des ressources (scripts, images)
    Header always set Content-Security-Policy "default-src 'self';"
    ```

    `Header always set` : Ajoute l'en-tête spécifié à chaque réponse HTTP.

3. **Application des changements.**

    ```bash
    sudo systemctl restart apache2
    ```

    `systemctl restart` : Redémarre complètement le service pour appliquer la configuration globale.

---
## 3. Création de l'arborescence et cloisonnement des droits

1. **Création du dossier projet.** Préparation de l'espace de stockage pour un étudiant.

    ```bash
    sudo mkdir -p /var/www/portfolio_etu_nom
    ```

    `mkdir -p` : Crée le répertoire et ses parents s'ils n'existent pas.

2. **Configuration des permissions.** Application d'un accès en lecture seule pour Apache (`www-data`), empêchant la modification de fichiers par des scripts malveillants.

    ```bash
    sudo chown -R ton_utilisateur_de_deploiement:www-data /var/www/portfolio_etu_nom
    sudo find /var/www/portfolio_etu_nom -type d -exec chmod 750 {} \;
    sudo find /var/www/portfolio_etu_nom -type f -exec chmod 640 {} \;
    ```

    `chown -R` : Change le propriétaire et le groupe récursivement. Ici, ton utilisateur gère les fichiers, le groupe web (`www-data`) y accède.

    `find ... -type d -exec chmod 750` : Cherche les dossiers (`-type d`) et donne les droits de lecture/exécution (7) au propriétaire, et lecture/accès (5) au groupe web. Rien (0) pour les autres.

    `find ... -type f -exec chmod 640` : Cherche les fichiers (`-type f`) et donne les droits de lecture/écriture (6) au propriétaire, et uniquement lecture (4) au groupe web. 

---
## 4. Configuration du VirtualHost étudiant

1. **Création du fichier VHost.** Routage du trafic selon le nom de domaine.

    ```bash
    sudo nano /etc/apache2/sites-available/etu_nom.conf
    ```

    *Contenu :*

    ```apache
    <VirtualHost *:80>
        ServerName etu_nom.domaine_vps.ext
        DocumentRoot /var/www/portfolio_etu_nom

        <Directory /var/www/portfolio_etu_nom>
            AllowOverride None
            Require all granted
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/etu_nom_error.log
        CustomLog ${APACHE_LOG_DIR}/etu_nom_access.log combined
    </VirtualHost>
    ```

    `<VirtualHost *:80>` : Définit la configuration pour le port HTTP (80).

    `ServerName` : Le sous-domaine spécifique à l'étudiant.

    `DocumentRoot` : Le chemin vers les fichiers du site.

    `AllowOverride None` : Désactive l'utilisation des fichiers `.htaccess` pour des raisons de performance et de sécurité centralisée.

2. **Activation du site.**

    ```bash
    sudo a2ensite etu_nom.conf
    sudo systemctl reload apache2
    ```

    `a2ensite` (Apache2 Enable Site) : Active le VirtualHost.

    `systemctl reload` : Recharge la configuration à chaud sans interrompre les connexions en cours.

---
## 5. Chiffrement TLS (Let's Encrypt)

1. **Installation de Certbot.** Outil officiel pour automatiser la gestion des certificats.

    ```bash
    sudo apt install -y certbot python3-certbot-apache
    ```

2. **Génération et configuration du certificat.**

    ```bash
    sudo certbot --apache -d etu_nom.domaine_vps.ext
    ```

    `certbot --apache` : Lance Certbot avec le plugin Apache. Il va automatiquement valider le domaine, générer le certificat TLS, créer la configuration VHost pour le port 443 (HTTPS) et configurer la redirection de HTTP vers HTTPS.

    `-d` : Spécifie le domaine à certifier.