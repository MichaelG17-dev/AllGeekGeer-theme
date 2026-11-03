<?php
/**
 * AllGeekGear — інтеграція фольгованих карток у WooCommerce
 * Шлях: inc/woocommerce.php
 *
 * Підключається з functions.php:
 *   require_once get_stylesheet_directory() . '/inc/woocommerce.php';
 *
 * ЩО ЦЕЙ ФАЙЛ РОБИТЬ
 * ------------------------------------------------------------
 * 1. Визначає рідкісність товару, читаючи ГЛОБАЛЬНИЙ АТРИБУТ
 *    WooCommerce (напр. «Ранг» зі значенням AR) — не лише
 *    мета-поле _agg_rarity.
 * 2. Підміняє стандартну мініатюру в каталозі на фольговану
 *    картку, щоб ефект при наведенні працював на звичайних
 *    сторінках магазину, категорій і в блоці [products].
 * 3. Вішає data-rarity на <li class="product">, щоб можна було
 *    стилізувати саму комірку каталогу.
 *
 * @package AllGeekGear
 */

defined( 'ABSPATH' ) || exit;

/* =============================================================
 *  1. ВИЗНАЧЕННЯ РІДКІСНОСТІ Й ЕФЕКТУ
 * ========================================================== */

/**
 * Коди рідкісності, які ми вважаємо валідними.
 *
 * Саме за цим списком скрипт упізнає потрібний атрибут, хоч би
 * як той атрибут називався — «Ранг», «Rarity», «Рідкісність».
 */
function agg_rarity_codes() {
	return apply_filters(
		'agg_rarity_codes',
		array( 'R', 'SR', 'SSR', 'SP', 'SE', 'UR', 'OR', 'AR', 'MR', 'ZR', 'CR', 'GP', 'NR', 'PR', 'CP', 'TR' )
	);
}

/**
 * Усі слаги ефектів фольги.
 */
function agg_foil_slugs() {
	return array(
		'rainbow', 'cosmos', 'glitter', 'crosshatch', 'gold', 'secret',
		'prism', 'flame', 'sharingan', 'lightning', 'vbeam', 'glass',
		'void', 'sand', 'ink', 'glitch',
	);
}

/**
 * Дістає рідкісність товару.
 *
 * Порядок пошуку:
 *   1. мета-поле _agg_rarity (ручне перевизначення);
 *   2. атрибут із таксономії, слаг якої містить 'rarity' або 'ранг';
 *   3. БУДЬ-ЯКИЙ атрибут, значення якого схоже на код рідкісності;
 *   4. кастомний (не глобальний) атрибут товару.
 *
 * @param WC_Product|int|null $product Товар або його ID.
 * @return string Код у верхньому регістрі або ''.
 */
function agg_get_product_rarity( $product = null ) {
	$product = agg_resolve_product( $product );

	if ( ! $product ) {
		return '';
	}

	// --- 1. Ручне перевизначення через мета-поле ---------------
	$meta = get_post_meta( $product->get_id(), '_agg_rarity', true );

	if ( $meta ) {
		return strtoupper( trim( (string) $meta ) );
	}

	$codes = agg_rarity_codes();

	// --- 2–4. Перебираємо атрибути товару ----------------------
	foreach ( $product->get_attributes() as $attribute ) {

		// Кастомний (локальний) атрибут — значення лежать рядком.
		if ( ! $attribute->is_taxonomy() ) {
			foreach ( (array) $attribute->get_options() as $option ) {
				$value = strtoupper( trim( (string) $option ) );

				if ( in_array( $value, $codes, true ) ) {
					return $value;
				}
			}

			continue;
		}

		// Глобальний атрибут — значення це терміни таксономії.
		$terms = wc_get_product_terms(
			$product->get_id(),
			$attribute->get_name(),
			array( 'fields' => 'all' )
		);

		foreach ( $terms as $term ) {
			// Перевіряємо і назву, і слаг: «AR» та «ar».
			foreach ( array( $term->name, $term->slug ) as $candidate ) {
				$value = strtoupper( trim( (string) $candidate ) );

				if ( in_array( $value, $codes, true ) ) {
					return $value;
				}
			}
		}
	}

	return '';
}

/**
 * Дістає примусовий ефект фольги (якщо заданий).
 *
 * Шукає мета-поле _agg_foil, а потім атрибут зі значенням на
 * кшталт 'flame' чи 'gold'.
 *
 * @param WC_Product|int|null $product Товар або його ID.
 * @return string Слаг ефекту або ''.
 */
function agg_get_product_foil( $product = null ) {
	$product = agg_resolve_product( $product );

	if ( ! $product ) {
		return '';
	}

	$meta = get_post_meta( $product->get_id(), '_agg_foil', true );

	if ( $meta ) {
		return sanitize_key( $meta );
	}

	$foils = agg_foil_slugs();

	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! $attribute->is_taxonomy() ) {
			foreach ( (array) $attribute->get_options() as $option ) {
				$value = sanitize_key( $option );

				if ( in_array( $value, $foils, true ) ) {
					return $value;
				}
			}

			continue;
		}

		$terms = wc_get_product_terms(
			$product->get_id(),
			$attribute->get_name(),
			array( 'fields' => 'slugs' )
		);

		foreach ( $terms as $slug ) {
			$value = sanitize_key( $slug );

			if ( in_array( $value, $foils, true ) ) {
				return $value;
			}
		}
	}

	return '';
}

/**
 * Приводить що завгодно до об'єкта WC_Product.
 */
function agg_resolve_product( $maybe = null ) {
	if ( $maybe instanceof WC_Product ) {
		return $maybe;
	}

	if ( is_numeric( $maybe ) ) {
		return wc_get_product( (int) $maybe );
	}

	// Нічого не передали — беремо поточний товар циклу.
	global $product;

	return ( $product instanceof WC_Product ) ? $product : null;
}

/* =============================================================
 *  2. ПІДМІНА МІНІАТЮРИ В КАТАЛОЗІ
 * ========================================================== */

/**
 * Прибираємо стандартну мініатюру WooCommerce і малюємо замість
 * неї фольговану картку.
 *
 * Хук спрацьовує і на /shop/, і на сторінках категорій,
 * і всередині шорткоду [products].
 */
function agg_swap_loop_thumbnail() {
	// Якщо файл карток не підключено — нічого не чіпаємо.
	if ( ! function_exists( 'agg_render_foil_card' ) ) {
		return;
	}

	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	add_action( 'woocommerce_before_shop_loop_item_title', 'agg_loop_foil_card', 10 );
}
add_action( 'wp', 'agg_swap_loop_thumbnail' );

/**
 * Виводить фольговану картку замість мініатюри.
 */
function agg_loop_foil_card() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$thumb_id = $product->get_image_id();

	// Немає зображення — повертаємось до стандартного плейсхолдера.
	if ( ! $thumb_id ) {
		echo wc_placeholder_img( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput

		return;
	}

	echo agg_render_foil_card( // phpcs:ignore WordPress.Security.EscapeOutput
		array(
			'image'  => $thumb_id,
			'alt'    => $product->get_name(),
			'rarity' => agg_get_product_rarity( $product ),
			'foil'   => agg_get_product_foil( $product ),
			'tilt'   => '12',
			'badge'  => 'yes',
		)
	);
}

/* =============================================================
 *  3. data-rarity НА КОМІРЦІ КАТАЛОГУ
 * ========================================================== */

/**
 * Додає клас рідкісності до <li class="product …>.
 *
 * Зручно, якщо захочеш підсвічувати рамку самої комірки.
 */
function agg_product_loop_class( $classes, $class = '', $post_id = 0 ) {
	$rarity = agg_get_product_rarity( $post_id ? $post_id : get_the_ID() );

	if ( $rarity ) {
		$classes[] = 'agg-has-rarity';
		$classes[] = 'agg-rarity-' . strtolower( $rarity );
	}

	return $classes;
}
add_filter( 'post_class', 'agg_product_loop_class', 10, 3 );

/* =============================================================
 *  4. ГОЛОВНЕ ЗОБРАЖЕННЯ НА СТОРІНЦІ ТОВАРУ
 *
 *  Вимкнено за замовчуванням: галерея WooCommerce має власний
 *  зум і лайтбокс, які конфліктують із 3D-нахилом.
 *  Щоб увімкнути — додай у functions.php:
 *      add_filter( 'agg_foil_on_single', '__return_true' );
 * ========================================================== */

function agg_swap_single_image() {
	if ( ! apply_filters( 'agg_foil_on_single', false ) ) {
		return;
	}

	if ( ! function_exists( 'agg_render_foil_card' ) ) {
		return;
	}

	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	add_action( 'woocommerce_before_single_product_summary', 'agg_single_foil_card', 20 );
}
add_action( 'wp', 'agg_swap_single_image' );

function agg_single_foil_card() {
	global $product;

	if ( ! $product instanceof WC_Product || ! $product->get_image_id() ) {
		return;
	}

	echo '<div class="agg-single-foil">';
	echo agg_render_foil_card( // phpcs:ignore WordPress.Security.EscapeOutput
		array(
			'image'  => $product->get_image_id(),
			'alt'    => $product->get_name(),
			'rarity' => agg_get_product_rarity( $product ),
			'foil'   => agg_get_product_foil( $product ),
			'tilt'   => '18',
			'size'   => 'lg',
		)
	);
	echo '</div>';
}
