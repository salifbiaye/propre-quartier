<?php
/**
 * Page "A propos" — version editorialisee (sections, images, statuts, CTA).
 * Le design vit dans ce gabarit : on n'utilise pas le contenu brut de la page.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

$img_hands = 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=1500&q=70';
$img_bins  = 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?auto=format&fit=crop&w=1100&q=70';
$img_litter= 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?auto=format&fit=crop&w=1100&q=70';

$statuts = function_exists( 'pq_statuts' ) ? pq_statuts() : array();
?>
<section class="about-hero" style="--about-img:url('<?php echo esc_url( $img_hands ); ?>')">
	<div class="pq-wrap about-hero__inner">
		<p class="pq-kicker">Notre mission</p>
		<h1>Rendre visible ce que le quartier subit en silence.</h1>
		<p>Propre Quartier donne a chaque habitant un moyen simple de signaler les problemes de proprete et de voirie, de les localiser, et d'en suivre la resolution au grand jour.</p>
	</div>
</section>

<!-- Mission / tri -->
<section class="section">
	<div class="pq-wrap about-split">
		<div class="about-figure"><img src="<?php echo esc_url( $img_bins ); ?>" alt="Bacs de tri colores"></div>
		<div class="about-text">
			<p class="pq-kicker">Pourquoi</p>
			<h2>Un quartier propre, c'est l'affaire de tous.</h2>
			<p>Trop souvent, un depot d'ordures ou un caniveau bouche traine pendant des semaines, faute d'un endroit ou le signaler. L'information se perd entre voisins, services et autorites. Nous voulons en finir avec ce silence : un probleme remonte, localise et suivi devient difficile a ignorer.</p>
		</div>
	</div>
</section>

<!-- Le probleme -->
<section class="section" style="background:var(--surface-2);border-block:1px solid var(--line)">
	<div class="pq-wrap about-split about-split--reverse">
		<div class="about-figure"><img src="<?php echo esc_url( $img_litter ); ?>" alt="Dechets abandonnes au sol"></div>
		<div class="about-text">
			<p class="pq-kicker">Le constat</p>
			<h2>Voir le probleme, c'est deja agir.</h2>
			<p>Notre conviction est simple : rendre un probleme visible est la premiere etape pour le resoudre. Quand un signalement est public, geolocalise et suivi par toute la communaute, il pese davantage et obtient une reponse plus rapide.</p>
		</div>
	</div>
</section>

<!-- Comment ca marche -->
<section class="section">
	<div class="pq-wrap">
		<div class="section__head">
			<p class="pq-kicker">Comment ca marche</p>
			<h2>Quatre etapes, sans paperasse.</h2>
		</div>
		<div class="steps">
			<div class="step"><span class="step__num">01</span><h3>Reperez</h3><p>Un probleme dans votre rue ? Prenez une photo si possible.</p></div>
			<div class="step"><span class="step__num">02</span><h3>Signalez</h3><p>Titre, description, categorie, et un clic sur la carte pour localiser.</p></div>
			<div class="step"><span class="step__num">03</span><h3>Moderation</h3><p>Un responsable verifie le signalement avant publication.</p></div>
			<div class="step"><span class="step__num">04</span><h3>Suivez</h3><p>Le statut evolue jusqu'a la resolution, visible de tous.</p></div>
		</div>
	</div>
</section>

<!-- Statuts -->
<section class="section" style="background:var(--surface-2);border-top:1px solid var(--line)">
	<div class="pq-wrap">
		<div class="section__head">
			<p class="pq-kicker">Le suivi</p>
			<h2>Le cycle de vie d'un signalement.</h2>
			<p>Chaque probleme avance le long d'un parcours clair &mdash; tout le monde voit ou il en est.</p>
		</div>
		<?php
		$c_sig = $statuts['signale']['couleur']  ?? '#C2492C';
		$c_enc = $statuts['en_cours']['couleur'] ?? '#B5791C';
		$c_res = $statuts['resolu']['couleur']   ?? '#2C7A4B';
		?>
		<div class="pq-flow">
			<div class="pq-flow__step">
				<span class="pq-flow__dot" style="background:<?php echo esc_attr( $c_sig ); ?>">1</span>
				<span class="pq-flow__arrow">&#10148;</span>
				<span class="pq-flow__tag" style="color:<?php echo esc_attr( $c_sig ); ?>">Signale</span>
				<h3>On remonte le probleme</h3>
				<p>Le signalement vient d'etre publie et attend une prise en charge.</p>
			</div>
			<div class="pq-flow__step">
				<span class="pq-flow__dot" style="background:<?php echo esc_attr( $c_enc ); ?>">2</span>
				<span class="pq-flow__arrow">&#10148;</span>
				<span class="pq-flow__tag" style="color:<?php echo esc_attr( $c_enc ); ?>">En cours</span>
				<h3>L'intervention demarre</h3>
				<p>Le probleme est pris en compte&nbsp;: une equipe est en route.</p>
			</div>
			<div class="pq-flow__step">
				<span class="pq-flow__dot" style="background:<?php echo esc_attr( $c_res ); ?>">3</span>
				<span class="pq-flow__tag" style="color:<?php echo esc_attr( $c_res ); ?>">Resolu</span>
				<h3>Le quartier respire</h3>
				<p>C'est regle. Le signalement reste visible comme preuve d'action.</p>
			</div>
		</div>
	</div>
</section>

<!-- Qui sommes-nous -->
<section class="section">
	<div class="pq-wrap" style="max-width:760px">
		<p class="pq-kicker">Le projet</p>
		<h2 style="font-size:clamp(1.7rem,3.4vw,2.4rem);font-weight:400;margin:.5rem 0 .8rem">Qui sommes-nous ?</h2>
		<p style="color:var(--muted);font-size:1.06rem">Propre Quartier est realise dans le cadre du cours <strong>IDA</strong> a l'<strong>Ecole Superieure Polytechnique (ESP) de l'UCAD</strong>. Pense par un groupe d'etudiants, il s'appuie sur le CMS <strong>WordPress</strong>, etendu par un theme et un module sur mesure : type de contenu dedie, cartographie, formulaire public modere, filtres et suivi des statuts.</p>
	</div>
</section>

<section class="section section--ink">
	<div class="pq-wrap" style="text-align:center;max-width:720px">
		<p class="pq-kicker" style="justify-content:center">Votre quartier compte sur vous</p>
		<h2 style="font-size:clamp(2rem,5vw,3rem)">Un signalement, c'est deja un quartier qui avance.</h2>
		<p style="color:rgba(239,237,230,.7);font-size:1.1rem;margin:1rem auto 2rem">Rejoignez les habitants qui font bouger les choses, rue apres rue.</p>
		<a class="pq-button" href="<?php echo esc_url( home_url( '/signaler/' ) ); ?>">Faire un signalement</a>
	</div>
</section>
<?php
get_footer();
