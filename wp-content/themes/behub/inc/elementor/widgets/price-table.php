<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Icons_Manager;
class behub_Elementor_Price_Table extends Elementor\Widget_Base
{

    public function get_name()
    {
        return 'behub-price-table';
    }

    public function get_title()
    {
        return esc_html__('behub Price Table', 'behub');
    }

    public function get_categories()
    {
        return array('behub-addons');
    }
    public function get_script_depends() {
        return ['behub-elementor-price-table', 'slick'];
    }

    public function get_icon()
    {
        return 'eicon-price-table';
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'section_header',
            [
                'label' => esc_html__('Wrap', 'behub'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'pricing_image',
            [
                'label'      => esc_html__('Choose Image', 'behub'),
                'default'    => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'type'       => Controls_Manager::MEDIA,
                'show_label' => false,
            ]
        );

        $repeater->add_control(
            'heading',
            [
                'label' => esc_html__('Title', 'behub'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => esc_html__('Pricing Table', 'behub'),
            ]
        );

        $repeater->add_control('heading_pricing', [
            'label' => esc_html__('Pricing', 'behub'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $repeater->add_control(
            'price',
            [
                'label' => esc_html__('Price', 'behub'),
                'type' => Controls_Manager::NUMBER,
                'default' => '39.99',
            ]
        );

        $repeater->add_control(
            'symbol',
            [
                'label' => esc_html__('symbol', 'behub'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('per month', 'behub'),
                'placeholder' => esc_html__('symbol ...', 'behub'),
            ]
        );

        $repeater->add_control(
            'period',
            [
                'label' => esc_html__('Period', 'behub'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('per month', 'behub'),
                'placeholder' => esc_html__('Period ...', 'behub'),
            ]
        );
        $repeater->add_control(
            'description',
            [
                'label' => esc_html__('Title Description', 'behub'),
                'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
                'rows' => '5',
            ]
        );

        $repeater->add_control(
            'icon_text',
            [
                'label'   => esc_html__('Text', 'behub'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('List Item', 'behub'),
            ]
        );
        $repeater->add_control(
            'icon_text_one',
            [
                'label'   => esc_html__('Text', 'behub'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('List Item', 'behub'),
            ]
        );
        $repeater->add_control(
            'icon_text_two',
            [
                'label'   => esc_html__('Text', 'behub'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('List Item', 'behub'),
            ]
        );




        $repeater->add_control(
            'button_type',
            [
                'label' => esc_html__('Type', 'behub'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => esc_html__('Default', 'behub'),
                    'primary' => esc_html__('Primary', 'behub'),
                    'link' => esc_html__('Link', 'behub'),
                    'info' => esc_html__('Info', 'behub'),
                    'success' => esc_html__('Success', 'behub'),
                    'warning' => esc_html__('Warning', 'behub'),
                    'danger' => esc_html__('Danger', 'behub'),
                ],
                'prefix_class' => 'elementor-button-',
            ]
        );

        $repeater->add_control(
            'button_text',
            [
                'label' => esc_html__('Button Text', 'behub'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Click Here', 'behub'),
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__('Link', 'behub'),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'behub'),
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $repeater->add_control(
            'item_text',
            [
                'label'   => esc_html__('Text', 'behub'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('List Item', 'behub'),
            ]
        );

        $this->add_control(
            'price_table',
            [
                'label' => esc_html__('Items', 'behub'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ heading }}}',
            ]
        );

        $this->add_responsive_control(
            'column',
            [
                'label' => esc_html__('Columns', 'behub'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 1,
                'options' => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 6 => 6],
            ]
        );

        $this->end_controls_section();
        $this->add_control_carousel_customs();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        if (!empty($settings['price_table']) && is_array($settings['price_table'])) {
            $this->add_render_attribute('wrapper', 'class', 'elementor-price-table-item-wrapper');
            // Row
            $this->add_render_attribute('row', 'class', 'row');
            //item
            if ($settings['enable_carousel'] === 'yes') {
                $this->add_render_attribute('row', 'class', 'behub-carousel');
                $carousel_settings = $this->get_carousel_settings();
                $this->add_render_attribute('row', 'data-settings', wp_json_encode($carousel_settings));
            } else {

                if (!empty($settings['column_widescreen'])) {
                    $this->add_render_attribute('row', 'data-elementor-columns-widescreen', $settings['column_widescreen']);
                }

                if (!empty($settings['column'])) {
                    $this->add_render_attribute('row', 'data-elementor-columns', $settings['column']);
                } else {
                    $this->add_render_attribute('row', 'data-elementor-columns', 5);
                }

                if (!empty($settings['column_laptop'])) {
                    $this->add_render_attribute('row', 'data-elementor-columns-laptop', $settings['column_laptop']);
                }

                if (!empty($settings['column_tablet_extra'])) {
                    $this->add_render_attribute('row', 'data-elementor-columns-tablet-extra', $settings['column_tablet_extra']);
                }

                if (!empty($settings['column_tablet'])) {
                    $this->add_render_attribute('row', 'data-elementor-columns-tablet', $settings['column_tablet']);
                } else {
                    $this->add_render_attribute('row', 'data-elementor-columns-tablet', 2);
                }

                if (!empty($settings['column_mobile_extra'])) {
                    $this->add_render_attribute('row', 'data-elementor-columns-mobile-extra', $settings['column_mobile_extra']);
                }

                if (!empty($settings['column_mobile'])) {
                    $this->add_render_attribute('row', 'data-elementor-columns-mobile', $settings['column_mobile']);
                } else {
                    $this->add_render_attribute('row', 'data-elementor-columns-mobile', 1);
                }
            }
            $this->add_render_attribute('item', 'class', 'column-item elementor-price-table-item');
            ?>
               <div class="elementor-price-table__wrapper-header">
                <?php
                $pricing_number = '';
                if (!empty($settings['price'])) {
                    $pricing_string = (string)$settings['price'];
                    $pricing_array  = explode('.', $pricing_string);
                    if (isset($pricing_array[1]) && strlen($pricing_array[1]) < 2) {
                        $decimals = 1;
                    } else {
                        $decimals = 2;
                    }

                    if (count($pricing_array) < 2) {
                        $decimals = 0;
                    }

                    if (empty($settings['currency_format'])) {
                        $dec_point     = '.';
                        $thousands_sep = ',';
                    } else {
                        $dec_point     = ',';
                        $thousands_sep = '.';
                    }
                    $pricing_number = number_format($settings['price'], $decimals, $dec_point, $thousands_sep);
                }
                ?>

            <div <?php $this->print_render_attribute_string('wrapper'); ?>>
                <div <?php $this->print_render_attribute_string('row'); ?>>
                    <?php foreach ($settings['price_table'] as $price): ?>
                        <div <?php $this->print_render_attribute_string('item'); ?>>
                            <div class="price_table_inner">
                            <?php $this->render_image($settings, $price); ?>
                            <?php if (!empty($price['heading'])) : ?>
                                <span class="title"><?php echo esc_html($price["heading"]) ?></span>
                            <?php endif; ?>

                                <div class="elementor-price-table__price">
                                    <span class="elementor-price-table__integer-part"><?php echo esc_html($price['symbol']); ?></span>
                                    <span class="elementor-price-table__currency"><?php echo esc_html($price['price']); ?></span>
                                    <span class="elementor-price-table__period"><?php echo esc_html($price['period']); ?></span>
                                </div>

                                 <?php if(!empty($price['description'])): ?>
                                 <div class="description"><?php echo esc_html__($price['description'], 'behub'); ?></div>
                                   <?php endif; ?>

                                             <div class="elementor-price-table__list">

                                                 <div class="icon_text">
                                                 <i class ="behub-icon-check-circle"></i>
                                                  <?php if (!empty($price['icon_text'])) : echo esc_html($price['icon_text']); endif; ?>
                                                </div>

                                                <div class="icon_text">
                                                 <i class ="behub-icon-check-circle"></i>
                                                  <?php if (!empty($price['icon_text_one'])) : echo esc_html($price['icon_text_one']); endif; ?>
                                                </div>

                                                <div class="icon_text">
                                                 <i class ="behub-icon-check-circle"></i>
                                                  <?php if (!empty($price['icon_text_two'])) : echo esc_html($price['icon_text_two']); endif; ?>
                                                </div>

                                              </div>

                                          <div class="elementor-price-table__button">
                                                <a class="button-more-link" href="<?php echo esc_attr($price['link']['url']) ?>">
                                                    <?php if (!empty($price['button_text'])) : ?>
                                                        <span><?php echo esc_html($price['button_text']);?></span>
                                                    <?php endif; ?>
                                                </a>
                                           </div>

                        </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php } ?>

        <?php
    }
    private function render_image($settings, $price) {
        if (!empty($price['pricing_image']['url'])) :
            ?>
            <div class="pricing_image">
                <?php
                $price['pricing_image_size']             = $settings['pricing_image'];
                $price['pricing_image_custom_dimension'] = $settings['pricing_image_custom_dimension'];
                echo Group_Control_Image_Size::get_attachment_image_html($price, 'pricing_image');
                ?>
            </div>
        <?php
        endif;
    }

    //customs carousel
    protected function add_control_carousel_customs($condition = array())
    {
        $this->start_controls_section(
            'section_carousel_options',
            [
                'label' => esc_html__('Carousel Options', 'behub'),
                'type' => Controls_Manager::SECTION,
            ]
        );

        $this->add_control(
            'enable_carousel',
            [
                'label' => esc_html__('Enable', 'behub'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );


        $this->add_control(
            'navigation',
            [
                'label' => esc_html__('Navigation', 'behub'),
                'type' => Controls_Manager::SELECT,
                'default' => 'dots',
                'options' => [
                    'both' => esc_html__('Arrows and Dots', 'behub'),
                    'arrows' => esc_html__('Arrows', 'behub'),
                    'dots' => esc_html__('Dots', 'behub'),
                    'none' => esc_html__('None', 'behub'),
                ],
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => esc_html__('Pause on Hover', 'behub'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => esc_html__('Autoplay', 'behub'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => esc_html__('Autoplay Speed', 'behub'),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
                'condition' => [
                    'autoplay' => 'yes',
                    'enable_carousel' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
                ],
            ]
        );

        $this->add_control(
            'infinite',
            [
                'label' => esc_html__('Infinite Loop', 'behub'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'style_carousel',
            [
                'label'        => esc_html__('Layout Carousel', 'behub'),
                'type'         => Controls_Manager::SELECT,
                'options'      => [
                    '1' => esc_html__('Layout 1', 'behub'),
                    '2' => esc_html__('Layout 2', 'behub'),
                ],
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
                'prefix_class' => 'layout-carousel-style-',
                'default'      => '1',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'carousel_arrows',
            [
                'label' => esc_html__('Carousel Arrows', 'behub'),
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'enable_carousel',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'navigation',
                            'operator' => '!==',
                            'value' => 'none',
                        ],
                        [
                            'name' => 'navigation',
                            'operator' => '!==',
                            'value' => 'dots',
                        ],
                    ],
                ],
            ]
        );


        //Style arrow
        $this->add_responsive_control(
            'icon_size',
            [
                'label'     => esc_html__( 'Size', 'behub' ),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-arrow:before' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        //add icon next color
        $this->add_control(
            'color_button',
            [
                'label' => esc_html__('Color', 'behub'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->start_controls_tabs('tabs_carousel_arrow_style');

        $this->start_controls_tab(
            'tab_carousel_arrow_normal',
            [
                'label' => esc_html__('Normal', 'behub'),
            ]
        );

        $this->add_control(
            'carousel_arrow_color_icon',
            [
                'label'     => esc_html__('Color icon', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_border',
            [
                'label'     => esc_html__('Color Border', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_background',
            [
                'label'     => esc_html__('Color background', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_carousel_arrow_hover',
            [
                'label' => esc_html__('Hover', 'behub'),
            ]
        );

        $this->add_control(
            'carousel_arrow_color_icon_hover',
            [
                'label'     => esc_html__('Color icon', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:hover:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:hover:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_border_hover',
            [
                'label'     => esc_html__('Color Border', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:hover' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_background_hover',
            [
                'label'     => esc_html__('Color background', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'next_heading',
            [
                'label' => esc_html__( 'Next button', 'behub' ),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'next_vertical',
            [
                'label'       => esc_html__( 'Next Vertical', 'behub' ),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'top'    => [
                        'title' => esc_html__( 'Top', 'behub' ),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => esc_html__( 'Bottom', 'behub' ),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ]
            ]
        );

        $this->add_responsive_control(
            'next_vertical_value',
            [
                'type'       => Controls_Manager::SLIDER,
                'show_label' => false,
                'size_units' => [ 'px', '%' ],
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
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .slick-next' => 'top: unset; bottom: unset; {{next_vertical.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );
        $this->add_control(
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
                'defautl'     => 'right'
            ]
        );
        $this->add_responsive_control(
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
                    'size' => - 45,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .slick-next' => 'left: unset; right: unset;{{next_horizontal.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );


        $this->add_control(
            'prev_heading',
            [
                'label'     => esc_html__( 'Prev button', 'behub' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'prev_vertical',
            [
                'label'       => esc_html__( 'Prev Vertical', 'behub' ),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'top'    => [
                        'title' => esc_html__( 'Top', 'behub' ),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => esc_html__( 'Bottom', 'behub' ),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ]
            ]
        );

        $this->add_responsive_control(
            'prev_vertical_value',
            [
                'type'       => Controls_Manager::SLIDER,
                'show_label' => false,
                'size_units' => [ 'px', '%' ],
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
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .slick-prev' => 'top: unset; bottom: unset; {{prev_vertical.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );
        $this->add_control(
            'prev_horizontal',
            [
                'label'       => esc_html__( 'Prev Horizontal', 'behub' ),
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
                'defautl'     => 'left'
            ]
        );
        $this->add_responsive_control(
            'prev_horizontal_value',
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
                    'size' => - 45,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .slick-prev' => 'left: unset; right: unset; {{prev_horizontal.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'carousel_dots',
            [
                'label'      => esc_html__('Carousel Dots', 'behub'),
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name'     => 'enable_carousel',
                            'operator' => '==',
                            'value'    => 'yes',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'none',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'both',
                        ],
                        [
                            'name'     => 'navigation',
                            'operator' => '!==',
                            'value'    => 'arrows',
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'style_dots',
            [
                'label' => esc_html__( 'Style Dots', 'behub' ),
                'type'  => Controls_Manager::SELECT,
                'options'   => [
                    'style-1'   => esc_html__('Style 1', 'behub'),
                    'style-2'   => esc_html__('Style 2', 'behub'),
                ],
                'default'   => 'style-1',
                'prefix_class'  => 'dots-'
            ]
        );

        $this->start_controls_tabs('tabs_carousel_dots_style');

        $this->start_controls_tab(
            'tab_carousel_dots_normal',
            [
                'label' => esc_html__('Normal', 'behub'),
            ]
        );

        $this->add_control(
            'carousel_dots_color',
            [
                'label'     => esc_html__('Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_dots_opacity',
            [
                'label'     => esc_html__('Opacity', 'behub'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_carousel_dots_hover',
            [
                'label' => esc_html__('Hover', 'behub'),
            ]
        );

        $this->add_control(
            'carousel_dots_color_hover',
            [
                'label'     => esc_html__('Color Hover', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:hover:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .slick-dots li button:focus:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_dots_opacity_hover',
            [
                'label'     => esc_html__('Opacity', 'behub'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:hover:before' => 'opacity: {{SIZE}};',
                    '{{WRAPPER}} .slick-dots li button:focus:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_carousel_dots_activate',
            [
                'label' => esc_html__('Activate', 'behub'),
            ]
        );

        $this->add_control(
            'carousel_dots_color_activate',
            [
                'label'     => esc_html__('Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li.slick-active button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_dots_opacity_activate',
            [
                'label'     => esc_html__('Opacity', 'behub'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li.slick-active button:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'dots_vertical_value',
            [
                'label'      => esc_html__('Spacing', 'behub'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range'      => [
                    'px' => [
                        'min'  => -1000,
                        'max'  => 1000,
                        'step' => 1,
                    ],
                    '%'  => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => '',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'Alignment_text',
            [
                'label' => esc_html__('Alignment text', 'behub'),
                'type' => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__('Left', 'behub'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'behub'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__('Right', 'behub'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots' => 'text-align: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();

    }

    protected function get_carousel_settings() {
        $settings    = $this->get_settings_for_display();
        $breakpoints = \Elementor\Plugin::$instance->breakpoints->get_breakpoints();
            $tablet      = isset($settings['column_tablet']) ? $settings['column_tablet'] : 2;
            return array(
                'navigation'              => $settings['navigation'],
                'autoplayHoverPause'      => $settings['pause_on_hover'] === 'yes' ? true : false,
                'autoplay'                => $settings['autoplay'] === 'yes' ? true : false,
                'autoplaySpeed'           => !empty($settings['autoplay_speed']) ? $settings['autoplay_speed'] : 5000,
                'items'                   => $settings['column'],
                'items_laptop'            => isset($settings['column_laptop']) ? $settings['column_laptop'] : $settings['column'],
                'items_tablet_extra'      => isset($settings['column_tablet_extra']) ? $settings['column_tablet_extra'] : $settings['column'],
                'items_tablet'            => $tablet,
                'items_mobile_extra'      => isset($settings['column_mobile_extra']) ? $settings['column_mobile_extra'] : $tablet,
                'items_mobile'            => isset($settings['column_mobile']) ? $settings['column_mobile'] : 1,
                'loop'                    => $settings['infinite'] === 'yes' ? true : false,
                'breakpoint_laptop'       => $breakpoints['laptop']->get_value(),
                'breakpoint_tablet_extra' => $breakpoints['tablet_extra']->get_value(),
                'breakpoint_tablet'       => $breakpoints['tablet']->get_value(),
                'breakpoint_mobile_extra' => $breakpoints['mobile_extra']->get_value(),
                'breakpoint_mobile'       => $breakpoints['mobile']->get_value(),
            );
    }
}

$widgets_manager->register(new behub_Elementor_Price_Table());
