<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BeHub_Elementor_Breadcrumb extends Elementor\Widget_Base {

    public function get_name() {
        return 'behub-breadcrumb';
    }

    public function get_title() {
        return esc_html__('BeHub Breadcrumbs', 'behub');
    }

    public function get_icon() {
        return 'eicon-product-breadcrumbs';
    }

    public function get_keywords() {
        return ['breadcrumbs'];
    }

    public function get_categories() {
        return array('behub-addons');
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_product_rating_style',
            [
                'label' => esc_html__('Style Breadcrumbs', 'behub'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'wc_style_warning',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => esc_html__('The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'behub'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__('Text Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .breadcrumb-listItem' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'link_color',
            [
                'label'     => esc_html__('Link Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .breadcrumb-listItem a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'link_color_hover',
            [
                'label'     => esc_html__('Link Color Hover', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .breadcrumb-listItem a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'text_typography',
                'selector' => '{{WRAPPER}} .breadcrumb span',
            ]
        );

        $this->add_responsive_control(
            'alignment',
            [
                'label'     => esc_html__('Alignment', 'behub'),
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
                'selectors' => [
                    '{{WRAPPER}} .breadcrumb'    => 'text-align: {{VALUE}}',
                    '{{WRAPPER}} .behub-title' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'display_list_item',
            [
                'label'        => esc_html__('Hidden List Item', 'behub'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'prefix_class' => 'hidden-behub-list-item-',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_product_rating_style_title',
            [
                'label' => esc_html__('Style Title', 'behub'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color_title',
            [
                'label'     => esc_html__('Title Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .behub-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .behub-title',
            ]
        );

        $this->add_control(
            'display_title',
            [
                'label'        => esc_html__('Hidden Title', 'behub'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'prefix_class' => 'hidden-behub-title-',
            ]
        );

        $this->add_control(
            'display_title_single',
            [
                'label'        => esc_html__('Hidden Title Single', 'behub'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'prefix_class' => 'hidden-behub-title-single-'
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label'      => esc_html__('Margin', 'behub'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .behub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();
    }

    protected function render() {
        ?>
        <div class="breadcrumb" typeof="BreadcrumbList" vocab="https://schema.org/">
            <?php
            if (behub_is_bcn_nav_activated()) {
                echo '<div class="breadcrumb-listItem">';
                bcn_display();
                echo '</div>';
            }
            ?>
            <h1 class="behub-title">
                <?php
                if (is_page() || is_single()) {
                    the_title();
                } elseif (is_archive() && is_tax() && !is_category() && !is_tag()) {
                    $tax_object = get_queried_object();
                    echo esc_html($tax_object->name);
                } elseif (is_category()) {
                    single_cat_title();
                } elseif (is_home()) {
                    echo esc_html__('Our Blog', 'behub');
                } elseif (is_404()){
                    echo esc_html__('Error 404', 'behub');
                } elseif (is_post_type_archive()) {
                    $tax_object = get_queried_object();
                    echo esc_html($tax_object->label);
                } elseif (is_tag()) {
                    // Get tag information
                    $term_id  = get_query_var('tag_id');
                    $taxonomy = 'post_tag';
                    $args     = 'include=' . esc_attr($term_id);
                    $terms    = get_terms($taxonomy, $args);
                    // Display the tag name
                    if (isset($terms[0]->name)) {
                        echo esc_html($terms[0]->name);
                    }
                } elseif (is_day()) {
                    echo esc_html__('Day Archives', 'behub');
                } elseif (is_month()) {
                    echo get_the_time('F') . esc_html__(' Archives', 'behub');
                } elseif (is_year()) {
                    echo get_the_time('Y') . esc_html__(' Archives', 'behub');
                } elseif (is_search()) {
                    esc_html_e('Search Results', 'behub');
                } elseif (is_author()) {
                    global $author;
                    if (!empty($author)) {
                        $usermetadata = get_userdata($author);
                        echo esc_html__('Author', 'behub') . ': ' . $usermetadata->display_name;
                    }
                }
                ?>
            </h1>
        </div>
        <?php
    }
}

$widgets_manager->register(new BeHub_Elementor_Breadcrumb());
