<?php
/**
 * Title: AGG — Hero (головний екран)
 * Slug: allgeekgear/hero
 * Categories: allgeekgear
 * Description: Hero-секція з частинками чакри, великим заголовком і головною фольгованою карткою.
 * Keywords: hero, головна, чакра, картка
 * Viewport Width: 1400
 *
 * ЩО МІНЯТИ:
 *  • у [agg_card image="123"] заміни 123 на ID зображення
 *    (Медіафайли → відкрий зображення → ID у адресному рядку);
 *  • усі тексти редагуються прямо в Gutenberg;
 *  • щоб прибрати частинки — видали блок із [agg_particles].
 *
 * @package AllGeekGear
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- wp:group {"tagName":"section","metadata":{"name":"AGG Hero"},"className":"agg-hero","anchor":"top","layout":{"type":"default"}} -->
<section id="top" class="wp-block-group agg-hero">

	<!-- wp:shortcode -->
	[agg_particles max="110" density="11000" link="90" grid="yes" orbs="yes"]
	<!-- /wp:shortcode -->

	<!-- wp:group {"className":"agg-hero__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group agg-hero__inner">

		<!-- wp:group {"className":"agg-hero__copy","layout":{"type":"default"}} -->
		<div class="wp-block-group agg-hero__copy">

			<!-- wp:paragraph {"className":"agg-eyebrow agg-eyebrow--pill"} -->
			<p class="agg-eyebrow agg-eyebrow--pill">Wave 5 drop уже тут</p>
			<!-- /wp:paragraph -->

			<!-- wp:html -->
			<h1 class="agg-display">
				<span class="agg-display__line agg-display__line--primary">ALLGEEK</span>
				<span class="agg-display__line agg-display__line--accent">GEAR</span>
			</h1>
			<!-- /wp:html -->

			<!-- wp:paragraph {"className":"agg-subtitle"} -->
			<p class="agg-subtitle">Арсенал колекціонера карток <strong>Naruto Kayou</strong>. Полюй на SSR, UR та ультрарідкісні OR і AR зі справжньою голографічною фольгою.</p>
			<!-- /wp:paragraph -->

			<!-- wp:html -->
			<div class="agg-hero__actions">
				<a class="agg-btn agg-btn--primary" href="#shop">Дивитись картки <span class="agg-btn__arrow" aria-hidden="true">&rarr;</span></a>
				<a class="agg-btn agg-btn--accent" href="#foils">Фольгова скарбниця</a>
			</div>
			<!-- /wp:html -->

			<!-- wp:html -->
			<dl class="agg-stats">
				<div class="agg-stats__item">
					<dd class="agg-stats__value">12K+</dd>
					<dd class="agg-stats__label">карток у сховищі</dd>
				</div>
				<div class="agg-stats__item">
					<dd class="agg-stats__value">100%</dd>
					<dd class="agg-stats__label">оригінал Kayou</dd>
				</div>
				<div class="agg-stats__item">
					<dd class="agg-stats__value">24H</dd>
					<dd class="agg-stats__label">відправка</dd>
				</div>
			</dl>
			<!-- /wp:html -->

		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"agg-hero__showcase","layout":{"type":"default"}} -->
		<div class="wp-block-group agg-hero__showcase">

			<!-- wp:html -->
			<div class="agg-hero__halo agg-spin-slow" aria-hidden="true"></div>
			<div class="agg-hero__halo agg-hero__halo--inner" aria-hidden="true"></div>
			<!-- /wp:html -->

			<!-- wp:group {"className":"agg-float","layout":{"type":"default"}} -->
			<div class="wp-block-group agg-float">
				<!-- wp:shortcode -->
				[agg_card image="1" name="Naruto Uzumaki" subtitle="Nine-Tails Jinchuriki" rarity="CR" foil="prism" tilt="18"]
				<!-- /wp:shortcode -->
			</div>
			<!-- /wp:group -->

			<!-- wp:paragraph {"className":"agg-caption agg-hero__hint"} -->
			<p class="agg-caption agg-hero__hint">Проведи курсором по картці</p>
			<!-- /wp:paragraph -->

		</div>
		<!-- /wp:group -->

	</div>
	<!-- /wp:group -->

</section>
<!-- /wp:group -->
