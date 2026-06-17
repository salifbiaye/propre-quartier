<?php
/**
 * Plugin Name:       Propre Quartier - Core
 * Description:        Logique metier de la plateforme de signalement citoyen : type de contenu "Signalement", categories de problemes, statuts de suivi, formulaire public et carte interactive.
 * Version:           1.0.0
 * Author:            Groupe IDA - ESP/UCAD
 * Text Domain:       propre-quartier
 * Requires PHP:      8.0
 *
 * On regroupe ici tout ce qui touche aux DONNEES (structure, stockage,
 * traitement). L'affichage pur, lui, vit dans le theme. Cette separation
 * garantit que si on change de theme un jour, les signalements ne sont pas
 * perdus.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Pas d'acces direct.
}

define( 'PQ_VERSION', '1.1.0' );
define( 'PQ_PATH', plugin_dir_path( __FILE__ ) );
define( 'PQ_URL', plugin_dir_url( __FILE__ ) );

// Chargement des modules.
require_once PQ_PATH . 'includes/cpt.php';
require_once PQ_PATH . 'includes/taxonomies.php';
require_once PQ_PATH . 'includes/meta.php';
require_once PQ_PATH . 'includes/form.php';
require_once PQ_PATH . 'includes/shortcodes.php';
require_once PQ_PATH . 'includes/assets.php';

/**
 * A l'activation du plugin, on (re)declare nos types puis on rafraichit les
 * permaliens : indispensable pour que les URL des signalements fonctionnent.
 */
register_activation_hook( __FILE__, function () {
	pq_register_cpt();
	pq_register_taxonomies();
	pq_seed_terms();
	flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function () {
	flush_rewrite_rules();
} );
