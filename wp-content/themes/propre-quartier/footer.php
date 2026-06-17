<?php
/**
 * Pied de page du site.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
</main><!-- #contenu -->

<footer class="site-footer">
	<div class="pq-wrap">
		<div class="site-footer__grid">
			<div>
				<p class="site-footer__word">Propre<br><span>Quartier.</span></p>
				<p>Plateforme citoyenne de signalement des problemes de proprete et de voirie. Un quartier plus propre, ca commence par un signalement.</p>
			</div>

			<div>
				<h4>Naviguer</h4>
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'container'      => false,
						'items_wrap'     => '<ul>%3$s</ul>',
						'depth'          => 1,
					) );
				}
				?>
			</div>

			<div>
				<h4>Agir maintenant</h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/signaler/' ) ); ?>">Faire un signalement</a></li>
					<li><a href="<?php echo esc_url( home_url( '/carte/' ) ); ?>">Voir la carte</a></li>
					<li><a href="<?php echo esc_url( home_url( '/a-propos/' ) ); ?>">A propos du projet</a></li>
				</ul>
			</div>
		</div>

		<div class="site-footer__base">
			<span>&copy; <?php echo esc_html( date( 'Y' ) ); ?> Propre Quartier &middot; Projet IDA / CMS &middot; ESP &mdash; UCAD</span>
			<span>WordPress &middot; Dockerise &middot; Dakar</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>

<script>
/* Menu mobile */
(function () {
	var t = document.querySelector('.nav-toggle');
	var n = document.querySelector('.site-nav');
	if (t && n) {
		t.addEventListener('click', function () {
			var open = n.classList.toggle('open');
			t.setAttribute('aria-expanded', open ? 'true' : 'false');
			t.innerHTML = open ? '&times;' : '&#9776;';
		});
	}
})();
</script>
</body>
</html>
