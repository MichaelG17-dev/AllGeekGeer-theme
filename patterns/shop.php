<?php
/**
 * Title: AGG — Магазин (сітка карток)
 * Slug: allgeekgear/shop
 * Categories: allgeekgear
 * Description: Сітка товарів із фольгованими картками та фільтром рідкісності.
 * Keywords: магазин, shop, товари, картки
 * Viewport Width: 1400
 *
 * Товари виводяться блоком WooCommerce «Product Collection»
 * з класом agg-default-product (див. inc/product-collection.php).
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
		<!-- wp:woocommerce/product-collection {"queryId":0,"query":{"perPage":12,"pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","search":"","exclude":[],"inherit":false,"taxQuery":[],"isProductCollectionBlock":true,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","outofstock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[]},"tagName":"div","displayLayout":{"type":"flex","columns":4,"shrinkColumns":true},"className":"agg-default-product"} -->
		<div class="wp-block-woocommerce-product-collection agg-default-product">
			<!-- wp:woocommerce/product-template -->
				<!-- wp:woocommerce/product-image {"isDescendentOfQueryLoop":true} /-->
				<!-- wp:post-title {"isLink":true,"level":3,"__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->
				<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between"}} -->
				<div class="wp-block-group">
					<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true} /-->
					<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true} /-->
				</div>
				<!-- /wp:group -->
			<!-- /wp:woocommerce/product-template -->
		</div>
		<!-- /wp:woocommerce/product-collection -->
	</div>
	<!-- /wp:group -->

</section>
<!-- /wp:group -->
