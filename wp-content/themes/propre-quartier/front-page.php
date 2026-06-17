<?php
/**
 * Page d'accueil : landing complete construite cote theme (on n'utilise pas le
 * contenu de la page, on orchestre directement les sections + shortcodes du
 * plugin).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$stats = function_exists( 'pq_compter_par_statut' ) ? pq_compter_par_statut() : array( 'total' => 0, 'resolu' => 0, 'en_cours' => 0 );
?>

<section class="hero">
	<div class="hero__deco" aria-hidden="true">
		<div class="dots"></div>
		<svg class="skyline" viewBox="0 0 1440 140" preserveAspectRatio="none" fill="#2E9468" xmlns="http://www.w3.org/2000/svg">
			<rect x="0" y="86" width="90" height="54"/><rect x="96" y="64" width="64" height="76"/>
			<rect x="166" y="98" width="54" height="42"/><rect x="226" y="50" width="78" height="90"/>
			<rect x="310" y="80" width="60" height="60"/><rect x="376" y="38" width="46" height="102"/>
			<rect x="430" y="92" width="84" height="48"/><rect x="520" y="60" width="58" height="80"/>
			<rect x="584" y="100" width="70" height="40"/><rect x="660" y="44" width="80" height="96"/>
			<rect x="746" y="84" width="56" height="56"/><rect x="808" y="66" width="66" height="74"/>
			<rect x="880" y="96" width="60" height="44"/><rect x="946" y="52" width="74" height="88"/>
			<rect x="1026" y="88" width="58" height="52"/><rect x="1090" y="40" width="48" height="100"/>
			<rect x="1144" y="94" width="86" height="46"/><rect x="1236" y="62" width="60" height="78"/>
			<rect x="1302" y="98" width="56" height="42"/><rect x="1364" y="56" width="76" height="84"/>
		</svg>
	</div>
	<div class="pq-wrap hero__inner">
		<p class="pq-kicker reveal reveal-1">Plateforme citoyenne &middot; Dakar</p>
		<h1 class="reveal reveal-2">
			Un probleme dans<br>votre quartier&nbsp;? <em>Signalez-le.</em>
		</h1>
		<p class="hero__lead reveal reveal-3">
			Ordures, fuites d'eau, eclairage en panne, voirie degradee&hellip;
			Remontez-le en deux minutes, situez-le sur la <span class="mark">carte</span>
			et suivez sa resolution avec toute la communaute.
		</p>
		<div class="hero__actions reveal reveal-4">
			<a class="pq-button" href="<?php echo esc_url( home_url( '/signaler/' ) ); ?>">Faire un signalement</a>
			<a class="pq-button pq-button--ghost" href="#carte">Explorer la carte</a>
		</div>

		<div class="hero__stats reveal reveal-4">
			<div class="hero__stat">
				<div class="n"><?php echo esc_html( $stats['total'] ); ?></div>
				<div class="l">Signalements</div>
			</div>
			<div class="hero__stat hero__stat--clay">
				<div class="n"><?php echo esc_html( $stats['en_cours'] ); ?></div>
				<div class="l">En cours de traitement</div>
			</div>
			<div class="hero__stat hero__stat--petrol">
				<div class="n"><?php echo esc_html( $stats['resolu'] ); ?></div>
				<div class="l">Problemes resolus</div>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="pq-wrap">
		<div class="section__head">
			<p class="pq-kicker">Comment ca marche</p>
			<h2>Trois etapes, zero paperasse.</h2>
			<p>Pas besoin de compte ni de connaissances techniques : un habitant signale, un moderateur valide, la communaute suit.</p>
		</div>
		<div class="steps">
			<div class="step">
				<span class="step__num">01 /</span>
				<h3>Reperez</h3>
				<p>Vous tombez sur un depot d'ordures, une fuite, un lampadaire mort&hellip; Prenez une photo.</p>
			</div>
			<div class="step">
				<span class="step__num">02 /</span>
				<h3>Signalez</h3>
				<p>Decrivez, cliquez sur la carte pour localiser, envoyez. Deux minutes, sans inscription.</p>
			</div>
			<div class="step">
				<span class="step__num">03 /</span>
				<h3>Suivez</h3>
				<p>Le signalement passe de &laquo;&nbsp;signale&nbsp;&raquo; a &laquo;&nbsp;en cours&nbsp;&raquo; puis &laquo;&nbsp;resolu&nbsp;&raquo;.</p>
			</div>
		</div>
	</div>
</section>

<section class="section" style="padding-top:0">
	<div class="pq-wrap">
		<div class="section__head" style="margin-bottom:1.6rem">
			<p class="pq-kicker">Sur le terrain</p>
			<h2>Ce que vit le quartier, en images.</h2>
		</div>
		<div class="pq-gallery">
			<figure>
				<img src="https://images.unsplash.com/photo-1605600659908-0ef719419d41?auto=format&amp;fit=crop&amp;w=700&amp;q=70" alt="Poubelle qui deborde dans la rue" loading="lazy">
				<figcaption>Quand rien n'est signale</figcaption>
			</figure>
			<figure>
				<img src="https://images.unsplash.com/photo-1571727153934-b9e0059b7ab2?auto=format&amp;fit=crop&amp;w=700&amp;q=70" alt="Tas de bouteilles plastiques" loading="lazy">
				<figcaption>Des dechets qui s'accumulent</figcaption>
			</figure>
			<figure>
				<img src="https://images.unsplash.com/photo-1567393528677-d6adae7d4a0a?auto=format&amp;fit=crop&amp;w=700&amp;q=70" alt="Cartons prets a etre recycles" loading="lazy">
				<figcaption>Du tri qui devient possible</figcaption>
			</figure>
			<figure>
				<img src="https://images.unsplash.com/photo-1497250681960-ef046c08a56e?auto=format&amp;fit=crop&amp;w=700&amp;q=70" alt="Vegetation verdoyante" loading="lazy">
				<figcaption>Un quartier qui respire</figcaption>
			</figure>
		</div>
	</div>
</section>

<section class="section" id="carte" style="background:var(--surface-2);border-block:1px solid var(--line)">
	<div class="pq-wrap">
		<div class="section__head">
			<p class="pq-kicker">En temps reel</p>
			<h2>La carte des signalements.</h2>
			<p>Chaque point est un probleme remonte par un habitant. La couleur indique son statut.</p>
		</div>
		<?php echo do_shortcode( '[pq_carte hauteur="520"]' ); ?>
	</div>
</section>

<section class="section">
	<div class="pq-wrap">
		<div class="section__head">
			<p class="pq-kicker">Derniers signalements</p>
			<h2>Ce que la communaute remonte.</h2>
		</div>
		<?php echo do_shortcode( '[pq_signalements nombre="6"]' ); ?>
		<p style="margin-top:2rem">
			<a class="pq-button pq-button--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'signalement' ) ?: home_url( '/carte/' ) ); ?>">Tout voir &rarr;</a>
		</p>
	</div>
</section>

<section class="section section--ink">
	<div class="pq-wrap" style="text-align:center;max-width:760px">
		<p class="pq-kicker" style="justify-content:center">Votre quartier compte sur vous</p>
		<h2 style="font-size:clamp(2rem,5vw,3.2rem)">Un signalement, c'est deja un pas vers un quartier plus propre.</h2>
		<p style="color:rgba(239,237,230,.66);font-size:1.1rem;margin:1rem auto 2rem">Rejoignez les habitants qui font bouger les choses, rue apres rue.</p>
		<a class="pq-button" href="<?php echo esc_url( home_url( '/signaler/' ) ); ?>">Je signale un probleme</a>
	</div>
</section>

<?php get_footer();
