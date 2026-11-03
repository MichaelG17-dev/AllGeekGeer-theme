<?php
/**
 * Title: AGG — Фольгова скарбниця (16 ефектів)
 * Slug: allgeekgear/foils
 * Categories: allgeekgear
 * Description: Вітрина всіх 16 варіантів фольги з унікальними ефектами при наведенні.
 * Keywords: фольга, foil, holo, рідкісність, ефекти
 * Viewport Width: 1400
 *
 * У [agg_foil_showcase image="1"] заміни 1 на ID свого зображення картки.
 *
 * @package AllGeekGear
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- wp:group {"tagName":"section","metadata":{"name":"AGG Foils"},"className":"agg-section agg-foils","anchor":"foils","layout":{"type":"default"}} -->
<section id="foils" class="wp-block-group agg-section agg-foils">

	<!-- wp:html -->
	<div class="agg-foils__glow" aria-hidden="true"></div>
	<!-- /wp:html -->

	<!-- wp:group {"className":"agg-heading agg-reveal","layout":{"type":"default"}} -->
	<div class="wp-block-group agg-heading agg-reveal">
		<!-- wp:paragraph {"className":"agg-eyebrow"} -->
		<p class="agg-eyebrow">Скарбниця фольги · 16 фінішів</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"className":"agg-title"} -->
		<h2 class="agg-title">Choose Your Foil</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"agg-subtitle"} -->
		<p class="agg-subtitle">Шістнадцять легендарних фінішів — кожен по-своєму ловить світло. Наведи, щоб роздивитись.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"agg-section__body","layout":{"type":"default"}} -->
	<div class="wp-block-group agg-section__body">
		<!-- wp:shortcode -->
		[agg_foil_showcase image="1"]
		<!-- /wp:shortcode -->
	</div>
	<!-- /wp:group -->

</section>
<!-- /wp:group -->
