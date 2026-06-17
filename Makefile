# ============================================================
#  Propre Quartier - commandes de pilotage
#  Usage : make <cible>   (ou les commandes docker compose a la main)
# ============================================================

.PHONY: up down setup logs ps restart clean wpcli permissions reset

## Demarre les conteneurs (db, wordpress, phpmyadmin)
up:
	docker compose up -d
	@echo ">> Site     : http://localhost:8080"
	@echo ">> Admin    : http://localhost:8080/wp-admin"
	@echo ">> phpMyAdmin: http://localhost:8081"

## Installe/configure WordPress (theme, plugin, pages, demo)
setup:
	docker compose run --rm wpcli

## Demarre PUIS installe (premier lancement)
install: up setup

## Arrete les conteneurs (garde les donnees)
down:
	docker compose down

## Logs en direct
logs:
	docker compose logs -f wordpress

## Etat des conteneurs
ps:
	docker compose ps

## Redemarre WordPress
restart:
	docker compose restart wordpress

## Repare les permissions d'ecriture de wp-content (plugins/imports)
permissions:
	docker compose exec wordpress chown -R www-data:www-data /var/www/html/wp-content/ai1wm-backups /var/www/html/wp-content/plugins/all-in-one-wp-migration

## Commande WP-CLI ad-hoc (ex: make wpcli CMD="plugin list")
wpcli:
	docker compose run --rm --entrypoint wp wpcli $(CMD)

## RAZ TOTALE : supprime conteneurs ET volumes (donnees perdues)
reset:
	docker compose down -v
