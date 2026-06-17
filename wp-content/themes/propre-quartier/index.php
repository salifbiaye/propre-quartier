<?php
/**
 * Gabarit generique de repli (blog, recherche, archives diverses, 404).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<section class="single-hero">
	<div class="pq-wrap">
		<p class="pq-kicker">Propre Quartier</p>
		<h1>
			<?php
			if ( is_search() ) {
				printf( 'Recherche : %s', esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				the_archive_title();
			} elseif ( is_404() ) {
				echo 'Page introuvable';
			} else {
				echo 'Journal';
			}
			?>
		</h1>
	</div>
</section>

<section class="section">
	<div class="pq-wrap">
		<?php if ( have_posts() ) : ?>
			<div class="pq-grille">
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="pq-carte-item">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="pq-vignette" href="<?php the_permalink(); ?>" style="background-image:url('<?php echo esc_url( get_the_post_thumbnail_url( null, 'medium' ) ); ?>')"></a>
						<?php endif; ?>
						<div class="pq-corps">
							<span class="pq-cat"><?php echo esc_html( get_the_date() ); ?></span>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="pq-extrait"><?php echo esc_html( get_the_excerpt() ); ?></p>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<div style="margin-top:2.5rem"><?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?></div>
		<?php else : ?>
			<p class="pq-vide">Rien a afficher ici pour le moment.</p>
			<p style="margin-top:1.5rem"><a class="pq-button" href="<?php echo esc_url( home_url( '/' ) ); ?>">Retour a l'accueil</a></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
