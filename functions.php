<?php
/**
 * AllGeekGeer Theme functions and definitions
 */

function allgeekgear_enqueue_styles() {
    // 1. Підключаємо стилі батьківської теми Twenty Twenty-Five
    wp_enqueue_style( 
        'twentytwentyfive-parent-style', 
        get_template_directory_uri() . '/style.css' 
    );

    // 2. Підключаємо кастомний style.css нашої дочірньої теми
    wp_enqueue_style( 
        'allgeekgear-child-style', 
        get_stylesheet_uri(), 
        array('twentytwentyfive-parent-style'), 
        filemtime(get_stylesheet_directory() . '/style.css') // Авто-скидання кешу при збереженні SCSS
    );

    // 3. Підключаємо ваш JS для перемикача тем (якщо потрібно)
    if (file_exists(get_stylesheet_directory() . '/assets/js/light-night-switcher.js')) {
        wp_enqueue_script(
            'allgeekgear-switcher',
            get_stylesheet_directory_uri() . '/assets/js/light-night-switcher.js',
            array(),
            filemtime(get_stylesheet_directory() . '/assets/js/light-night-switcher.js'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'allgeekgear_enqueue_styles');