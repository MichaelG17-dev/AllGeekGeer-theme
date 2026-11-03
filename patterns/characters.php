<?php
/**
 * Title: AGG — Персонажі (слайдер)
 * Slug: allgeekgear/characters
 * Categories: allgeekgear
 * Description: Горизонтальний слайдер персонажів із кільцями-градієнтами.
 * Keywords: персонажі, слайдер, фільтр
 * Viewport Width: 1400
 *
 * Потребує таксономії 'agg_character' (реєструється в inc/post-types.php)
 * або заміни taxonomy="product_cat" для WooCommerce.
 *
 * @package AllGeekGear
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- wp:group {"tagName":"section","metadata":{"name":"AGG Characters"},"className":"agg-section agg-characters","anchor":"characters","layout":{"type":"default"}} -->
<section id="characters" class="wp-block-group agg-section agg-characters">

	<!-- wp:group {"className":"agg-heading agg-reveal","layout":{"type":"default"}} -->
	<div class="wp-block-group agg-heading agg-reveal">
		<!-- wp:paragraph {"className":"agg-eyebrow"} -->
		<p class="agg-eyebrow">Обери свого шінобі</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"className":"agg-title"} -->
		<h2 class="agg-title">Character Select</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"agg-subtitle"} -->
		<p class="agg-subtitle">Гортай ростер і фільтруй сховище за улюбленим ніндзя.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"agg-section__body agg-reveal","layout":{"type":"default"}} -->
	<div class="wp-block-group agg-section__body agg-reveal">
		<!-- wp:shortcode -->
		[agg_characters taxonomy="agg_character" limit="12"]
		<!-- /wp:shortcode -->
	</div>
	<!-- /wp:group -->

</section>
<!-- /wp:group -->
