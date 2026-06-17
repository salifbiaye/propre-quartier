<?php
/**
 * Type de contenu personnalise : Signalement.
 * C'est l'objet central de la plateforme : un probleme remonte par un citoyen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pq_register_cpt() {
	$labels = array(
		'name'               => 'Signalements',
		'singular_name'      => 'Signalement',
		'menu_name'          => 'Signalements',
		'add_new'            => 'Ajouter',
		'add_new_item'       => 'Nouveau signalement',
		'edit_item'          => 'Modifier le signalement',
		'new_item'           => 'Nouveau signalement',
		'view_item'          => 'Voir le signalement',
		'search_items'       => 'Rechercher un signalement',
		'not_found'          => 'Aucun signalement',
		'not_found_in_trash' => 'Aucun signalement dans la corbeille',
		'all_items'          => 'Tous les signalements',
	);

	register_post_type( 'signalement', array(
		'labels'       => $labels,
		'public'       => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-flag',
		'menu_position'=> 5,
		'supports'     => array( 'title', 'editor', 'thumbnail', 'author' ),
		'rewrite'      => array( 'slug' => 'signalements' ),
		'show_in_rest' => true, // Expose le CPT a l'API REST et a l'editeur de blocs.
	) );
}
add_action( 'init', 'pq_register_cpt' );
