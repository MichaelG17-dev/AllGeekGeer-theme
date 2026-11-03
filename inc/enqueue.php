<?php
/**
 * AllGeekGear — підключення стилів, скриптів і шрифтів
 * Шлях: inc/enqueue.php
 *
 * Підключається з functions.php рядком:
 *   require_once get_stylesheet_directory() . '/inc/enqueue.php';
 *
 * @package AllGeekGear
 */

defined( 'ABSPATH' ) || exit;

/**
 * Версія ассетів = час зміни файлу.
 * Так браузер завжди бачить свіжий CSS після перекомпіляції SCSS.
 */
function agg_asset_version( $relative_path ) {
	$file = get_stylesheet_directory() . '/' . ltrim( $relative_path, '/' );

	return file_exists( $file ) ? (string) filemtime( $file ) : '1.0.0';
}

/**
 * Фронтенд: стилі та скрипти.
 */
function agg_enqueue_assets() {
	$uri = get_stylesheet_directory_uri();

	// --- Шрифти -------------------------------------------------
	// Якщо локальні шрифти вже підключені через base/_fonts.scss
	// або theme.json — цей блок можна видалити.
	wp_enqueue_style(
		'agg-fonts',
		'https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Orbitron:wght@500;700;900&display=swap',
		array(),
		null
	);

	// --- Скомпільований style.css дочірньої теми ----------------
	wp_enqueue_style(
		'agg-theme',
		$uri . '/style.css',
		array( 'agg-fonts' ),
		agg_asset_version( 'style.css' )
	);

	// --- Скрипти ------------------------------------------------
	// Перемикач теми: БЕЗ defer, має спрацювати якнайраніше.
	wp_enqueue_script(
		'agg-theme-switcher',
		$uri . '/assets/js/light-night-switcher.js',
		array(),
		agg_asset_version( 'assets/js/light-night-switcher.js' ),
		true
	);

	// Інтерактив карток (3D-нахил + фольга).
	wp_enqueue_script(
		'agg-foil',
		$uri . '/assets/js/agg-foil.js',
		array(),
		agg_asset_version( 'assets/js/agg-foil.js' ),
		true
	);

	// Поява при скролі.
	wp_enqueue_script(
		'agg-reveal',
		$uri . '/assets/js/agg-reveal.js',
		array(),
		agg_asset_version( 'assets/js/agg-reveal.js' ),
		true
	);

	// Частинки — лише там, де є canvas (головна / сторінка з хіро).
	if ( is_front_page() || is_page_template() || has_block( 'core/pattern' ) ) {
		wp_enqueue_script(
			'agg-particles',
			$uri . '/assets/js/agg-particles.js',
			array(),
			agg_asset_version( 'assets/js/agg-particles.js' ),
			true
		);
	}

	// Клас .is-scrolled на хедері + мобільне меню.
	wp_add_inline_script(
		'agg-reveal',
		"(function(){
			var h=document.querySelector('.agg-header');
			if(h){
				var s=function(){h.classList.toggle('is-scrolled',window.scrollY>12);};
				s();window.addEventListener('scroll',s,{passive:true});
			}
			document.addEventListener('click',function(e){
				var b=e.target.closest('[data-agg-burger]');
				if(b){document.querySelector('.agg-header__mobile').classList.toggle('is-open');}
				var s=e.target.closest('[data-agg-scroll]');
				if(s){
					var t=s.closest('.agg-chars').querySelector('.agg-chars__track');
					t.scrollBy({left:parseInt(s.dataset.aggScroll,10)*320,behavior:'smooth'});
				}
			});
		})();"
	);
}
add_action( 'wp_enqueue_scripts', 'agg_enqueue_assets' );

/**
 * Інлайновий скрипт у <head> — прибирає спалах світлою темою.
 *
 * Виставляє клас на <html> ДО того, як браузер намалює перший кадр,
 * тому користувач ніколи не бачить «білого моргання».
 */
function agg_theme_no_flash_script() {
	?>
	<script>
		(function () {
			try {
				var t = localStorage.getItem('theme') || 'dark-mode';
				document.documentElement.classList.add(t === 'light-mode' ? 'light-mode' : 'dark-mode');
			} catch (e) {
				document.documentElement.classList.add('dark-mode');
			}
		})();
	</script>
	<?php
}
add_action( 'wp_head', 'agg_theme_no_flash_script', 1 );

/**
 * Стилі для редактора Gutenberg,
 * щоб блоки в адмінці виглядали як на фронті.
 */
function agg_editor_styles() {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
}
add_action( 'after_setup_theme', 'agg_editor_styles' );

/**
 * Скрипти всередині редактора блоків —
 * щоб картки «жили» вже у превʼю Gutenberg.
 */
function agg_enqueue_editor_assets() {
	$uri = get_stylesheet_directory_uri();

	wp_enqueue_script(
		'agg-foil-editor',
		$uri . '/assets/js/agg-foil.js',
		array(),
		agg_asset_version( 'assets/js/agg-foil.js' ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'agg_enqueue_editor_assets' );
