<?php

if (!defined('ABSPATH')) {
    exit;
}
use Elementor\Controls_Manager;

if (!class_exists('BeHub_Elementor_Section_Background')) :
    class BeHub_Elementor_Section_Background {
        public function __construct() {
            add_action('elementor/element/before_section_end', [$this, 'register_controls'], 10, 3);
        }

        /**
         * @param $element \Elementor\Controls_Stack
         * @param $section_id
         * @param $args
         */
        public function register_controls( $element, $section_id, $args ) {
            static $sections = [
                'section_background', /* Section */
            ];

            if ( ! in_array( $section_id, $sections ) ) {
                return;
            }

            $element->add_control(
                'behub_background_section_switcher',
                [
                    'label'        => esc_html__('Theme Background', 'behub'),
                    'type'         => Controls_Manager::SWITCHER,
                    'prefix_class' => 'behub-custom-background-section-',
                    'separator' => 'before',
                ]
            );

            $element->add_control(
                'behub_background_color_section',
                [
                    'label'     => esc_html__('Background Color', 'behub'),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}}.behub-custom-background-section-yes > .elementor-container:before' => 'background-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'behub_background_section_switcher' => 'yes'
                    ]
                ]
            );

            $element->add_control(
                'next_horizontal',
                [
                    'label'       => esc_html__( 'Next Horizontal', 'behub' ),
                    'type'        => Controls_Manager::CHOOSE,
                    'label_block' => false,
                    'options'     => [
                        'left'  => [
                            'title' => esc_html__( 'Left', 'behub' ),
                            'icon'  => 'eicon-h-align-left',
                        ],
                        'right' => [
                            'title' => esc_html__( 'Right', 'behub' ),
                            'icon'  => 'eicon-h-align-right',
                        ],
                    ],
                    'condition' => [
                        'behub_background_section_switcher' => 'yes'
                    ]
                ]
            );

            $element->add_responsive_control(
                'next_horizontal_value',
                [
                    'type'       => Controls_Manager::SLIDER,
                    'show_label' => false,
                    'size_units' => [ 'px', 'em', '%' ],
                    'range'      => [
                        'px' => [
                            'min'  => - 1000,
                            'max'  => 1000,
                            'step' => 1,
                        ],
                        '%'  => [
                            'min' => - 100,
                            'max' => 100,
                        ],
                    ],
                    'default'    => [
                        'unit' => 'px',
                        'size' => 0,
                    ],
                    'selectors'  => [
                        '{{WRAPPER}}.behub-custom-background-section-yes > .elementor-container:before' => 'left: unset; right: unset;{{next_horizontal.value}}: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'behub_background_section_switcher' => 'yes'
                    ]
                ]
            );

        }
    }
endif;

return new BeHub_Elementor_Section_Background();
