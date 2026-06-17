<?php
/**
 * Chargement des ressources front : Leaflet (carte OpenStreetMap, gratuit et
 * sans cle API) + notre script de carte + styles des shortcodes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', function () {
	// Leaflet depuis le CDN officiel.
	wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
	wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );

	// Nos ressources.
	wp_enqueue_style( 'pq-front', PQ_URL . 'assets/pq.css', array( 'leaflet' ), PQ_VERSION );
	wp_enqueue_script( 'pq-carte', PQ_URL . 'assets/pq-carte.js', array( 'leaflet' ), PQ_VERSION, true );

	// Centre par defaut : Dakar.
	wp_localize_script( 'pq-carte', 'PQ_CONFIG', array(
		'centreLat' => 14.6928,
		'centreLng' => -17.4467,
		'zoom'      => 12,
	) );
} );
