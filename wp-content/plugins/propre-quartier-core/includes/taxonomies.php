<?php
/**
 * Taxonomie : Categorie de probleme (ordures, eau, eclairage...).
 * Permet de filtrer et de colorer les signalements par type.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pq_register_taxonomies() {
	register_taxonomy( 'categorie_probleme', 'signalement', array(
		'labels'            => array(
			'name'          => 'Categories de probleme',
			'singular_name' => 'Categorie',
			'menu_name'     => 'Categories',
			'all_items'     => 'Toutes les categories',
			'edit_item'     => 'Modifier la categorie',
			'add_new_item'  => 'Ajouter une categorie',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'categorie' ),
	) );
}
add_action( 'init', 'pq_register_taxonomies' );

/**
 * Categories par defaut, creees a l'activation du plugin.
 */
function pq_seed_terms() {
	$defaults = array(
		'Ordures / depot sauvage',
		'Eau / assainissement',
		'Eclairage public',
		'Voirie / nids-de-poule',
		'Espaces verts',
		'Autre',
	);
	foreach ( $defaults as $term ) {
		if ( ! term_exists( $term, 'categorie_probleme' ) ) {
			wp_insert_term( $term, 'categorie_probleme' );
		}
	}
}
