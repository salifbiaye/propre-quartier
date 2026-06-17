<?php
/**
 * Detail d'un signalement : titre, statut, adresse, photo, description et
 * mini-carte centree sur le point.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

while ( have_posts() ) :
	the_post();
	$id      = get_the_ID();
	$lat     = get_post_meta( $id, 'pq_lat', true );
	$lng     = get_post_meta( $id, 'pq_lng', true );
	$adresse = get_post_meta( $id, 'pq_adresse', true );
	$statut  = get_post_meta( $id, 'pq_statut', true ) ?: 'signale';
	$cats    = get_the_term_list( $id, 'categorie_probleme', '', ', ' );

	$statuts = function_exists( 'pq_statuts' ) ? pq_statuts() : array();
	$info    = $statuts[ $statut ] ?? array( 'label' => 'Signale', 'couleur' => '#D9461F' );
	?>
	<section class="single-hero">
		<div class="pq-wrap">
			<p class="pq-kicker">Signalement</p>
			<?php the_title( '<h1>', '</h1>' ); ?>
			<div class="pq-single-meta">
				<span class="pq-badge" style="background:<?php echo esc_attr( $info['couleur'] ); ?>"><?php echo esc_html( $info['label'] ); ?></span>
				<?php if ( $cats ) : ?><span><?php echo wp_kses_post( $cats ); ?></span><?php endif; ?>
				<?php if ( $adresse ) : ?><span>&#128205; <?php echo esc_html( $adresse ); ?></span><?php endif; ?>
				<span><?php echo esc_html( get_the_date() ); ?></span>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="pq-wrap">
			<?php if ( has_post_thumbnail() ) : ?>
				<div style="border:1px solid var(--line);border-radius:var(--radius);overflow:hidden;box-shadow:var(--sh-2);margin-bottom:2rem;max-width:760px">
					<?php the_post_thumbnail( 'large', array( 'style' => 'width:100%;display:block' ) ); ?>
				</div>
			<?php endif; ?>

			<p class="pq-kicker" style="margin-bottom:1.1rem">La situation</p>
			<div class="signalement-body"><?php the_content(); ?></div>

			<?php if ( $lat && $lng ) :
				$point = array( array(
					'titre'        => get_the_title(),
					'extrait'      => '',
					'lien'         => get_permalink(),
					'lat'          => $lat,
					'lng'          => $lng,
					'adresse'      => $adresse,
					'statut'       => $statut,
					'statut_label' => $info['label'],
					'couleur'      => $info['couleur'],
					'categorie'    => '',
					'image'        => '',
				) );
				?>
				<h2 style="margin-top:2.5rem">Localisation</h2>
				<div class="pq-carte" style="height:360px;max-width:760px" data-points="<?php echo esc_attr( wp_json_encode( $point ) ); ?>"></div>
			<?php endif; ?>

			<p style="margin-top:2.5rem">
				<a class="back-link" href="<?php echo esc_url( get_post_type_archive_link( 'signalement' ) ?: home_url( '/carte/' ) ); ?>">&larr; Tous les signalements</a>
			</p>
		</div>
	</section>
	<?php
endwhile;

get_footer();
