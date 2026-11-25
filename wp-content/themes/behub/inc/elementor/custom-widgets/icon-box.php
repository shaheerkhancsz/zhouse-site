<?php

use Elementor\Controls_Manager;

add_action('elementor/element/icon-box/section_style_content/before_section_end', function ($element, $args) {
	$element->add_control(
		'icon_box_title_hover',
		[
			'label'     => esc_html__('Color Title Hover', 'behub'),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .elementor-icon-box-wrapper:hover .elementor-icon-box-content .elementor-icon-box-title' => 'color: {{VALUE}};',
			],
		]
	);
}, 10, 2);
add_action('elementor/element/icon-box/section_style_icon/before_section_end', function ($element, $args) {
    $element->add_control(
        'icon_style_theme',
        [
            'label'        => esc_html__('Theme Style', 'behub'),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => '',
            'prefix_class' => 'icon-style-behub-',
        ]
    );

}, 10, 2);
add_action('elementor/element/icon-box/section_icon/before_section_end', function ($element, $args) {
    $element->add_control(
        'iconbox_style_theme',
        [
            'label'        => esc_html__('Theme Style', 'behub'),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => '',
            'prefix_class' => 'iconbox-style-behub-',
        ]
    );

}, 10, 2);