<?php
/**
 * Gabarit des pages standard (Signaler, Carte, A propos...).
 * Le contenu (shortcodes inclus) est rendu tel quel dans une zone lisible.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<section class="single-hero">
	<div class="pq-wrap">
		<p class="pq-kicker">Propre Quartier</p>
		<?php the_title( '<h1>', '</h1>' ); ?>
	</div>
</section>

<section class="section">
	<div class="pq-wrap">
		<?php
		while ( have_posts() ) :
			the_post();
			echo '<div class="entry-content">';
			the_content();
			echo '</div>';
		endwhile;
		?>
	</div>
</section>
<?php
get_footer();
