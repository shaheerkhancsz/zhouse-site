<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;


/**
 * Class OSF_Elementor_Blog
 */
class BeHub_Elementor_Location extends Elementor\Widget_Base {

    public function get_name() {
        return 'behub-location';
    }

    public function get_title() {
        return esc_html__('Locations', 'behub');
    }

    /**
     * Get widget icon.
     *
     * Retrieve testimonial widget icon.
     *
     * @return string Widget icon.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return array('behub-addons');
    }

    public function get_script_depends() {
        return ['behub-elementor-location', 'slick', 'isotope', 'imagesloaded'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Location', 'behub'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label'   => esc_html__('Posts Per Page', 'behub'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        $this->add_control(
            'categories',
            [
                'label'    => __('Include Categories', 'behub'),
                'type'     => Controls_Manager::SELECT2,
                'options'  => $this->get_location_categories(),
                'multiple' => true,
            ]
        );

        $this->add_control(
            'location_style',
            [
                'label'        => esc_html__('Layout', 'behub'),
                'default'      => '1',
                'type'         => Controls_Manager::SELECT,
                'options'      => [
                    '1' => esc_html__('Style 1', 'behub'),
                    '2' => esc_html__('Style 2', 'behub'),
                ],
                'prefix_class' => 'location-style-',
            ]
        );

        $this->add_responsive_control(
            'column',
            [
                'label'   => esc_html__('Columns', 'behub'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 2,
                'options' => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 6 => 6],
            ]
        );

        $this->add_responsive_control(
            'item_spacing',
            [
                'label'      => esc_html__('Spacing', 'behub'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-post-wrapper .row'         => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
                    '{{WRAPPER}} .elementor-post-wrapper .column-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2); margin-bottom: calc({{SIZE}}{{UNIT}});',
                ],
            ]
        );


        $this->add_control(
            'show_filter_bar',
            [
                'label'     => __('Filter Bar', 'behub'),
                'type'      => Controls_Manager::SWITCHER,
                'label_off' => __('Off', 'behub'),
                'label_on'  => __('On', 'behub'),
            ]
        );

        $this->add_control(
            'show_border',
            [
                'label'        => esc_html__('Border style', 'behub'),
                'type'         => Controls_Manager::SWITCHER,
                'default'      => 'no',
                'prefix_class' => 'border-style-',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_design_filter',
            [
                'label'     => __('Filter Bar', 'behub'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_filter_bar' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'align_items',
            [
                'label'       => esc_html__('Align', 'behub'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'behub'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center'     => [
                        'title' => esc_html__('Center', 'behub'),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'flex-end'   => [
                        'title' => esc_html__('Right', 'behub'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'default'     => 'center',
                'selectors'   => [
                    '{{WRAPPER}} .elementor-location__filters' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'typography_filter',
                'selector' => '{{WRAPPER}} .elementor-location__filter',
            ]
        );

        $this->start_controls_tabs('tabs_wrapper_style');

        $this->start_controls_tab(
            'tab_filter_normal',
            [
                'label' => __('Normal', 'behub'),
            ]
        );
        $this->add_control(
            'color_filter',
            [
                'label'     => __('Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-location__filter' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'background_filter',
            [
                'label'     => __('Background', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-location__filter' => 'background: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_filter_hover',
            [
                'label' => __('Active', 'behub'),
            ]
        );
        $this->add_control(
            'color_filter_active',
            [
                'label'     => __('Active Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-location__filter.elementor-active,{{WRAPPER}} .elementor-location__filter:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_filter_hover',
            [
                'label'     => __('Active Background', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-location__filter.elementor-active,{{WRAPPER}} .elementor-location__filter:hover' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'filter_item_spacing',
            [
                'label'     => __('Space Between Horizontal', 'behub'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'   => [
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-location__filter:not(:last-child)'  => 'margin-right: calc({{SIZE}}{{UNIT}}/2)',
                    '{{WRAPPER}} .elementor-location__filter:not(:first-child)' => 'margin-left: calc({{SIZE}}{{UNIT}}/2)',
                ],
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'filter_item_spacing_vertical',
            [
                'label'     => __('Space Between Vertical', 'behub'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 40,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-location__filter' => 'margin-top: calc({{SIZE}}{{UNIT}}/2);margin-bottom: calc({{SIZE}}{{UNIT}}/2);',
                ],
            ]
        );

        $this->add_responsive_control(
            'filter_spacing',
            [
                'label'     => __('Spacing', 'behub'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'   => [
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-location__filters' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();

        //customs carousel
        $this->add_control_carousel_customs();

    }

    public static function get_query_args($settings) {
        $query_args = [
            'post_type'           => 'behub_location',
            'ignore_sticky_posts' => 1,
            'post_status'         => 'publish', // Hide drafts/private posts for admins
        ];

        $query_args['posts_per_page'] = $settings['posts_per_page'];

        if (!empty($settings['categories'])) {
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'behub_location_cat',
                    'field'    => 'slug',
                    'terms'    => $settings['categories'],
                ]
            ];
        }

        if (is_front_page()) {
            $query_args['paged'] = (get_query_var('page')) ? get_query_var('page') : 1;
        } else {
            $query_args['paged'] = (get_query_var('paged')) ? get_query_var('paged') : 1;
        }

        return $query_args;
    }

    public function query_posts() {
        $query_args = $this->get_query_args($this->get_settings());
        return new WP_Query($query_args);
    }

    protected function render_filter_menu($categories) {
        $terms = [];

        if ($categories && !empty($categories)) {
            foreach ($categories as $category) {
                $term = get_term_by('slug', $category, 'behub_location_cat');

                if ($term->count != 0) {
                    $terms[$term->slug] = $term->name;
                }

                if ($term->parent == 0) {
                    $chirlds = get_term_children($term->term_id, 'behub_location_cat');

                    if (!is_wp_error($chirlds)) {
                        foreach ($chirlds as $chirld) {
                            $category               = get_term_by('term_id', $chirld, 'behub_location_cat');
                            $terms[$category->slug] = $category->name;
                        }

                    }
                }
            }
        } else {
            $terms = $this->get_location_categories();
        }

        ?>
        <ul class="elementor-location__filters" data-related="isotope-<?php echo esc_attr($this->get_id()); ?>">
            <li class="elementor-location__filter elementor-active all"
                data-filter=".__all"><?php echo __('All', 'behub'); ?><span class="count"></span></li>
            <?php $total = 0; ?>
            <?php foreach ($terms as $key => $term) {

                $term_item = get_term_by('slug', $term, 'behub_location_cat');
                $total     += $term_item->count;
                ?>
                <li class="elementor-location__filter"
                    data-filter=".<?php echo esc_attr($key); ?>"><?php echo esc_html($term); ?>
                    <span class="count">(<?php echo esc_html($term_item->count); ?>)</span></li>
            <?php } ?>
            <li class="total">(<?php echo esc_html($total); ?>)</li>
        </ul>
        <?php
    }


    protected function get_location_categories() {
        $categories = get_terms(array(
                'taxonomy'   => 'behub_location_cat',
                'hide_empty' => false,
            )
        );
        $results    = array();
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $results[$category->slug] = $category->name;
            }
        }
        return $results;

    }


    protected function render() {
        $settings = $this->get_settings_for_display();

        $query = $this->query_posts();

        if (!$query->found_posts) {
            return;
        }

//        Tab filter
        if ($this->get_settings('show_filter_bar')) {
            $this->render_filter_menu($settings['categories']);
        }

        $this->add_render_attribute('wrapper', 'class', 'elementor-post-wrapper');

        $this->add_render_attribute('row', 'class', 'row');

        if ($settings['show_filter_bar']) {
            $this->add_render_attribute('row', 'class', 'isotope-grid');

        }

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
        ?>
        <div <?php echo behub_elementor_get_render_attribute_string('wrapper', $this); // WPCS: XSS ok
        ?>>
            <div <?php echo behub_elementor_get_render_attribute_string('row', $this); // WPCS: XSS ok
            ?>>

                <?php
                while ($query->have_posts()) {
                    $query->the_post();
                    if ($settings['show_filter_bar']) {
                        $item_classes = '__all ';
                        $item_cats    = get_the_terms($query->post->ID, 'behub_location_cat');
                        foreach ((array)$item_cats as $item_cat) {
                            if (!empty($item_cats) && !is_wp_error($item_cats)) {
                                $item_classes .= $item_cat->slug . ' ';
                            }
                        }
                        echo '<div class="column-item location-entries ' . esc_attr($item_classes) . '">';
                        get_template_part('/template-parts/location/content');
                        echo '</div>';
                    } else {
                        echo '<div class="column-item">';
                        get_template_part('/template-parts/location/content');
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <?php
        wp_reset_postdata();

    }

    //customs carousel
    protected function add_control_carousel_customs($condition = array()) {
        $this->start_controls_section(
            'section_carousel_options',
            [
                'label'     => esc_html__('Carousel Options', 'behub'),
                'type'      => Controls_Manager::SECTION,
                'condition' => [
                    'show_filter_bar' => ''
                ]
            ]
        );

        $this->add_control(
            'enable_carousel',
            [
                'label' => esc_html__('Enable', 'behub'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'   => esc_html__('Columns', 'behub'),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'condition' => [
                    'enable_carousel' => 'yes',
                    'style_carousel' => '2',
                ]
            ]
        );


        $this->add_control(
            'navigation',
            [
                'label'     => esc_html__('Navigation', 'behub'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'dots',
                'options'   => [
                    'both'   => esc_html__('Arrows and Dots', 'behub'),
                    'arrows' => esc_html__('Arrows', 'behub'),
                    'dots'   => esc_html__('Dots', 'behub'),
                    'none'   => esc_html__('None', 'behub'),
                ],
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label'     => esc_html__('Pause on Hover', 'behub'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label'     => esc_html__('Autoplay', 'behub'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label'     => esc_html__('Autoplay Speed', 'behub'),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 5000,
                'condition' => [
                    'autoplay'        => 'yes',
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
                'label'     => esc_html__('Infinite Loop', 'behub'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
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
                'default'      => '1',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'carousel_arrows',
            [
                'label'      => esc_html__('Carousel Arrows', 'behub'),
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
                            'value'    => 'dots',
                        ],
                    ],
                ],
            ]
        );

        //Style arrow
        $this->add_responsive_control(
            'icon_size',
            [
                'label'     => esc_html__('Size', 'behub'),
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
                'label' => esc_html__('Next button', 'behub'),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'next_vertical',
            [
                'label'       => esc_html__('Next Vertical', 'behub'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'top'    => [
                        'title' => esc_html__('Top', 'behub'),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom', 'behub'),
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
                'label'       => esc_html__('Next Horizontal', 'behub'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'left'  => [
                        'title' => esc_html__('Left', 'behub'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'behub'),
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
                'size_units' => ['px', 'em', '%'],
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
                    'unit' => 'px',
                    'size' => -45,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .slick-next' => 'left: unset; right: unset;{{next_horizontal.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );


        $this->add_control(
            'prev_heading',
            [
                'label'     => esc_html__('Prev button', 'behub'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'prev_vertical',
            [
                'label'       => esc_html__('Prev Vertical', 'behub'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'top'    => [
                        'title' => esc_html__('Top', 'behub'),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom', 'behub'),
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
                'label'       => esc_html__('Prev Horizontal', 'behub'),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'left'  => [
                        'title' => esc_html__('Left', 'behub'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'behub'),
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
                'size_units' => ['px', 'em', '%'],
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
                    'unit' => 'px',
                    'size' => -45,
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
                'label'        => esc_html__('Style Dots', 'behub'),
                'type'         => Controls_Manager::SELECT,
                'options'      => [
                    'style-1' => esc_html__('Style 1', 'behub'),
                    'style-2' => esc_html__('Style 2', 'behub'),
                ],
                'default'      => 'style-1',
                'prefix_class' => 'dots-'
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
                'label'     => esc_html__('Alignment text', 'behub'),
                'type'      => Controls_Manager::CHOOSE,
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
        $style = $settings['style_carousel'];
        if($style == '2'){
            $tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;

            return array(
                'navigation'              => $settings['navigation'],
                'autoplayHoverPause'      => $settings['pause_on_hover'] === 'yes' ? true : false,
                'autoplay'                => $settings['autoplay'] === 'yes' ? true : false,
                'autoplaySpeed'           => !empty($settings['autoplay_speed']) ? $settings['autoplay_speed'] : 5000,
                'items'                   => $settings['columns'],
                'items_laptop'            => isset($settings['columns_laptop']) ? $settings['columns_laptop'] : $settings['columns'],
                'items_tablet_extra'      => isset($settings['columns_tablet_extra']) ? $settings['columns_tablet_extra'] : $settings['columns'],
                'items_tablet'            => $tablet,
                'items_mobile_extra'      => isset($settings['columns_mobile_extra']) ? $settings['columns_mobile_extra'] : $tablet,
                'items_mobile'            => isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1,
                'loop'                    => $settings['infinite'] === 'yes' ? true : false,
                'layout'                  => $settings['style_carousel'],
                'breakpoint_laptop'       => $breakpoints['laptop']->get_value(),
                'breakpoint_tablet_extra' => $breakpoints['tablet_extra']->get_value(),
                'breakpoint_tablet'       => $breakpoints['tablet']->get_value(),
                'breakpoint_mobile_extra' => $breakpoints['mobile_extra']->get_value(),
                'breakpoint_mobile'       => $breakpoints['mobile']->get_value(),
            );
        }else{
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
                'layout'                  => $settings['style_carousel'],
                'breakpoint_laptop'       => $breakpoints['laptop']->get_value(),
                'breakpoint_tablet_extra' => $breakpoints['tablet_extra']->get_value(),
                'breakpoint_tablet'       => $breakpoints['tablet']->get_value(),
                'breakpoint_mobile_extra' => $breakpoints['mobile_extra']->get_value(),
                'breakpoint_mobile'       => $breakpoints['mobile']->get_value(),
            );
        }
    }

}

$widgets_manager->register(new BeHub_Elementor_Location());