<?php

/**
 * Option Panel
 *
 * @package Blogier
 */

function blogier_customize_register($wp_customize) {

$blogier_default = blogier_get_default_theme_options();

$wp_customize->remove_section('social_options');
	
$wp_customize->remove_control('background_color'); 

$wp_customize->add_setting('background_color',
    array(
        'default' => '#fff',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    )
);
$wp_customize->add_control('background_color',
    array(
        'label' => esc_html__('Background Color', 'blogier'),
        'section' => 'colors',
        'type' => 'color',
        'priority' => 3,
    )
);
$wp_customize->remove_section('frontpage_main_banner_section_settings');
}
add_action('customize_register', 'blogier_customize_register');