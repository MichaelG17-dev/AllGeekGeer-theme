<?
/**
 * AllGeekGeer Theme functions and definitions
 */

add_action('wp_enqueue_scripts', function() {
    // 1. Підключаємо стилі батьківської теми Twenty Twenty-Five
    wp_enqueue_style(
        'twentytwentyfive-parent-style',
        get_template_directory_uri() . '/style.css'
    );

    // 2. Підключаємо стилі дочірньої теми (сюди компілюється ваш SCSS через npm)
    wp_enqueue_style(
        'twentytwentyfive-child-style',
        get_stylesheet_uri(),
        ['twentytwentyfive-parent-style'],
        filemtime(get_stylesheet_directory() . '/style.css')
    );

    // 3. Автоматичне підключення ваших .js файлів із папки assets/js/
    $js_dir = get_stylesheet_directory() . '/assets/js/';
    if (is_dir($js_dir)) {
        foreach (glob($js_dir . '*.js') as $file) {
            wp_enqueue_script(
                'allgeekgear-' . basename($file, '.js'),
                get_stylesheet_directory_uri() . '/assets/js/' . basename($file),
                [],
                filemtime($file),
                ['in_footer' => true, 'strategy' => 'defer']
            );
        }
    }
});