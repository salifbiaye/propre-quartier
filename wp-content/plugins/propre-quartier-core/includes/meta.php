<?php
/**
 * Champs personnalises d'un signalement : localisation (lat/lng), adresse et
 * statut de suivi. On les enregistre aupres de l'API REST (show_in_rest) pour
 * pouvoir les lire cote JavaScript (la carte) sans bricolage.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Les trois statuts du cycle de vie d'un signalement.
 */
function pq_statuts() {
	return array(
		'signale' => array( 'label' => 'Signale',  'couleur' => '#C2492C' ),
		'en_cours'=> array( 'label' => 'En cours',  'couleur' => '#B5791C' ),
		'resolu'  => array( 'label' => 'Resolu',    'couleur' => '#2C7A4B' ),
	);
}

/**
 * Enregistrement des metadonnees aupres de WordPress + REST.
 */
function pq_register_meta() {
	$fields = array(
		'pq_lat'     => 'string',
		'pq_lng'     => 'string',
		'pq_adresse' => 'string',
		'pq_statut'  => 'string',
	);
	foreach ( $fields as $key => $type ) {
		register_post_meta( 'signalement', $key, array(
			'type'         => $type,
			'single'       => true,
			'show_in_rest' => true,
			'auth_callback'=> function () {
				return current_user_can( 'edit_posts' );
			},
		) );
	}
}
add_action( 'init', 'pq_register_meta' );

/**
 * Boite d'edition dans l'admin : saisir la position et le statut a la main.
 */
add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'pq_details',
		'Details du signalement',
		'pq_render_meta_box',
		'signalement',
		'side',
		'high'
	);
} );

function pq_render_meta_box( $post ) {
	wp_nonce_field( 'pq_save_meta', 'pq_meta_nonce' );
	$lat     = get_post_meta( $post->ID, 'pq_lat', true );
	$lng     = get_post_meta( $post->ID, 'pq_lng', true );
	$adresse = get_post_meta( $post->ID, 'pq_adresse', true );
	$statut  = get_post_meta( $post->ID, 'pq_statut', true ) ?: 'signale';
	?>
	<p>
		<label><strong>Adresse</strong></label>
		<input type="text" name="pq_adresse" value="<?php echo esc_attr( $adresse ); ?>" style="width:100%">
	</p>
	<p style="display:flex;gap:6px">
		<span style="flex:1"><label>Latitude</label>
			<input type="text" name="pq_lat" value="<?php echo esc_attr( $lat ); ?>" style="width:100%"></span>
		<span style="flex:1"><label>Longitude</label>
			<input type="text" name="pq_lng" value="<?php echo esc_attr( $lng ); ?>" style="width:100%"></span>
	</p>
	<p>
		<label><strong>Statut</strong></label>
		<select name="pq_statut" style="width:100%">
			<?php foreach ( pq_statuts() as $key => $info ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $statut, $key ); ?>>
					<?php echo esc_html( $info['label'] ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

/**
 * Sauvegarde de la boite d'edition.
 */
add_action( 'save_post_signalement', function ( $post_id ) {
	if ( ! isset( $_POST['pq_meta_nonce'] ) || ! wp_verify_nonce( $_POST['pq_meta_nonce'], 'pq_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array( 'pq_lat', 'pq_lng', 'pq_adresse', 'pq_statut' ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
} );

/**
 * Colonne "Statut" dans la liste d'admin, avec pastille de couleur.
 */
add_filter( 'manage_signalement_posts_columns', function ( $cols ) {
	$cols['pq_statut'] = 'Statut';
	return $cols;
} );
add_action( 'manage_signalement_posts_custom_column', function ( $col, $post_id ) {
	if ( 'pq_statut' === $col ) {
		$statut  = get_post_meta( $post_id, 'pq_statut', true ) ?: 'signale';
		$statuts = pq_statuts();
		$info    = $statuts[ $statut ] ?? $statuts['signale'];
		printf(
			'<span style="display:inline-block;padding:2px 8px;border-radius:10px;color:#fff;font-size:12px;background:%s">%s</span>',
			esc_attr( $info['couleur'] ),
			esc_html( $info['label'] )
		);
	}
}, 10, 2 );
