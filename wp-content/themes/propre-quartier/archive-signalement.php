<?php
/**
 * Archive des signalements : carte (reflet des filtres) + barre de filtres
 * server-side + liste paginee.
 *
 * Le filtrage et la pagination sont entierement geres cote serveur :
 *   - pre_get_posts (functions.php) applique ?statut / ?categorie a la requete
 *     principale + posts_per_page = 9 ;
 *   - les chips de filtre sont des liens qui rechargent la page ;
 *   - the_posts_pagination() pagine la requete principale.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

global $wp_query;
$total = (int) $wp_query->found_posts;

// Carte : on montre TOUS les points correspondant aux filtres (pas seulement
// la page courante), via une requete dediee non paginee.
$map_ids = get_posts( array_merge( array(
	'post_type'      => 'signalement',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'fields'         => 'ids',
), pq_request_filter_args() ) );

$points = array();
foreach ( $map_ids as $mid ) {
	$points[] = pq_point_data( $mid );
}
?>
<section class="single-hero">
	<div class="pq-wrap">
		<p class="pq-kicker">Tous les signalements</p>
		<h1>La carte des problemes du quartier.</h1>
	</div>
</section>

<section class="section">
	<div class="pq-wrap">
		<div class="pq-carte-wrap">
			<div class="pq-carte" style="height:480px" data-points="<?php echo esc_attr( wp_json_encode( $points ) ); ?>"></div>
			<div class="pq-legende">
				<?php foreach ( pq_statuts() as $info ) : ?>
					<span><i style="background:<?php echo esc_attr( $info['couleur'] ); ?>"></i><?php echo esc_html( $info['label'] ); ?></span>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section class="section" id="liste" style="background:var(--surface-2);border-top:1px solid var(--line);scroll-margin-top:90px">
	<div class="pq-wrap">
		<div class="section__head" style="margin-bottom:1.6rem">
			<p class="pq-kicker">Liste complete</p>
			<h2>Parcourir &amp; filtrer.</h2>
		</div>

		<?php echo pq_render_filtres(); ?>

		<p class="pq-resultats">
			<strong><?php echo esc_html( $total ); ?></strong>
			signalement<?php echo $total > 1 ? 's' : ''; ?> correspondant<?php echo $total > 1 ? 's' : ''; ?>.
		</p>

		<?php if ( have_posts() ) : ?>
			<div class="pq-grille">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php echo pq_card_markup( get_the_ID() ); ?>
				<?php endwhile; ?>
			</div>

			<nav class="pq-pagination" aria-label="Pagination des signalements">
				<?php
				echo paginate_links( array(
					'mid_size'     => 1,
					'prev_text'    => '&larr; Precedent',
					'next_text'    => 'Suivant &rarr;',
					'add_fragment' => '#liste',
				) );
				?>
			</nav>
		<?php else : ?>
			<p class="pq-vide">Aucun signalement ne correspond a ces filtres.</p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
