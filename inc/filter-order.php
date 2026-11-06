<?php
/**
 * Безпечне сортування термів атрибутів (не чіпає адмін-панель та AJAX-запити)
 */
add_filter('get_terms_args', function($args, $taxonomies) {
    // Якщо це запит з адмінки або AJAX (наприклад, пошук значень атрибута в товарі), не чіпаємо сортування
    if (is_admin() || wp_doing_ajax()) {
        return $args;
    }

    // Наші цільові атрибути для фронтенду
    $target_taxonomies = ['pa_box-series', 'pa_characters', 'pa_rarity'];
    
    if (is_array($taxonomies) && count(array_intersect($taxonomies, $target_taxonomies)) > 0) {
        $args['orderby'] = 'term_order';
        $args['order']   = 'ASC';
    }
    
    return $args;
}, 10, 2);
?>