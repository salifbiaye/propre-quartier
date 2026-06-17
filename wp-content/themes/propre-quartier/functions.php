<?php
/**
 * Fonctions du theme Propre Quartier (gabarits classiques sur base TT4).
 *
 * Responsabilites :
 *   - chargement des polices et du style
 *   - supports de theme + menus
 *   - SEO (meta description, Open Graph, Twitter Card, JSON-LD schema.org)
 *   - reglages d'affichage (extraits)
 *
 * La logique metier (signalements) vit dans le plugin propre-quartier-core.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------
 * Supports de theme + menus
 * ------------------------------------------------------------------- */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus( array(
		'primary' => 'Menu principal',
		'footer'  => 'Menu pied de page',
	) );
} );

/* ---------------------------------------------------------------------
 * Polices + feuille de style
 * ------------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', function () {
	// Preconnexion pour accelerer le chargement des polices.
	add_action( 'wp_head', function () {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	}, 1 );

	wp_enqueue_style(
		'pq-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400&family=Hanken+Grotesk:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'pq-theme', get_stylesheet_uri(), array( 'pq-fonts' ), '4.5.1' );
}, 20 );

/* ---------------------------------------------------------------------
 * Longueur des extraits
 * ------------------------------------------------------------------- */
add_filter( 'excerpt_length', fn() => 26 );
add_filter( 'excerpt_more', fn() => ' &hellip;' );

/* ---------------------------------------------------------------------
 * Filtrage SERVER-SIDE de l'archive des signalements.
 * Les filtres sont de simples parametres d'URL (?statut=...&categorie=...)
 * lus ici et injectes dans la requete principale. Aucun JavaScript : la page
 * se recharge avec les bons resultats, et la pagination fonctionne nativement.
 * ------------------------------------------------------------------- */
function pq_request_filter_args() {
	$args = array();

	$statut = isset( $_GET['statut'] ) ? sanitize_key( $_GET['statut'] ) : '';
	if ( in_array( $statut, array( 'signale', 'en_cours', 'resolu' ), true ) ) {
		$args['meta_query'] = array(
			array( 'key' => 'pq_statut', 'value' => $statut ),
		);
	}

	$cat = isset( $_GET['categorie'] ) ? sanitize_title( $_GET['categorie'] ) : '';
	if ( $cat ) {
		$args['tax_query'] = array(
			array( 'taxonomy' => 'categorie_probleme', 'field' => 'slug', 'terms' => $cat ),
		);
	}

	return $args;
}

add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( ! $query->is_post_type_archive( 'signalement' ) ) {
		return;
	}
	$query->set( 'posts_per_page', 9 );
	foreach ( pq_request_filter_args() as $key => $value ) {
		$query->set( $key, $value );
	}
} );

/* ---------------------------------------------------------------------
 * Helper : comptage des signalements par statut (pour le hero)
 * ------------------------------------------------------------------- */
function pq_compter_par_statut() {
	$out = array( 'total' => 0, 'signale' => 0, 'en_cours' => 0, 'resolu' => 0 );
	$ids = get_posts( array(
		'post_type'      => 'signalement',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	) );
	$out['total'] = count( $ids );
	foreach ( $ids as $id ) {
		$s = get_post_meta( $id, 'pq_statut', true ) ?: 'signale';
		if ( isset( $out[ $s ] ) ) {
			$out[ $s ]++;
		}
	}
	return $out;
}

/* =====================================================================
 * SEO
 * ===================================================================== */

/**
 * Construit une meta description selon le contexte.
 */
function pq_meta_description() {
	if ( is_singular() ) {
		$post = get_queried_object();
		$txt  = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_strip_all_tags( $post->post_content );
		$txt  = trim( preg_replace( '/\s+/', ' ', $txt ) );
		if ( $txt ) {
			return wp_html_excerpt( $txt, 155, '…' );
		}
	}
	$tagline = get_bloginfo( 'description' );
	return $tagline ?: 'Signalez les problemes de proprete et de voirie de votre quartier et suivez leur resolution sur une carte interactive.';
}

/**
 * Balises meta SEO + Open Graph + Twitter Card.
 */
add_action( 'wp_head', function () {
	$desc  = pq_meta_description();
	$title = wp_get_document_title();
	$url   = ( is_singular() || is_page() ) ? get_permalink() : home_url( add_query_arg( null, null ) );
	$img   = '';

	if ( is_singular() && has_post_thumbnail() ) {
		$img = get_the_post_thumbnail_url( get_queried_object_id(), 'large' );
	}

	echo "\n<!-- SEO Propre Quartier -->\n";
	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	echo '<meta name="robots" content="index, follow, max-image-preview:large">' . "\n";
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );

	// Open Graph
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular() ? 'article' : 'website' );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:locale" content="%s">' . "\n", 'fr_FR' );
	if ( $img ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $img ) );
	}

	// Twitter
	printf( '<meta name="twitter:card" content="%s">' . "\n", $img ? 'summary_large_image' : 'summary' );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	if ( $img ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $img ) );
	}
}, 2 );

/**
 * Donnees structurees JSON-LD.
 *  - Partout : WebSite + Organization
 *  - Signalement : Report avec localisation geographique
 */
add_action( 'wp_head', function () {
	$graph = array();

	$graph[] = array(
		'@type' => 'WebSite',
		'@id'   => home_url( '/#website' ),
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
		'description'   => get_bloginfo( 'description' ),
		'inLanguage'    => 'fr-FR',
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => home_url( '/?s={search_term_string}' ),
			'query-input' => 'required name=search_term_string',
		),
	);

	$graph[] = array(
		'@type' => 'Organization',
		'@id'   => home_url( '/#org' ),
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
		'slogan'=> get_bloginfo( 'description' ),
	);

	if ( is_singular( 'signalement' ) ) {
		$id     = get_queried_object_id();
		$lat    = get_post_meta( $id, 'pq_lat', true );
		$lng    = get_post_meta( $id, 'pq_lng', true );
		$report = array(
			'@type'         => 'Report',
			'@id'           => get_permalink() . '#report',
			'headline'      => get_the_title(),
			'description'   => wp_html_excerpt( wp_strip_all_tags( get_post_field( 'post_content', $id ) ), 200, '…' ),
			'datePublished' => get_the_date( 'c', $id ),
			'dateModified'  => get_the_modified_date( 'c', $id ),
			'inLanguage'    => 'fr-FR',
			'publisher'     => array( '@id' => home_url( '/#org' ) ),
		);
		if ( has_post_thumbnail( $id ) ) {
			$report['image'] = get_the_post_thumbnail_url( $id, 'large' );
		}
		if ( $lat && $lng ) {
			$report['contentLocation'] = array(
				'@type'     => 'Place',
				'name'      => get_post_meta( $id, 'pq_adresse', true ) ?: get_the_title(),
				'geo'       => array(
					'@type'     => 'GeoCoordinates',
					'latitude'  => $lat,
					'longitude' => $lng,
				),
			);
		}
		$graph[] = $report;
	}

	$data = array( '@context' => 'https://schema.org', '@graph' => $graph );
	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}, 3 );
