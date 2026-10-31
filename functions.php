<?php
if (!defined('ABSPATH')) {
	exit; // Захист від прямого доступу
}

add_action('wp_enqueue_scripts', function () {
	// 1. Підключаємо стилі батьківської теми Blocksy
	wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

	// 2. Підключаємо style.css дочірньої теми
	wp_enqueue_style('child-style', get_stylesheet_uri(), array('parent-style'));

	// 3. Підключаємо кастомний JS (з автоматичним оновленням кешу через filemtime)
	wp_enqueue_script(
		'blocksy-child-custom',
		get_stylesheet_directory_uri() . '/assets/js/custom.js',
		array('jquery'),
		filemtime(get_stylesheet_directory() . '/assets/js/custom.js'),
		true
	);
});