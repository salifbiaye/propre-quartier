<?php
/**
 * En-tete du site : <head> + barre de navigation collante.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="topbar">
	<div class="pq-wrap topbar__inner">
		<span>&#9851; Ensemble pour un quartier plus <strong>propre</strong></span>
		<span>Signalement gratuit &middot; Dakar</span>
	</div>
</div>

<header class="site-header">
	<div class="pq-wrap site-header__inner">
		<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<span class="site-brand__pin" aria-hidden="true">&#9679;</span>
			<?php bloginfo( 'name' ); ?>
		</a>

		<button class="nav-toggle" aria-label="Ouvrir le menu" aria-expanded="false">&#9776;</button>

		<nav class="site-nav" aria-label="Navigation principale">
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
			<a class="pq-button site-nav__cta" href="<?php echo esc_url( home_url( '/signaler/' ) ); ?>">Signaler &rarr;</a>
		</nav>
	</div>
</header>

<main id="contenu">
