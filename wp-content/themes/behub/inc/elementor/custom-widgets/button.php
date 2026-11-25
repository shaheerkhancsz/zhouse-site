<?php
// Button
use Elementor\Controls_Manager;

add_action( 'elementor/element/button/section_style/after_section_end', function ($element, $args ) {

    $element->update_control(
        'background_color',
        [
            'global' => [
                'default' => '',
            ],
			'selectors' => [
				'{{WRAPPER}} .elementor-button' => ' background-color: {{VALUE}};',
			],
        ]
    );
}, 10, 2 );

add_action('elementor/element/button/section_style/before_section_end', function ($element, $args) {
    $element->add_control(
        'icon_button_size',
        [
            'label' => esc_html__('Icon Size', 'behub'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 6,
                    'max' => 300,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .elementor-button .elementor-button-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'selected_icon[value]!' => '',
            ],
        ]
    );
    $element->add_control(
        'button_icon_color',
        [
            'label'     => esc_html__('Icon Color', 'behub'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
            ],
            'condition' => [
                'selected_icon[value]!' => '',
            ],
        ]
    );

    $element->add_control(
        'button_icon_color_hover',
        [
            'label'     => esc_html__('Icon Color Hover', 'behub'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .elementor-button:hover .elementor-button-icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
            ],
            'condition' => [
                'selected_icon[value]!' => '',
            ],
        ]
    );


}, 10, 2);

add_action('elementor/element/button/section_style/before_section_end', function ($element, $args) {
    $element->add_control(
        'button_style_theme',
        [
            'label'        => esc_html__('Style', 'behub'),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'prefix_class' => 'button-style-behub-',
        ]
    );

    $element->add_control(
        'button_background_color_1',
        [
            'label'     => esc_html__('Background Color', 'behub'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}}.elementor-button-typo-default .elementor-button:hover:before' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'button_style_theme' => 'yes',
            ],
        ]
    );
    $element->add_control(
        'dot_color',
        [
            'label'     => esc_html__('Dot Color', 'behub'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .elementor-button-text:before,{{WRAPPER}} .elementor-button-text:after' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'button_style_theme' => 'yes',
            ],
        ]
    );
    $element->add_control(
        'dot_color_hover',
        [
            'label'     => esc_html__('Dot Color Hover', 'behub'),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .elementor-button:hover .elementor-button-text:before,{{WRAPPER}} .elementor-button:hover .elementor-button-text:after' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'button_style_theme' => 'yes',
            ],
        ]
    );

}, 10, 2);

add_action('elementor/element/button/section_button/after_section_start', function ($element, $args) {

    $element->add_control(
        'button_typo',
        [
            'label' => esc_html__( 'Typo', 'behub' ),
            'type' => Controls_Manager::SELECT,
            'default' => 'default',
            'options' => [
                'default' => esc_html__( 'Default', 'behub' ),
                'border' => esc_html__( 'Border', 'behub' ),
                'link' => esc_html__( 'Link', 'behub' ),
            ],
            'prefix_class' => 'elementor-button-typo-',
        ]
    );

}, 10, 2);
