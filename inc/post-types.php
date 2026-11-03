<?php
/**
 * AllGeekGear — типи записів, таксономії та мета-поля карток
 * Шлях: inc/post-types.php
 *
 * ЦЕЙ ФАЙЛ ПОТРІБЕН, ЯКЩО ТИ НЕ ВИКОРИСТОВУЄШ WooCommerce.
 * З WooCommerce: видали реєстрацію CPT, але ЗАЛИШ таксономію
 * agg_character і мета-поля — вони просто чіпляються до 'product'.
 *
 * @package AllGeekGear
 */

defined( 'ABSPATH' ) || exit;

/**
 * До яких типів записів чіпляти мета-поля карток.
 */
function agg_card_post_types() {
	return apply_filters( 'agg_card_post_types', array( 'product', 'agg_card' ) );
}

/**
 * CPT «Картка» — якщо магазин без WooCommerce.
 */
function agg_register_post_types() {
	register_post_type(
		'agg_card',
		array(
			'labels'       => array(
				'name'          => __( 'Картки', 'allgeekgear' ),
				'singular_name' => __( 'Картка', 'allgeekgear' ),
				'add_new_item'  => __( 'Додати картку', 'allgeekgear' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-images-alt2',
			'rewrite'      => array( 'slug' => 'cards' ),
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'show_in_rest' => true, // обовʼязково для Gutenberg
		)
	);
}
add_action( 'init', 'agg_register_post_types' );

/**
 * Таксономія «Персонаж».
 */
function agg_register_taxonomies() {
	register_taxonomy(
		'agg_character',
		agg_card_post_types(),
		array(
			'labels'            => array(
				'name'          => __( 'Персонажі', 'allgeekgear' ),
				'singular_name' => __( 'Персонаж', 'allgeekgear' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'character' ),
		)
	);
}
add_action( 'init', 'agg_register_taxonomies' );

/**
 * Мета-поля картки, доступні і в REST, і в Gutenberg.
 *
 *  _agg_rarity — код рідкісності (R, SR, SSR, SP, SE, UR, OR, AR, MR, ZR, CR, GP, NR, PR, CP, TR)
 *  _agg_foil   — примусовий ефект фольги (перевизначає rarity)
 *  _agg_set    — назва серії, напр. «Tier 4 · Wave 5»
 *  _agg_stock  — залишок
 *  _agg_price  — ціна (якщо без WooCommerce)
 */
function agg_register_meta() {
	$fields = array(
		'_agg_rarity' => 'string',
		'_agg_foil'   => 'string',
		'_agg_set'    => 'string',
		'_agg_stock'  => 'integer',
		'_agg_price'  => 'string',
	);

	foreach ( agg_card_post_types() as $post_type ) {
		foreach ( $fields as $key => $type ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'          => $type,
					'single'        => true,
					'show_in_rest'  => true,
					'auth_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'agg_register_meta' );

/* =============================================================
 *  Проста метабокс-форма (щоб не ставити ACF)
 * ========================================================== */

/**
 * Список рідкісностей → назва ефекту (для підказки в адмінці).
 */
function agg_rarity_map() {
	return array(
		''    => '— не вказано —',
		'R'   => 'R — Rainbow Holo',
		'SR'  => 'SR — Sharingan Pulse',
		'SSR' => 'SSR — Rainbow Holo',
		'SP'  => 'SP — Sunburst Prism',
		'SE'  => 'SE — Radiant Crosshatch',
		'UR'  => 'UR — Gold Leaf',
		'OR'  => 'OR — Gold Leaf',
		'AR'  => 'AR — Secret Rainbow',
		'MR'  => 'MR — Cosmos Galaxy',
		'ZR'  => 'ZR — Nine-Tails Chakra',
		'CR'  => 'CR — Secret Rainbow',
		'GP'  => 'GP — Diamond Glitter',
		'NR'  => 'NR — V-Beam Lines',
		'PR'  => 'PR — Chidori Storm',
		'CP'  => 'CP — Mirror Chrome',
		'TR'  => 'TR — Rinnegan Inversion',
	);
}

/**
 * Усі доступні ефекти фольги.
 */
function agg_foil_list() {
	return array(
		''           => '— за рідкісністю —',
		'rainbow'    => 'Rainbow Holo',
		'cosmos'     => 'Cosmos Galaxy',
		'glitter'    => 'Diamond Glitter',
		'crosshatch' => 'Radiant Crosshatch',
		'gold'       => 'Gold Leaf',
		'secret'     => 'Secret Rainbow',
		'prism'      => 'Sunburst Prism',
		'flame'      => 'Nine-Tails Chakra',
		'sharingan'  => 'Sharingan Pulse',
		'lightning'  => 'Chidori Storm',
		'vbeam'      => 'V-Beam Lines',
		'glass'      => 'Mirror Chrome',
		'void'       => 'Rinnegan Inversion',
		'sand'       => 'Desert Grain',
		'ink'        => 'Ink Reveal',
		'glitch'     => 'Cyber Glitch',
	);
}

/**
 * Додає метабокс до всіх типів карток.
 */
function agg_add_meta_box() {
	foreach ( agg_card_post_types() as $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			continue;
		}

		add_meta_box(
			'agg_card_meta',
			__( 'Параметри картки', 'allgeekgear' ),
			'agg_render_meta_box',
			$post_type,
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'agg_add_meta_box' );

/**
 * Вміст метабокса.
 */
function agg_render_meta_box( $post ) {
	wp_nonce_field( 'agg_save_meta', 'agg_meta_nonce' );

	$rarity = get_post_meta( $post->ID, '_agg_rarity', true );
	$foil   = get_post_meta( $post->ID, '_agg_foil', true );
	$set    = get_post_meta( $post->ID, '_agg_set', true );
	$stock  = get_post_meta( $post->ID, '_agg_stock', true );
	$price  = get_post_meta( $post->ID, '_agg_price', true );
	?>
	<p>
		<label for="agg_rarity"><strong><?php esc_html_e( 'Рідкісність', 'allgeekgear' ); ?></strong></label><br>
		<select name="agg_rarity" id="agg_rarity" style="width:100%">
			<?php foreach ( agg_rarity_map() as $code => $label ) : ?>
				<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $rarity, $code ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>

	<p>
		<label for="agg_foil"><strong><?php esc_html_e( 'Ефект фольги', 'allgeekgear' ); ?></strong></label><br>
		<select name="agg_foil" id="agg_foil" style="width:100%">
			<?php foreach ( agg_foil_list() as $code => $label ) : ?>
				<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $foil, $code ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<span class="description"><?php esc_html_e( 'Залиш порожнім — ефект підбереться за рідкісністю.', 'allgeekgear' ); ?></span>
	</p>

	<p>
		<label for="agg_set"><strong><?php esc_html_e( 'Серія', 'allgeekgear' ); ?></strong></label><br>
		<input type="text" name="agg_set" id="agg_set" style="width:100%"
			value="<?php echo esc_attr( $set ); ?>" placeholder="Tier 4 · Wave 5">
	</p>

	<p>
		<label for="agg_stock"><strong><?php esc_html_e( 'Залишок', 'allgeekgear' ); ?></strong></label><br>
		<input type="number" name="agg_stock" id="agg_stock" style="width:100%" value="<?php echo esc_attr( $stock ); ?>">
	</p>

	<p>
		<label for="agg_price"><strong><?php esc_html_e( 'Ціна (без WooCommerce)', 'allgeekgear' ); ?></strong></label><br>
		<input type="text" name="agg_price" id="agg_price" style="width:100%" value="<?php echo esc_attr( $price ); ?>">
	</p>
	<?php
}

/**
 * Збереження метабокса.
 */
function agg_save_meta( $post_id ) {
	if ( ! isset( $_POST['agg_meta_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['agg_meta_nonce'] ) ), 'agg_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$map = array(
		'agg_rarity' => '_agg_rarity',
		'agg_foil'   => '_agg_foil',
		'agg_set'    => '_agg_set',
		'agg_stock'  => '_agg_stock',
		'agg_price'  => '_agg_price',
	);

	foreach ( $map as $field => $meta_key ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
}
add_action( 'save_post', 'agg_save_meta' );

/**
 * Категорія патернів «AllGeekGear» у вставлялці Gutenberg.
 */
function agg_register_pattern_category() {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
			'allgeekgear',
			array( 'label' => __( 'AllGeekGear', 'allgeekgear' ) )
		);
	}
}
add_action( 'init', 'agg_register_pattern_category' );
