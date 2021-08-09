<?php

/**
 * Option Panel
 *
 * @package Newsbulk
 */


function newsbulk_customize_register($wp_customize) {

$newsup_default = newsbulk_get_default_theme_options();


//section title
$wp_customize->add_setting('recent_post_section_title',
    array(
        'sanitize_callback' => 'sanitize_text_field',
    )
);

$wp_customize->add_control(
    new newsup_Section_Title(
        $wp_customize,
        'recent_post_section_title',
        array(
            'label'             => esc_html__( 'Recent Post Section', 'newsbulk' ),
            'section'           => 'frontpage_main_banner_section_settings',
            'priority'          => 100,
            'active_callback' => 'newsup_main_banner_section_status'
        )
    )
);


// Setting - drop down category for slider.
$wp_customize->add_setting('select_recent_news_category',
    array(
        'default' => $newsup_default['select_recent_news_category'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'absint',
    )
);


$wp_customize->add_control(new Newsup_Dropdown_Taxonomies_Control($wp_customize, 'select_recent_news_category',
    array(
        'label' => esc_html__('Category', 'newsbulk'),
        'description' => esc_html__('Posts to be shown on recent 4 Post', 'newsbulk'),
        'section' => 'frontpage_main_banner_section_settings',
        'type' => 'dropdown-taxonomies',
        'taxonomy' => 'category',
        'priority' => 100,
        'active_callback' => 'newsup_main_banner_section_status'
    )));
}
add_action('customize_register', 'newsbulk_customize_register');
