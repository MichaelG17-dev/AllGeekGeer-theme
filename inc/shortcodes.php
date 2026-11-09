<?php
/**
 * AllGeekGear — шорткоди секцій і карток
 * Шлях: inc/shortcodes.php
 *
 * Підключається з functions.php:
 *   require_once get_stylesheet_directory() . '/inc/shortcodes.php';
 *
 * Усі шорткоди можна вставляти в Gutenberg блоком «Шорткод»
 * або просто писати в блоці «Абзац» — WordPress їх обробить.
 *
 * @package AllGeekGear
 */

defined( 'ABSPATH' ) || exit;

/* =============================================================
 *  ДОПОМІЖНІ ФУНКЦІЇ
 * ========================================================== */

/**
 * Перетворює ID вкладення або URL у готовий URL зображення.
 */
function agg_resolve_image( $value, $size = 'large' ) {
	if ( is_numeric( $value ) ) {
		$url = wp_get_attachment_image_url( (int) $value, $size );

		return $url ? $url : '';
	}

	return esc_url_raw( (string) $value );
}

/**
 * Рендерить ОДНУ фольговану картку.
 *
 * @param array $args image, alt, name, subtitle, rarity, foil, tilt, badge, href.
 * @return string HTML.
 */
function agg_render_foil_card( $args = array() ) {
	$a = wp_parse_args(
		$args,
		array(
			'image'    => '',
			'alt'      => '',
			'name'     => '',
			'subtitle' => '',
			'rarity'   => '',
			'foil'     => '', // перевизначає ефект, обраний за rarity
			'tilt'     => '14',
			'badge'    => 'yes',
			'size'     => '', // sm | md | lg
			'href'     => '',
		)
	);

	$image = agg_resolve_image( $a['image'] );

	if ( ! $image ) {
		return '';
	}

	$wrap_class = 'agg-foil-wrap';
	if ( $a['size'] ) {
		$wrap_class .= ' agg-foil-wrap--' . sanitize_html_class( $a['size'] );
	}

	// Атрибути самої картки
	$attrs = array(
		'class'       => 'agg-foil',
		'data-active' => 'false',
		'data-tilt'   => $a['tilt'],
		'style'       => '--agg-art:url(' . esc_url( $image ) . ')',
	);

	if ( $a['rarity'] ) {
		$attrs['data-rarity'] = strtoupper( $a['rarity'] );
	}

	if ( $a['foil'] ) {
		$attrs['data-foil'] = sanitize_key( $a['foil'] );
	}

	$attr_html = '';
	foreach ( $attrs as $key => $value ) {
		$attr_html .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr( $wrap_class ); ?>">
		<?php if ( $a['href'] ) : ?>
			<a class="agg-foil-link" href="<?php echo esc_url( $a['href'] ); ?>">
		<?php endif; ?>

		<article<?php echo $attr_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<img class="agg-foil__art"
				src="<?php echo esc_url( $image ); ?>"
				alt="<?php echo esc_attr( $a['alt'] ? $a['alt'] : $a['name'] ); ?>"
				loading="lazy"
				draggable="false">

			<?php if ( 'ink' === sanitize_key( $a['foil'] ) ) : ?>
				<?php /* Ефект «Ink Reveal» потребує другої, кольорової копії арту */ ?>
				<img class="agg-foil__ink" src="<?php echo esc_url( $image ); ?>" alt="" aria-hidden="true" draggable="false">
			<?php endif; ?>

			<?php if ( $a['name'] ) : ?>
				<div class="agg-foil__plate">
					<?php if ( $a['subtitle'] ) : ?>
						<p class="agg-foil__subtitle"><?php echo esc_html( $a['subtitle'] ); ?></p>
					<?php endif; ?>
					<p class="agg-foil__name"><?php echo esc_html( $a['name'] ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( $a['rarity'] && 'yes' === $a['badge'] ) : ?>
				<span class="agg-badge agg-badge--on-card" data-rarity="<?php echo esc_attr( strtoupper( $a['rarity'] ) ); ?>">
					<?php echo esc_html( strtoupper( $a['rarity'] ) ); ?>
				</span>
			<?php endif; ?>

			<div class="agg-foil__fx" aria-hidden="true"></div>
			<div class="agg-foil__glare" aria-hidden="true"></div>
			<div class="agg-foil__frame" aria-hidden="true"></div>
		</article>

		<?php if ( $a['href'] ) : ?>
			</a>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/* =============================================================
 *  [agg_card] — одна картка
 *
 *  [agg_card image="123" name="Naruto Uzumaki"
 *            subtitle="Nine-Tails Jinchuriki" rarity="OR" tilt="18"]
 * ========================================================== */
function agg_shortcode_card( $atts ) {
	return agg_render_foil_card( (array) $atts );
}
add_shortcode( 'agg_card', 'agg_shortcode_card' );

/* =============================================================
 *  [agg_particles] — фоновий canvas із частинками чакри
 *
 *  [agg_particles max="110" density="11000" link="90"]
 * ========================================================== */
function agg_shortcode_particles( $atts ) {
	$a = shortcode_atts(
		array(
			'max'     => '110',
			'density' => '11000',
			'link'    => '90',
			'grid'    => 'yes', // кібер-сітка
			'orbs'    => 'yes', // розмиті кольорові плями
		),
		$atts
	);

	ob_start();
	?>
	<div class="agg-hero__bg" aria-hidden="true">
		<?php if ( 'yes' === $a['grid'] ) : ?>
			<div class="agg-cyber-grid"></div>
		<?php endif; ?>

		<?php if ( 'yes' === $a['orbs'] ) : ?>
			<div class="agg-orb agg-orb--primary agg-hero__orb-1"></div>
			<div class="agg-orb agg-orb--accent agg-hero__orb-2"></div>
		<?php endif; ?>

		<canvas class="agg-hero__particles"
			data-agg-particles
			data-max="<?php echo esc_attr( $a['max'] ); ?>"
			data-density="<?php echo esc_attr( $a['density'] ); ?>"
			data-link="<?php echo esc_attr( $a['link'] ); ?>"></canvas>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'agg_particles', 'agg_shortcode_particles' );

/* =============================================================
 *  [agg_marquee] — бігуча строка
 *
 *  [agg_marquee items="SSR|Holo Foil|UR|Kayou Authentic" speed="32s"]
 * ========================================================== */
function agg_shortcode_marquee( $atts ) {
	$a = shortcode_atts(
		array(
			'items'   => 'SSR|Holo Foil|UR|Kayou Authentic|CR|Tier 5 Packs|Graded Ready|Wave 5',
			'speed'   => '32s',
			'reverse' => 'no',
		),
		$atts
	);

	$items = array_filter( array_map( 'trim', explode( '|', $a['items'] ) ) );

	if ( empty( $items ) ) {
		return '';
	}

	$group = '';
	foreach ( $items as $item ) {
		$group .= '<span class="agg-marquee__item">' . esc_html( $item ) . '</span>';
		$group .= '<span class="agg-marquee__sep"></span>';
	}

	$group = '<div class="agg-marquee__group">' . $group . '</div>';

	$class = 'agg-marquee';
	if ( 'yes' === $a['reverse'] ) {
		$class .= ' agg-marquee--reverse';
	}

	return sprintf(
		'<div class="%1$s" aria-hidden="true" style="--agg-marquee-speed:%2$s"><div class="agg-marquee__track">%3$s%3$s</div></div>',
		esc_attr( $class ),
		esc_attr( $a['speed'] ),
		$group // Дублюємо РІВНО двічі — це дає безшовний цикл.
	);
}
add_shortcode( 'agg_marquee', 'agg_shortcode_marquee' );

/* =============================================================
 *  [agg_characters] — слайдер персонажів
 *
 *  Тягне терміни таксономії (типово 'agg_character').
 *  [agg_characters taxonomy="agg_character" limit="12"]
 *
 *  Картинку терміна бери з поля 'thumbnail_id' (ACF / term meta).
 * ========================================================== */
function agg_shortcode_characters( $atts ) {
	$a = shortcode_atts(
		array(
			'taxonomy' => 'agg_character',
			'limit'    => '12',
		),
		$atts
	);

	if ( ! taxonomy_exists( $a['taxonomy'] ) ) {
		return '<!-- agg_characters: таксономія ' . esc_html( $a['taxonomy'] ) . ' не існує -->';
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $a['taxonomy'],
			'hide_empty' => false,
			'number'     => (int) $a['limit'],
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="agg-chars">
		<div class="agg-chars__nav">
			<button type="button" class="agg-icon-btn" data-agg-scroll="-1" aria-label="<?php esc_attr_e( 'Прокрутити назад', 'allgeekgear' ); ?>">&#8592;</button>
			<button type="button" class="agg-icon-btn" data-agg-scroll="1" aria-label="<?php esc_attr_e( 'Прокрутити вперед', 'allgeekgear' ); ?>">&#8594;</button>
		</div>

		<div class="agg-chars__track agg-no-scrollbar" role="tablist" aria-label="<?php esc_attr_e( 'Фільтр за персонажем', 'allgeekgear' ); ?>">
			<?php foreach ( $terms as $term ) : ?>
				<?php
				$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
				$thumb    = $thumb_id ? wp_get_attachment_image_url( (int) $thumb_id, 'medium' ) : '';
				?>
				<a class="agg-chars__item"
					href="<?php echo esc_url( get_term_link( $term ) ); ?>"
					role="tab"
					aria-selected="false">
					<span class="agg-chars__ring">
						<?php if ( $thumb ) : ?>
							<img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy">
						<?php endif; ?>
					</span>
					<span class="agg-chars__label"><?php echo esc_html( $term->name ); ?></span>
					<span class="agg-chars__count">
						<?php
						printf(
							/* translators: %d — кількість карток */
							esc_html( _n( '%d картка', '%d карток', $term->count, 'allgeekgear' ) ),
							(int) $term->count
						);
						?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'agg_characters', 'agg_shortcode_characters' );

/* =============================================================
 *  [agg_foil_showcase] — вітрина всіх 16 фіній
 *
 *  Демонстраційна сітка: один і той самий арт із різними ефектами.
 *  [agg_foil_showcase image="123"]
 * ========================================================== */
function agg_shortcode_foil_showcase( $atts ) {
	$a = shortcode_atts(
		array(
			'image' => '',
			'name'  => '',
		),
		$atts
	);

	$foils = array(
		'rainbow'   => 'Rainbow Holo',
		'cosmos'    => 'Cosmos Galaxy',
		'glitter'   => 'Diamond Glitter',
		'crosshatch' => 'Radiant Crosshatch',
		'gold'      => 'Gold Leaf',
		'secret'    => 'Secret Rainbow',
		'prism'     => 'Sunburst Prism',
		'flame'     => 'Nine-Tails Chakra',
		'sharingan' => 'Sharingan Pulse',
		'lightning' => 'Chidori Storm',
		'vbeam'     => 'V-Beam Lines',
		'glass'     => 'Mirror Chrome',
		'void'      => 'Rinnegan Inversion',
		'sand'      => 'Desert Grain',
		'ink'       => 'Ink Reveal',
		'glitch'    => 'Cyber Glitch',
	);

	ob_start();
	?>
	<div class="agg-grid agg-grid--foils agg-stagger" role="radiogroup" aria-label="<?php esc_attr_e( 'Варіанти фольги', 'allgeekgear' ); ?>">
		<?php
		$i = 0;
		foreach ( $foils as $slug => $label ) :
			$i++;
			?>
			<div class="agg-foil-item agg-reveal" role="radio" aria-checked="false" tabindex="0">
				<div class="agg-foil-item__frame">
					<?php
					echo agg_render_foil_card( // phpcs:ignore WordPress.Security.EscapeOutput
						array(
							'image' => $a['image'],
							'alt'   => $label,
							'name'  => $a['name'],
							'foil'  => $slug,
							'tilt'  => '16',
						)
					);
					?>
					<span class="agg-foil-item__check" aria-hidden="true">&#10003;</span>
				</div>
				<div class="agg-foil-item__meta">
					<h3 class="agg-foil-item__name">
						<span class="agg-foil-item__num"><?php echo esc_html( str_pad( (string) $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<?php echo esc_html( $label ); ?>
					</h3>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'agg_foil_showcase', 'agg_shortcode_foil_showcase' );

/* =============================================================
 *  Вибір фольги мишкою/клавіатурою у вітрині
 * ========================================================== */
function agg_foil_showcase_script() {
	?>
	<script>
		document.addEventListener('click', function (e) {
			var item = e.target.closest('.agg-foil-item');
			if (!item) return;
			var on = item.getAttribute('aria-checked') === 'true';
			item.closest('[role="radiogroup"]').querySelectorAll('.agg-foil-item')
				.forEach(function (n) { n.setAttribute('aria-checked', 'false'); });
			item.setAttribute('aria-checked', on ? 'false' : 'true');
		});
		document.addEventListener('keydown', function (e) {
			if (e.key !== 'Enter' && e.key !== ' ') return;
			var item = e.target.closest('.agg-foil-item');
			if (!item) return;
			e.preventDefault();
			item.click();
		});
	</script>
	<?php
}
add_action( 'wp_footer', 'agg_foil_showcase_script', 20 );
