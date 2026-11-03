<?php
/**
 * Title: AGG — Магазин (сітка карток)
 * Slug: allgeekgear/shop
 * Categories: allgeekgear
 * Description: Сітка товарів із фольгованими картками та фільтром рідкісності.
 * Keywords: магазин, shop, товари, картки
 * Viewport Width: 1400
 *
 * post_type="product" — для WooCommerce.
 * Якщо використовуєш власний CPT, постав post_type="agg_card".
 *
 * @package AllGeekGear
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- wp:group {"tagName":"section","metadata":{"name":"AGG Shop"},"className":"agg-section agg-shop","anchor":"shop","layout":{"type":"default"}} -->
<section id="shop" class="wp-block-group agg-section agg-shop">

	<!-- wp:html -->
	<div class="agg-section__bar">
		<header class="agg-heading agg-reveal">
			<p class="agg-eyebrow">Дроп сховища</p>
			<h2 class="agg-title">Holo Singles</h2>
			<p class="agg-subtitle">Кожна картка їде в сліві та топ-лоадері. Наведи, щоб спіймати фольгу.</p>
		</header>

		<div class="agg-filter agg-reveal" role="group" aria-label="Фільтр за рідкісністю">
			<a class="agg-filter__btn" href="?rarity=" aria-pressed="true">ALL</a>
			<a class="agg-filter__btn" href="?rarity=SSR" aria-pressed="false">SSR</a>
			<a class="agg-filter__btn" href="?rarity=UR" aria-pressed="false">UR</a>
			<a class="agg-filter__btn" href="?rarity=OR" aria-pressed="false">OR</a>
			<a class="agg-filter__btn" href="?rarity=AR" aria-pressed="false">AR</a>
		</div>
	</div>
	<!-- /wp:html -->

	<!-- wp:group {"className":"agg-section__body","layout":{"type":"default"}} -->
	<div class="wp-block-group agg-section__body">
		<!-- wp:shortcode -->
		[agg_products post_type="product" limit="12"]
		<!-- /wp:shortcode -->
	</div>
	<!-- /wp:group -->

</section>
<!-- /wp:group -->
