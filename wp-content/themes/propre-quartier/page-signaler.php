<?php
/**
 * Page "Signaler" : en-tete vert + formulaire (colonne principale) accompagne
 * d'une colonne d'aide (comment ca marche, statuts, conseil).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$statuts = function_exists( 'pq_statuts' ) ? pq_statuts() : array();
?>
<section class="single-hero">
	<div class="pq-wrap">
		<p class="pq-kicker">Participer</p>
		<h1>Signaler un probleme</h1>
		<p class="hero__lead">Deux minutes suffisent. Decrivez le probleme, placez-le sur la carte, ajoutez une photo si vous pouvez. Pas besoin de compte.</p>
	</div>
</section>

<section class="section">
	<div class="pq-wrap signaler-grid">

		<div class="signaler-main">
			<?php
			while ( have_posts() ) :
				the_post();
				echo '<div class="entry-content">';
				the_content();
				echo '</div>';
			endwhile;
			?>
		</div>

		<aside class="signaler-aside">
			<figure class="aside-photo">
				<img src="https://images.unsplash.com/photo-1605600659908-0ef719419d41?auto=format&amp;fit=crop&amp;w=700&amp;q=70" alt="Exemple : une poubelle qui deborde" loading="lazy">
			</figure>
			<div class="aside-card aside-card--accent">
				<h3>&#9201; Comment ca marche</h3>
				<ol>
					<li>Decrivez le probleme (titre + details).</li>
					<li>Choisissez la categorie concernee.</li>
					<li>Cliquez sur la carte pour le localiser.</li>
					<li>Validez : un moderateur publie apres verification.</li>
				</ol>
			</div>

			<div class="aside-card">
				<h3>Le suivi</h3>
				<div class="aside-statuts">
					<?php foreach ( $statuts as $info ) : ?>
						<div class="aside-statut">
							<i style="background:<?php echo esc_attr( $info['couleur'] ); ?>"></i>
							<span><strong><?php echo esc_html( $info['label'] ); ?></strong></span>
						</div>
					<?php endforeach; ?>
				</div>
				<p style="margin:.8rem 0 0;color:var(--muted);font-size:.92rem">Votre signalement passe d'un statut a l'autre jusqu'a sa resolution.</p>
			</div>

			<div class="aside-card">
				<h3>&#128247; Un bon signalement</h3>
				<p style="margin:0;color:var(--muted);font-size:.95rem">Une photo nette et une localisation precise aident enormement les services a intervenir vite.</p>
			</div>
		</aside>

	</div>
</section>
<?php
get_footer();
