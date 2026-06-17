#!/bin/sh
# =============================================================================
# Installation automatique de Propre Quartier via WP-CLI.
# Idempotent : peut etre relance sans casser un site deja installe.
# Lance par :  docker compose run --rm wpcli
# =============================================================================
set -e

echo "==> Attente de la base de donnees..."
until wp db check --quiet 2>/dev/null; do
  sleep 3
  echo "    ... base pas encore prete, nouvelle tentative"
done

# -----------------------------------------------------------------------------
# Installation du coeur WordPress (si pas deja fait)
# -----------------------------------------------------------------------------
if wp core is-installed 2>/dev/null; then
  echo "==> WordPress est deja installe, on saute l'install du coeur."
else
  echo "==> Installation de WordPress..."
  wp core install \
    --url="${WP_URL:-http://localhost:8080}" \
    --title="${WP_SITE_TITLE:-Propre Quartier}" \
    --admin_user="${WP_ADMIN_USER:-admin}" \
    --admin_password="${WP_ADMIN_PASSWORD:-admin_pq_2025}" \
    --admin_email="${WP_ADMIN_EMAIL:-admin@propre-quartier.sn}" \
    --skip-email
fi

# -----------------------------------------------------------------------------
# Langue : francais
# -----------------------------------------------------------------------------
echo "==> Configuration de la langue (fr_FR)..."
wp language core install fr_FR --activate 2>/dev/null || true

# -----------------------------------------------------------------------------
# Plugin metier + theme enfant
# -----------------------------------------------------------------------------
echo "==> Activation du plugin metier et du theme..."
wp plugin activate propre-quartier-core
wp theme activate propre-quartier

# -----------------------------------------------------------------------------
# Reglages de base
# -----------------------------------------------------------------------------
echo "==> Reglages generaux..."
wp option update blogdescription "Signaler les problemes de mon quartier"
wp option update timezone_string "Africa/Dakar"
wp option update date_format "j F Y"
# Permaliens jolis (necessaires pour les CPT et l'API REST)
wp rewrite structure '/%postname%/' --hard
wp rewrite flush --hard

# -----------------------------------------------------------------------------
# Pages essentielles
# -----------------------------------------------------------------------------
echo "==> Creation des pages..."
create_page () {
  TITLE="$1"; CONTENT="$2"; SLUG="$3"
  if ! wp post list --post_type=page --field=post_name | grep -qx "$SLUG"; then
    wp post create --post_type=page --post_status=publish \
      --post_title="$TITLE" --post_name="$SLUG" --post_content="$CONTENT"
  fi
}

create_page "Accueil" "[pq_carte][pq_signalements]" "accueil"
create_page "Signaler un probleme" "[pq_formulaire]" "signaler"
create_page "Carte des signalements" "[pq_carte hauteur=560]" "carte"
create_page "A propos" "Propre Quartier permet aux habitants de signaler les problemes de proprete et de voirie de leur quartier." "a-propos"

# Contenu complet de la page A propos depuis un fichier (mieux que de l'inline)
APID=$(wp post list --post_type=page --name=a-propos --field=ID | head -n1)
if [ -n "$APID" ] && [ -f /bin/pq/about.html ]; then
  wp post update "$APID" /bin/pq/about.html >/dev/null
fi

# Page d'accueil statique
HOME_ID=$(wp post list --post_type=page --name=accueil --field=ID | head -n1)
if [ -n "$HOME_ID" ]; then
  wp option update show_on_front page
  wp option update page_on_front "$HOME_ID"
fi

# -----------------------------------------------------------------------------
# Menu principal
# -----------------------------------------------------------------------------
echo "==> Construction du menu..."
if ! wp menu list --fields=slug --format=csv 2>/dev/null | grep -qx "principal"; then
  wp menu create "Principal"

  # Accueil + Signaler (pages), libelles courts
  for entry in "accueil:Accueil" "signaler:Signaler"; do
    SLUG=${entry%%:*}; LABEL=${entry##*:}
    PID=$(wp post list --post_type=page --name="$SLUG" --field=ID | head -n1)
    [ -n "$PID" ] && wp menu item add-post principal "$PID" --title="$LABEL" >/dev/null
  done

  # Signalements -> archive du CPT (la page "voir tout" : carte + liste complete)
  wp menu item add-custom principal "Signalements" "${WP_URL%/}/signalements/" >/dev/null

  # A propos
  PID=$(wp post list --post_type=page --name="a-propos" --field=ID | head -n1)
  [ -n "$PID" ] && wp menu item add-post principal "$PID" --title="À propos" >/dev/null

  wp menu location assign principal primary 2>/dev/null || true
fi

# -----------------------------------------------------------------------------
# Quelques signalements de demonstration
# -----------------------------------------------------------------------------
echo "==> Donnees de demonstration..."
if [ "$(wp post list --post_type=signalement --format=count)" = "0" ]; then
  # add  titre | lat | lng | adresse | statut | categorie(slug) | description
  add_demo() {
    pid=$(wp post create --post_type=signalement --post_status=publish --porcelain \
      --post_title="$1" --post_content="$7")
    wp post meta update "$pid" pq_lat "$2" >/dev/null
    wp post meta update "$pid" pq_lng "$3" >/dev/null
    wp post meta update "$pid" pq_adresse "$4" >/dev/null
    wp post meta update "$pid" pq_statut "$5" >/dev/null
    wp post term set "$pid" categorie_probleme "$6" >/dev/null
  }

  add_demo "Tas d ordures rue 10"            14.6928 -17.4467 "Rue 10, Medina"            signale  ordures-depot-sauvage  "Depot sauvage au coin de la rue 10, present depuis une semaine."
  add_demo "Lampadaire en panne"             14.7100 -17.4600 "Avenue Bourguiba"          en_cours eclairage-public       "Eclairage public hors service, carrefour dangereux la nuit."
  add_demo "Fuite d eau reparee"             14.6800 -17.4400 "Rue Carnot"                resolu   eau-assainissement     "La fuite sur la canalisation principale a ete reparee."
  add_demo "Poubelles debordantes au marche" 14.6760 -17.4390 "Marche Tilene"             signale  ordures-depot-sauvage  "Les poubelles du marche debordent depuis plusieurs jours."
  add_demo "Caniveau bouche"                 14.7050 -17.4520 "Grand Yoff"                en_cours eau-assainissement     "Eau stagnante a cause d un caniveau bouche."
  add_demo "Lampadaires eteints"             14.6850 -17.4630 "Avenue Cheikh Anta Diop"   signale  eclairage-public       "Toute l avenue est dans le noir la nuit."
  add_demo "Nid-de-poule dangereux"          14.7200 -17.4670 "Patte d Oie"               en_cours voirie-nids-de-poule    "Gros trou sur la chaussee, accidents frequents."
  add_demo "Parc public a l abandon"         14.6920 -17.4570 "Point E"                   signale  espaces-verts          "Le petit parc est envahi par les herbes et les dechets."
  add_demo "Depot sauvage enleve"            14.6680 -17.4350 "Rue Carnot"                resolu   ordures-depot-sauvage  "Le depot a ete enleve par les services municipaux."
  add_demo "Fuite sur borne fontaine"        14.7320 -17.4560 "Camberene"                 signale  eau-assainissement     "Une borne fontaine fuit en continu."
  add_demo "Trottoir effondre"               14.6990 -17.4480 "Medina"                    resolu   voirie-nids-de-poule    "Le trottoir a ete refait."
fi

echo ""
echo "============================================================"
echo " Propre Quartier est pret !"
echo "   Site  : ${WP_URL:-http://localhost:8080}"
echo "   Admin : ${WP_URL:-http://localhost:8080}/wp-admin"
echo "   User  : ${WP_ADMIN_USER:-admin} / ${WP_ADMIN_PASSWORD:-admin_pq_2025}"
echo "============================================================"
