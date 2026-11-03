<?php
/**
 * AllGeekGeer Theme functions and definitions
 */

// Підключення модулів теми. Порядок важливий:
// shortcodes.php має йти ПЕРЕД woocommerce.php.
require_once get_stylesheet_directory() . '/inc/shortcodes.php';
require_once get_stylesheet_directory() . '/inc/post-types.php';
require_once get_stylesheet_directory() . '/inc/woocommerce.php';

add_action('wp_enqueue_scripts', function() {
    // 1. Стилі батьківської теми Twenty Twenty-Five
    wp_enqueue_style(
        'twentytwentyfive-parent-style',
        get_template_directory_uri() . '/style.css'
    );

    // 2. Шрифти дизайн-системи
    wp_enqueue_style(
        'allgeekgear-fonts',
        'https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Orbitron:wght@500;700;900&display=swap',
        [],
        null
    );

    // 3. Стилі дочірньої теми (сюди компілюється SCSS)
    $child_css = get_stylesheet_directory() . '/style.css';
    wp_enqueue_style(
        'twentytwentyfive-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        ['twentytwentyfive-parent-style', 'allgeekgear-fonts'],
        file_exists($child_css) ? filemtime($child_css) : null
    );

    // 4. Автоматичне підключення .js із assets/js/
    $js_dir = get_stylesheet_directory() . '/assets/js/';
    if (is_dir($js_dir)) {
        foreach (glob($js_dir . '*.js') as $file) {
            wp_enqueue_script(
                'allgeekgear-' . basename($file, '.js'),
                get_stylesheet_directory_uri() . '/assets/js/' . basename($file),
                [],
                filemtime($file),
                [
                    'in_footer' => true,
                    'strategy'  => 'defer',
                ]
            );
        }
    }
});

/**
 * Прибирає спалах світлою темою до спрацювання JS.
 * Має виконатись ДО першого малювання, тому не defer і не в футері.
 */
add_action('wp_head', function() {
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
}, 1);

/**
 * Хедер при скролі, бургер і стрілки слайдера персонажів.
 */
add_action('wp_footer', function() {
    ?>
    <script>
        (function () {
            var h = document.querySelector('.agg-header');
            if (h) {
                var s = function () { h.classList.toggle('is-scrolled', window.scrollY > 12); };
                s();
                window.addEventListener('scroll', s, { passive: true });
            }
            document.addEventListener('click', function (e) {
                var b = e.target.closest('[data-agg-burger]');
                if (b) {
                    var m = document.querySelector('.agg-header__mobile');
                    if (m) m.classList.toggle('is-open');
                }
                var sc = e.target.closest('[data-agg-scroll]');
                if (sc) {
                    var track = sc.closest('.agg-chars').querySelector('.agg-chars__track');
                    track.scrollBy({ left: parseInt(sc.dataset.aggScroll, 10) * 320, behavior: 'smooth' });
                }
            });
        })();
    </script>
    <?php
}, 20);

/**
 * Стилі теми всередині редактора Gutenberg.
 */
add_action('after_setup_theme', function() {
    add_theme_support('editor-styles');
    add_editor_style('style.css');
});

/**
 * Хедер поверх hero-секції на головній.
 */
add_filter('body_class', function($classes) {
    if (is_front_page()) {
        $classes[] = 'agg-has-overlay-header';
    }
    return $classes;
});
