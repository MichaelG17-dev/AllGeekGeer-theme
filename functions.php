<?
/**
 * AllGeekGeer Theme functions and definitions
 */

function allgeekgear_enqueue_assets() {
    // 1. Підключаємо стилі батьківської теми Twenty Twenty-Five
    wp_enqueue_style( 
        'twentytwentyfive-parent-style', 
        get_template_directory_uri() . '/style.css' 
    );

    // 2. Підключаємо головний скомпільований style.css дочірньої теми
    wp_enqueue_style( 
        'allgeekgear-child-style', 
        get_stylesheet_uri(), 
        array('twentytwentyfive-parent-style'), 
        filemtime(get_stylesheet_directory() . '/style.css')
    );

    // 3. Автоматичне підключення всіх .js файлів із папки assets/js/
    $js_dir = get_stylesheet_directory() . '/assets/js/';
    
    if (is_dir($js_dir)) {
        $files = glob($js_dir . '*.js');
        foreach ($files as $file) {
            $filename = basename($file, '.js');
            wp_enqueue_script(
                'allgeekgear-' . $filename,
                get_stylesheet_directory_uri() . '/assets/js/' . basename($file),
                array(),
                filemtime($file),
                array('in_footer' => true, 'strategy' => 'defer')
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'allgeekgear_enqueue_assets');