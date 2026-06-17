<?php
/**
 * Traitement du formulaire public de signalement.
 *
 * Un habitant n'a PAS de compte : il poste via admin-post.php. Le signalement
 * est cree en "pending" (en attente) pour qu'un moderateur valide avant
 * publication -> evite le spam et les abus.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_post_nopriv_pq_nouveau_signalement', 'pq_traiter_formulaire' );
add_action( 'admin_post_pq_nouveau_signalement', 'pq_traiter_formulaire' );

function pq_traiter_formulaire() {
	// Securite : verification du nonce.
	if ( ! isset( $_POST['pq_nonce'] ) || ! wp_verify_nonce( $_POST['pq_nonce'], 'pq_form' ) ) {
		wp_die( 'Echec de la verification de securite.' );
	}

	$titre   = sanitize_text_field( $_POST['pq_titre'] ?? '' );
	$desc    = sanitize_textarea_field( $_POST['pq_description'] ?? '' );
	$adresse = sanitize_text_field( $_POST['pq_adresse'] ?? '' );
	$lat     = sanitize_text_field( $_POST['pq_lat'] ?? '' );
	$lng     = sanitize_text_field( $_POST['pq_lng'] ?? '' );
	$cat     = absint( $_POST['pq_categorie'] ?? 0 );

	$retour = wp_get_referer() ?: home_url( '/signaler/' );

	if ( empty( $titre ) || empty( $desc ) ) {
		wp_safe_redirect( add_query_arg( 'pq', 'erreur', $retour ) );
		exit;
	}

	// Creation du signalement en attente de moderation.
	$post_id = wp_insert_post( array(
		'post_type'    => 'signalement',
		'post_status'  => 'pending',
		'post_title'   => $titre,
		'post_content' => $desc,
	) );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		wp_safe_redirect( add_query_arg( 'pq', 'erreur', $retour ) );
		exit;
	}

	// Metadonnees.
	update_post_meta( $post_id, 'pq_adresse', $adresse );
	update_post_meta( $post_id, 'pq_lat', $lat );
	update_post_meta( $post_id, 'pq_lng', $lng );
	update_post_meta( $post_id, 'pq_statut', 'signale' );

	if ( $cat ) {
		wp_set_post_terms( $post_id, array( $cat ), 'categorie_probleme' );
	}

	// Photo jointe (optionnelle).
	if ( ! empty( $_FILES['pq_photo']['name'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		$attach_id = media_handle_upload( 'pq_photo', $post_id );
		if ( ! is_wp_error( $attach_id ) ) {
			set_post_thumbnail( $post_id, $attach_id );
		}
	}

	wp_safe_redirect( add_query_arg( 'pq', 'merci', $retour ) );
	exit;
}
