<?php
/**
 * Shortcodes d'affichage cote public :
 *   [pq_formulaire]        -> formulaire de signalement
 *   [pq_carte]             -> carte Leaflet avec tous les signalements
 *   [pq_signalements]      -> liste/grille des derniers signalements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recupere les signalements publies avec coordonnees, pretes pour le JS.
 */
function pq_collecter_signalements( $limit = -1 ) {
	$query = new WP_Query( array(
		'post_type'      => 'signalement',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'no_found_rows'  => true,
	) );

	$points = array();
	foreach ( $query->posts as $p ) {
		$points[] = pq_point_data( $p->ID );
	}
	return $points;
}

/**
 * Donnees normalisees d'un signalement (utilisees par la carte ET les cartes
 * de la grille). Source unique pour eviter toute divergence.
 */
function pq_point_data( $post_id ) {
	$statuts = pq_statuts();
	$statut  = get_post_meta( $post_id, 'pq_statut', true ) ?: 'signale';
	$cats    = wp_get_post_terms( $post_id, 'categorie_probleme', array( 'fields' => 'names' ) );
	return array(
		'id'           => $post_id,
		'titre'        => get_the_title( $post_id ),
		'extrait'      => wp_trim_words( get_post_field( 'post_content', $post_id ), 24 ),
		'lien'         => get_permalink( $post_id ),
		'lat'          => get_post_meta( $post_id, 'pq_lat', true ),
		'lng'          => get_post_meta( $post_id, 'pq_lng', true ),
		'adresse'      => get_post_meta( $post_id, 'pq_adresse', true ),
		'statut'       => $statut,
		'statut_label' => $statuts[ $statut ]['label'] ?? 'Signale',
		'couleur'      => $statuts[ $statut ]['couleur'] ?? '#C2492C',
		'categorie'    => $cats ? $cats[0] : '',
		'image'        => get_the_post_thumbnail_url( $post_id, 'medium' ) ?: '',
	);
}

/**
 * Visuel (couleur + icone SVG) associe a une categorie de probleme.
 * Sert d'illustration de repli quand un signalement n'a pas de photo, pour
 * garder une grille vivante et coherente.
 */
function pq_category_visual( $categorie ) {
	$c = function_exists( 'mb_strtolower' ) ? mb_strtolower( $categorie ) : strtolower( $categorie );

	$icons = array(
		'ordure'   => array( '#C2492C', '<path d="M9 3h6l1 2h5v2H3V5h5l1-2zM6 9h12l-1 12H7L6 9z"/>' ),
		'eau'      => array( '#2563EB', '<path d="M12 2s7 8 7 13a7 7 0 1 1-14 0c0-5 7-13 7-13z"/>' ),
		'eclair'   => array( '#D97706', '<path d="M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2zM9 19h6v1H9v-1zm0 2h6v1H9v-1z"/>' ),
		'voirie'   => array( '#92400E', '<path d="M12 2 1 21h22L12 2zm0 5 6.9 12H5.1L12 7zm-1 4h2v3h-2v-3zm0 4h2v2h-2v-2z"/>' ),
		'vert'     => array( '#2C7A4B', '<path d="M17 8a5 5 0 0 0-10 0c0 .4.05.8.14 1.2A4 4 0 0 0 8 17h3v4h2v-4h3a4 4 0 0 0 .86-7.8c.09-.4.14-.8.14-1.2z"/>' ),
	);

	foreach ( $icons as $key => $data ) {
		if ( strpos( $c, $key ) !== false ) {
			return array( 'color' => $data[0], 'icon' => $data[1] );
		}
	}
	// Defaut : drapeau
	return array( 'color' => '#1C6B4B', 'icon' => '<path d="M5 3v18h2v-7h7l-1-3 1-3H7V3H5z"/>' );
}

/**
 * Markup d'une vignette de signalement (carte de la grille). Reutilisable
 * partout (shortcode liste, archive...).
 */
function pq_card_markup( $post_id ) {
	$pt  = pq_point_data( $post_id );
	$vis = pq_category_visual( $pt['categorie'] );
	ob_start();
	?>
	<article class="pq-carte-item">
		<?php if ( $pt['image'] ) : ?>
			<a href="<?php echo esc_url( $pt['lien'] ); ?>" class="pq-vignette" style="background-image:url('<?php echo esc_url( $pt['image'] ); ?>')"></a>
		<?php else : ?>
			<a href="<?php echo esc_url( $pt['lien'] ); ?>" class="pq-vignette pq-vignette--illu" style="background:<?php echo esc_attr( $vis['color'] ); ?>">
				<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><?php echo $vis['icon']; // phpcs:ignore ?></svg>
			</a>
		<?php endif; ?>
		<div class="pq-corps">
			<div>
				<span class="pq-badge" style="background:<?php echo esc_attr( $pt['couleur'] ); ?>"><?php echo esc_html( $pt['statut_label'] ); ?></span>
				<?php if ( $pt['categorie'] ) : ?><span class="pq-cat"><?php echo esc_html( $pt['categorie'] ); ?></span><?php endif; ?>
			</div>
			<h3><a href="<?php echo esc_url( $pt['lien'] ); ?>"><?php echo esc_html( $pt['titre'] ); ?></a></h3>
			<?php if ( $pt['adresse'] ) : ?><p class="pq-adresse">&#128205; <?php echo esc_html( $pt['adresse'] ); ?></p><?php endif; ?>
			<p class="pq-extrait"><?php echo esc_html( $pt['extrait'] ); ?></p>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * [pq_carte hauteur="480"]
 */
function pq_shortcode_carte( $atts ) {
	$atts    = shortcode_atts( array( 'hauteur' => 480 ), $atts );
	$points  = pq_collecter_signalements();
	$id      = 'pq-carte-' . wp_rand( 1000, 9999 );

	ob_start();
	?>
	<div class="pq-carte-wrap">
		<div id="<?php echo esc_attr( $id ); ?>"
		     class="pq-carte"
		     style="height:<?php echo intval( $atts['hauteur'] ); ?>px"
		     data-points="<?php echo esc_attr( wp_json_encode( $points ) ); ?>"></div>
		<div class="pq-legende">
			<?php foreach ( pq_statuts() as $info ) : ?>
				<span><i style="background:<?php echo esc_attr( $info['couleur'] ); ?>"></i><?php echo esc_html( $info['label'] ); ?></span>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'pq_carte', 'pq_shortcode_carte' );

/**
 * [pq_signalements nombre="9"]
 */
function pq_shortcode_liste( $atts ) {
	$atts = shortcode_atts( array( 'nombre' => 9 ), $atts );
	$ids  = get_posts( array(
		'post_type'      => 'signalement',
		'post_status'    => 'publish',
		'posts_per_page' => intval( $atts['nombre'] ),
		'fields'         => 'ids',
		'no_found_rows'  => true,
	) );

	if ( empty( $ids ) ) {
		return '<p class="pq-vide">Aucun signalement pour le moment.</p>';
	}

	$out = '<div class="pq-grille">';
	foreach ( $ids as $id ) {
		$out .= pq_card_markup( $id );
	}
	$out .= '</div>';
	return $out;
}
add_shortcode( 'pq_signalements', 'pq_shortcode_liste' );

/**
 * Barre de filtres (statut + categorie) pour l'archive des signalements.
 * 100% server-side : chaque filtre est un lien qui recharge la page avec les
 * bons parametres d'URL, lus ensuite par WP_Query (voir pre_get_posts du theme).
 */
function pq_render_filtres() {
	$base       = get_post_type_archive_link( 'signalement' );
	$cur_statut = isset( $_GET['statut'] ) ? sanitize_key( $_GET['statut'] ) : '';
	$cur_cat    = isset( $_GET['categorie'] ) ? sanitize_title( $_GET['categorie'] ) : '';

	// On preserve l'autre filtre quand on clique (et on repart page 1).
	// L'ancre #liste evite de remonter en haut de la page apres rechargement.
	$url = function ( $args ) use ( $base, $cur_statut, $cur_cat ) {
		$q = array( 'statut' => $cur_statut, 'categorie' => $cur_cat );
		$q = array_merge( $q, $args );
		$q = array_filter( $q ); // retire les valeurs vides
		return ( $q ? add_query_arg( $q, $base ) : $base ) . '#liste';
	};

	ob_start();
	echo '<div class="pq-filtres">';

	// --- Statut ---
	echo '<div class="pq-filtres__row"><span class="pq-filtres__label">Statut</span>';
	printf(
		'<a class="pq-chip%s" href="%s">Tous</a>',
		$cur_statut ? '' : ' is-active',
		esc_url( $url( array( 'statut' => '' ) ) )
	);
	foreach ( pq_statuts() as $key => $info ) {
		printf(
			'<a class="pq-chip%s" href="%s"><i style="background:%s"></i>%s</a>',
			$cur_statut === $key ? ' is-active' : '',
			esc_url( $url( array( 'statut' => $key ) ) ),
			esc_attr( $info['couleur'] ),
			esc_html( $info['label'] )
		);
	}
	echo '</div>';

	// --- Categorie ---
	$cats = get_terms( array( 'taxonomy' => 'categorie_probleme', 'hide_empty' => false ) );
	if ( ! is_wp_error( $cats ) && $cats ) {
		echo '<div class="pq-filtres__row"><span class="pq-filtres__label">Categorie</span>';
		printf(
			'<a class="pq-chip%s" href="%s">Toutes</a>',
			$cur_cat ? '' : ' is-active',
			esc_url( $url( array( 'categorie' => '' ) ) )
		);
		foreach ( $cats as $c ) {
			printf(
				'<a class="pq-chip%s" href="%s">%s</a>',
				$cur_cat === $c->slug ? ' is-active' : '',
				esc_url( $url( array( 'categorie' => $c->slug ) ) ),
				esc_html( $c->name )
			);
		}
		echo '</div>';
	}

	echo '</div>';
	return ob_get_clean();
}

/**
 * [pq_formulaire]
 */
function pq_shortcode_formulaire() {
	$cats = get_terms( array( 'taxonomy' => 'categorie_probleme', 'hide_empty' => false ) );
	$etat = $_GET['pq'] ?? '';

	ob_start();

	if ( 'merci' === $etat ) {
		echo '<div class="pq-message pq-ok">Merci ! Votre signalement a bien ete envoye. Il sera publie apres verification.</div>';
	} elseif ( 'erreur' === $etat ) {
		echo '<div class="pq-message pq-ko">Oups, le titre et la description sont obligatoires.</div>';
	}
	?>
	<form class="pq-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="pq_nouveau_signalement">
		<?php wp_nonce_field( 'pq_form', 'pq_nonce' ); ?>

		<label>Titre du probleme *
			<input type="text" name="pq_titre" required placeholder="Ex : Tas d'ordures rue 10">
		</label>

		<label>Categorie
			<select name="pq_categorie">
				<option value="">-- Choisir --</option>
				<?php foreach ( $cats as $c ) : ?>
					<option value="<?php echo esc_attr( $c->term_id ); ?>"><?php echo esc_html( $c->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<label>Description *
			<textarea name="pq_description" rows="4" required placeholder="Decrivez le probleme..."></textarea>
		</label>

		<label>Adresse / reperes
			<input type="text" name="pq_adresse" placeholder="Quartier, rue, point de repere">
		</label>

		<p class="pq-aide">Cliquez sur la carte pour situer le probleme :</p>
		<div id="pq-carte-form" class="pq-carte" style="height:300px"></div>
		<input type="hidden" name="pq_lat" id="pq_lat">
		<input type="hidden" name="pq_lng" id="pq_lng">

		<label>Photo (optionnel)
			<input type="file" name="pq_photo" accept="image/*">
		</label>

		<button type="submit" class="pq-btn">Envoyer le signalement</button>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'pq_formulaire', 'pq_shortcode_formulaire' );
