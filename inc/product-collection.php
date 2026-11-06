<?php
/**
 * AllGeekGear — фольговані картки в блоці «Product Collection»
 * Шлях: inc/product-collection.php
 *
 * Підключається з functions.php ПІСЛЯ shortcodes.php і woocommerce.php:
 *   require_once get_stylesheet_directory() . '/inc/product-collection.php';
 *
 * ЯК ЦЕ ПРАЦЮЄ
 * ------------------------------------------------------------
 * 1. У Gutenberg додаєш блок «Product Collection» і в
 *    «Додатково → Додаткові CSS-класи» пишеш:  agg-default-product
 * 2. Поки рендериться саме такий блок, ми підміняємо зображення
 *    кожного товару (блок woocommerce/product-image) на фольговану
 *    картку .agg-foil — ту саму, що й у шорткоді [agg_card].
 * 3. Ефект при наведенні обирається за рідкісністю товару
 *    (атрибут «Rarity», напр. AR → secret, UR → gold).
 *    Мапа «рідкісність → ефект» — у _agg-foil-effects.scss.
 *
 * Інші блоки Product Collection без цього класу не чіпаються.
 * Увімкнути для ВСІХ колекцій можна фільтром у functions.php:
 *   add_filter( 'agg_foil_all_product_collections', '__return_true' );
 *
 * @package AllGeekGear
 */

defined( 'ABSPATH' ) || exit;

/** CSS-клас, що вмикає фольговані картки в колекції. */
const AGG_PC_CLASS = 'agg-default-product';

/**
 * Чи має блок колекції наш клас.
 */
function agg_pc_is_foil_collection( $parsed_block ) {
	if ( empty( $parsed_block['blockName'] ) || 'woocommerce/product-collection' !== $parsed_block['blockName'] ) {
		return false;
	}

	if ( apply_filters( 'agg_foil_all_product_collections', false ) ) {
		return true;
	}

	$class = isset( $parsed_block['attrs']['className'] ) ? (string) $parsed_block['attrs']['className'] : '';

	return (bool) preg_match( '/(^|\s)' . preg_quote( AGG_PC_CLASS, '/' ) . '(\s|$)/', $class );
}

/**
 * Лічильник «ми зараз усередині потрібної колекції».
 * Лічильник, а не true/false — на випадок вкладених колекцій.
 */
function agg_pc_depth( $delta = 0 ) {
	static $depth = 0;
	$depth = max( 0, $depth + $delta );

	return $depth;
}

/**
 * Вхід у колекцію — до того, як рендеряться дочірні блоки.
 */
function agg_pc_enter( $pre_render, $parsed_block ) {
	if ( null === $pre_render && agg_pc_is_foil_collection( $parsed_block ) ) {
		agg_pc_depth( 1 );
	}

	return $pre_render;
}
add_filter( 'pre_render_block', 'agg_pc_enter', 10, 2 );

/**
 * Вихід із колекції — коли вона вже відрендерилась.
 */
function agg_pc_leave( $block_content, $block ) {
	if ( agg_pc_is_foil_collection( $block ) ) {
		agg_pc_depth( -1 );
	}

	return $block_content;
}
add_filter( 'render_block_woocommerce/product-collection', 'agg_pc_leave', 10, 2 );

/**
 * Підміна <img> у блоці «Product Image» на фольговану картку.
 *
 * @param string   $block_content Готовий HTML блока.
 * @param array    $block         Розібраний блок.
 * @param WP_Block $instance      Екземпляр (тут лежить postId).
 */
function agg_pc_foil_image( $block_content, $block, $instance = null ) {
	if ( agg_pc_depth() < 1 || ! function_exists( 'agg_render_foil_card' ) ) {
		return $block_content;
	}

	$product_id = 0;
	if ( $instance instanceof WP_Block && ! empty( $instance->context['postId'] ) ) {
		$product_id = (int) $instance->context['postId'];
	}

	$product = $product_id ? wc_get_product( $product_id ) : null;

	if ( ! $product ) {
		return $block_content;
	}

	$image_id    = $product->get_image_id();
	$placeholder = ! $image_id;
	$rarity      = function_exists( 'agg_get_product_rarity' ) ? agg_get_product_rarity( $product ) : '';
	$foil        = function_exists( 'agg_get_product_foil' ) ? agg_get_product_foil( $product ) : '';

	$card = agg_render_foil_card(
		array(
			'image'  => $placeholder ? wc_placeholder_img_src( 'woocommerce_single' ) : $image_id,
			'alt'    => $product->get_name(),
			'rarity' => $rarity,
			'foil'   => $foil,
			'tilt'   => '12', // як max={12} у v0 ProductCard
			'badge'  => 'yes',
		)
	);

	if ( ! $card ) {
		return $block_content;
	}

	if ( $placeholder ) {
		$card = preg_replace( '/class="agg-foil-wrap/', 'class="agg-foil-wrap agg-foil-wrap--placeholder', $card, 1 );
	}

	// Замінюємо лише перший <img> — посилання, бейдж «Розпродаж»
	// і директиви Interactivity API лишаються як були.
	$replaced = preg_replace( '/<img\b[^>]*>/i', str_replace( array( '\\', '$' ), array( '\\\\', '\\$' ), $card ), $block_content, 1, $count );

	if ( ! $count ) {
		return $block_content;
	}

	// Позначка для CSS: ця обгортка тепер містить фольговану картку.
	return preg_replace(
		'/class="wc-block-components-product-image\b/',
		'class="wc-block-components-product-image agg-pc-image',
		$replaced,
		1
	);
}
add_filter( 'render_block_woocommerce/product-image', 'agg_pc_foil_image', 10, 3 );
