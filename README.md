# ♻️ Propre Quartier — Plateforme citoyenne de signalement

> Projet **IDA / CMS** — ESP / UCAD
> CMS retenu : **WordPress** (conteneurisé avec Docker)

Propre Quartier permet aux habitants d'un quartier de **signaler des problèmes**
de propreté et de voirie (ordures, fuites d'eau, éclairage public, nids-de-poule…),
de les **localiser sur une carte** et d'en **suivre la résolution** (Signalé →
En cours → Résolu).

---

## 1. Pourquoi WordPress ?

| Critère du cahier des charges | Réponse du projet |
|---|---|
| **Originalité** | Outil de *civic-tech* utile à la communauté, pas un simple site vitrine. |
| **Choix du CMS** | WordPress : portail multi-rubriques, immense écosystème, hébergement gratuit facile, parfait pour un site communautaire. |
| **Niveau de personnalisation** | **Plugin métier maison** (type de contenu, statuts, formulaire, carte) + **thème enfant custom**. Du vrai développement, pas juste de la config. |

Moodle (LMS) a été écarté : il est pensé pour des *cours notés*, pas pour un
portail de signalement.

---

## 2. Architecture

```
projet-ida-cms/
├── docker-compose.yml        # db (MariaDB) + wordpress + phpMyAdmin + wp-cli
├── .env / .env.example       # configuration (ports, mots de passe, URL)
├── Makefile                  # raccourcis (make install, make up, make setup...)
├── bin/
│   └── install.sh            # installation auto via WP-CLI (idempotente)
└── wp-content/
    ├── plugins/propre-quartier-core/   # LOGIQUE MÉTIER (les données)
    │   ├── propre-quartier-core.php
    │   ├── includes/
    │   │   ├── cpt.php          # type de contenu "Signalement"
    │   │   ├── taxonomies.php   # catégories de problèmes
    │   │   ├── meta.php         # lat/lng/adresse/statut + admin
    │   │   ├── form.php         # traitement du formulaire public
    │   │   ├── shortcodes.php   # [pq_carte] [pq_formulaire] [pq_signalements]
    │   │   └── assets.php       # chargement Leaflet + CSS/JS
    │   └── assets/ (pq.css, pq-carte.js)
    └── themes/propre-quartier/         # PRÉSENTATION (thème enfant TT4)
        ├── style.css
        └── functions.php
```

**Séparation propre** : les *données* (signalements) vivent dans le **plugin**,
l'*apparence* dans le **thème**. On peut changer de thème sans perdre les données.

---

## 3. Démarrage rapide

**Pré-requis** : Docker Desktop installé et lancé.

```bash
# 1. Copier la config (les valeurs par défaut marchent telles quelles)
cp .env.example .env

# 2. Démarrer + installer en une commande
make install
#   équivaut à :
#   docker compose up -d
#   docker compose run --rm wpcli
```

Puis ouvrir :

| Service | URL | Identifiants |
|---|---|---|
| **Site** | http://localhost:8080 | — |
| **Admin WordPress** | http://localhost:8080/wp-admin | `admin` / `admin_pq_2025` |
| **phpMyAdmin** | http://localhost:8081 | `pq_user` / `pq_dev_pass` |

> Sans `make`, utiliser directement les commandes `docker compose` indiquées.

L'installation crée automatiquement : le thème + plugin activés, les pages
(Accueil, Signaler, Carte, À propos), le menu, 6 catégories et 3 signalements
de démonstration.

---

## 4. Commandes utiles

```bash
make up        # démarrer les conteneurs
make setup     # (ré)installer / configurer WordPress
make logs      # voir les logs WordPress
make ps        # état des conteneurs
make down      # arrêter (les données restent)
make reset     # tout supprimer (conteneurs + données)
make permissions # réparer les permissions wp-content après ajout/import de plugins

# WP-CLI à la volée :
make wpcli CMD="plugin list"
make wpcli CMD="post list --post_type=signalement"
```

---

## 5. Fonctionnalités

- 🗺️ **Carte interactive** (Leaflet + OpenStreetMap, sans clé API) avec marqueurs
  colorés selon le statut.
- 📝 **Formulaire public** : un habitant signale sans compte ; clic sur la carte
  pour géolocaliser, photo optionnelle. Soumission **modérée** (anti-spam).
- 🏷️ **Catégories** de problèmes et **statuts de suivi** (Signalé / En cours / Résolu).
- 🔎 **Filtres (statut + catégorie) et pagination 100 % côté serveur** sur la page
  Signalements : via `pre_get_posts` + paramètres d'URL, sans JavaScript. L'accueil
  n'affiche que les 6 derniers ; la page « Signalements » liste et filtre tout.
- 🔒 Sécurité : *nonces*, capacités, échappement/assainissement systématiques.
- 📱 Interface responsive.

---

## 6. Déploiement (étape suivante)

Deux options, au choix :

1. **Docker Compose sur un serveur** (VPS / hébergeur cloud gratuit) : copier le
   projet, ajuster `.env` (URL publique, mots de passe forts, `WP_DEBUG=0`),
   `make install`, puis un reverse-proxy + HTTPS.
2. **Hébergeur WordPress gratuit** (ex. InfinityFree, 000webhost) : importer le
   thème + le plugin (`wp-content/...`) et la base. Le code est portable tel quel.

> Détail du déploiement à finaliser ensemble selon l'hébergeur choisi.
