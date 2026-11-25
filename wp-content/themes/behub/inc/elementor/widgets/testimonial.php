<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;

class BeHub_Elementor_Testimonials extends Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve testimonial widget name.
     *
     * @return string Widget name.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_name() {
        return 'behub-testimonials';
    }

    /**
     * Get widget title.
     *
     * Retrieve testimonial widget title.
     *
     * @return string Widget title.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_title() {
        return esc_html__('BeHub Testimonials', 'behub');
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
        return 'eicon-testimonial';
    }

    public function get_script_depends() {
        return ['behub-elementor-testimonial', 'slick'];
    }

    public function get_categories() {
        return array('behub-addons');
    }

    /**
     * Register testimonial widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function register_controls() {
        $this->start_controls_section(
            'section_testimonial',
            [
                'label' => esc_html__('Testimonials', 'behub'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'testimonial_title',
            [
                'label'   => esc_html__('Title', 'behub'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Testimonial title',
            ]
        );

        $repeater->add_control(
            'testimonial_rating',
            [
                'label'   => esc_html__('Rating', 'behub'),
                'default' => 5,
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    0 => esc_html__('Hidden', 'behub'),
                    1 => esc_html__('Very poor', 'behub'),
                    2 => esc_html__('Not that bad', 'behub'),
                    3 => esc_html__('Average', 'behub'),
                    4 => esc_html__('Good', 'behub'),
                    5 => esc_html__('Perfect', 'behub'),
                ]
            ]
        );
        $repeater->add_control(
            'testimonial_content',
            [
                'label'       => esc_html__('Content', 'behub'),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
                'label_block' => true,
                'rows'        => '10',
            ]
        );

        $repeater->add_control(
            'testimonial_name',
            [
                'label'   => esc_html__('Name', 'behub'),
                'default' => 'John Doe',
                'type'    => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'testimonial_job',
            [
                'label'   => esc_html__('Job', 'behub'),
                'default' => 'Design',
                'type'    => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'testimonial_image',
            [
                'label'      => esc_html__('Choose Image', 'behub'),
                'type'       => Controls_Manager::MEDIA,
                'show_label' => false,
            ]
        );

        $repeater->add_control(
            'testimonial_link',
            [
                'label'       => esc_html__('Link to', 'behub'),
                'placeholder' => esc_html__('https://your-link.com', 'behub'),
                'type'        => Controls_Manager::URL,
            ]
        );

        $this->add_control(
            'testimonials',
            [
                'label'       => esc_html__('Items', 'behub'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ testimonial_name }}}',
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name'      => 'testimonial_image',
                'default'   => 'full',
                'separator' => 'none',
            ]
        );

        $this->add_responsive_control(
            'column',
            [
                'label'   => esc_html__('Columns', 'behub'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 1,
                'options' => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 6 => 6],
            ]
        );

        $this->add_control(
            'testimonial_layout',
            [
                'label'   => esc_html__('Layout', 'behub'),
                'default' => '1',
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    '1' => esc_html__('Layout 1', 'behub'),
                    '2' => esc_html__('Layout 2', 'behub'),
                    '3' => esc_html__('Layout 3', 'behub'),
                    '4' => esc_html__('Layout 4', 'behub'),
                ]
            ]
        );

        $this->add_responsive_control(
            'testimonial_alignment',
            [
                'label'       => esc_html__('Alignment', 'behub'),
                'type'        => Controls_Manager::CHOOSE,
                'options'     => [
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
                'label_block' => false,
                'selectors'   => [
                    '{{WRAPPER}} .elementor-testimonial-item-wrapper .inner' => 'text-align: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'view',
            [
                'label'   => esc_html__('View', 'behub'),
                'type'    => Controls_Manager::HIDDEN,
                'default' => 'traditional',
            ]
        );
        $this->end_controls_section();


        // WRAPPER STYLE
        $this->start_controls_section(
            'section_style_testimonial_wrapper',
            [
                'label' => esc_html__('Wrapper', 'behub'),
                'tab'   => Controls_Manager::TAB_STYLE,

            ]
        );
        $this->add_control(
            'show_bg',
            [
                'label'        => esc_html__('Show Bg', 'behub'),
                'type'         => Controls_Manager::SWITCHER,
                'prefix_class' => 'testimonial-bg-',
            ]
        );
        $this->add_responsive_control(
            'testimonial_width',
            [
                'label'          => esc_html__('Width', 'behub'),
                'type'           => Controls_Manager::SLIDER,
                'default'        => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units'     => ['%', 'px', 'vw'],
                'range'          => [
                    '%'  => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors'      => [
                    '{{WRAPPER}} .elementor-testimonial-item-wrapper .inner' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding_estimonial_wrapper',
            [
                'label'      => esc_html__('Padding', 'behub'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'margin_testimonial_wrapper',
            [
                'label'      => esc_html__('Margin', 'behub'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'color_testimonial_wrapper',
            [
                'label'     => esc_html__('Background Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .inner' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'wrapper_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .inner',
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'wrapper_radius',
            [
                'label'      => esc_html__('Border Radius', 'behub'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'wrapper_box_shadow',
                'selector' => '{{WRAPPER}} .inner',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_testimonial_img',
            [
                'label' => esc_html__('Image', 'behub'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'testimonial_radius_img',
            [
                'label'      => esc_html__('Border Radius', 'behub'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-testimonial-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding_testimonial_img',
            [
                'label'      => esc_html__('Padding', 'behub'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-testimonial-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'margin_testimonial_img',
            [
                'label'      => esc_html__('Margin', 'behub'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-testimonial-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_testimonial_decor',
            [
                'label' => esc_html__('Decor', 'behub'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'testimonial_layout' => '4'
                ],
            ]
        );

        $this->add_responsive_control(
            'decor_right_spacing',
            [
                'label'     => esc_html__('Spacing Right', 'behub'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .inner:before' => 'right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'decor_top_spacing',
            [
                'label'     => esc_html__('Spacing Top', 'behub'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .inner:before' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'decor_size',
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
                    '{{WRAPPER}} .inner:before' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Title
        $this->start_controls_section(
            'section_style_testimonial_title',
            [
                'label' => esc_html__('Title', 'behub'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_title_color',
            [
                'label'     => esc_html__('Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_title_color_hover',
            [
                'label'     => esc_html__('Color Hover', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-box:hover .title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .title',
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label'     => esc_html__('Spacing', 'behub'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-testimonial-item .title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        // Content style
        $this->start_controls_section(
            'section_style_testimonial_style',
            [
                'label' => esc_html__('Content', 'behub'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'content_content_color',
            [
                'label'     => esc_html__('Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_content_color_hover',
            [
                'label'     => esc_html__('Color Hover', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .inner:hover .content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typography',
                'selector' => '{{WRAPPER}} .content',
            ]
        );

        $this->add_responsive_control(
            'content_spacing',
            [
                'label'      => esc_html__('Margin', 'behub'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        // Name.
        $this->start_controls_section(
            'section_style_testimonial_name',
            [
                'label' => esc_html__('Name', 'behub'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'name_text_color',
            [
                'label'     => esc_html__('Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .name, {{WRAPPER}} .name a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'name_text_color_hover',
            [
                'label'     => esc_html__('Color Hover', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .inner .name:hover, {{WRAPPER}} .inner .name a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'name_typography',
                'selector' => '{{WRAPPER}} .name',
            ]
        );

        $this->add_responsive_control(
            'name_padding',
            [
                'size_units' => ['px', 'em', '%'],
                'label'      => esc_html__('Spacing', 'behub'),
                'type'       => Controls_Manager::SLIDER,
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Job.
        $this->start_controls_section(
            'section_style_testimonial_job',
            [
                'label' => esc_html__('Job', 'behub'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'job_text_color',
            [
                'label'     => esc_html__('Color', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .job' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'job_text_color_hover',
            [
                'label'     => esc_html__('Color Hover', 'behub'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .inner:hover .job' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'job_typography',
                'selector' => '{{WRAPPER}} .job',
            ]
        );

        $this->end_controls_section();

        //customs carousel
        $this->add_control_carousel_customs();

    }

    /**
     * Render testimonial widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        if (!empty($settings['testimonials']) && is_array($settings['testimonials'])) {
            $this->add_render_attribute('wrapper', 'class', 'elementor-testimonial-item-wrapper');
            // Row

            $this->add_render_attribute('row', 'class', 'layout-' . esc_attr($settings['testimonial_layout']));
            $this->add_render_attribute('row', 'class', 'row');

            //item
            if ($settings['enable_carousel'] === 'yes') {
                $this->add_render_attribute('row', 'class', 'behub-carousel');
                $this->add_render_attribute('row', 'class', 'layout-carousel-' . esc_attr($settings['style_carousel']));
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
            $this->add_render_attribute('item', 'class', 'column-item elementor-testimonial-item');
            $this->add_render_attribute('details', 'class', 'details');
            ?>
            <div <?php $this->print_render_attribute_string('wrapper'); ?>>
                <div <?php $this->print_render_attribute_string('row'); ?>>
                    <?php foreach ($settings['testimonials'] as $testimonial): ?>
                        <div <?php $this->print_render_attribute_string('item'); ?>>
                            <?php if ($settings['testimonial_layout'] == '1'): ?>
                                <div class="inner">
                                    <?php if (!empty($testimonial['testimonial_title'])) : ?>
                                        <h3 class="title"><?php echo esc_html($testimonial["testimonial_title"]) ?></h3>
                                    <?php endif; ?>
                                    <?php if ($testimonial['testimonial_rating'] && $testimonial['testimonial_rating'] > 0) {
                                        echo '<div class="elementor-testimonial-rating">';
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $testimonial['testimonial_rating']) {
                                                echo '<i class="fas fa-star active" aria-hidden="true"></i>';
                                            }
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                    <?php if (!empty($testimonial['testimonial_content'])) : ?>
                                        <div class="content"><?php echo sprintf('%s', $testimonial['testimonial_content']); ?></div>
                                    <?php endif; ?>
                                    <?php $this->render_image($settings, $testimonial); ?>
                                    <div class="testimonial-caption">

                                        <div <?php $this->print_render_attribute_string('details');?>>
                                            <?php
                                            $testimonial_name_html = $testimonial['testimonial_name'];
                                            if (!empty($testimonial['testimonial_link']['url'])) :
                                                $testimonial_name_html = '<a href="' . esc_url($testimonial['testimonial_link']['url']) . '">' . esc_html($testimonial_name_html) . '</a>';
                                            endif;
                                            printf('<div class="name">%s</div>', $testimonial_name_html);
                                            ?>
                                            <?php if ($testimonial['testimonial_job']): ?>
                                                <div class="job"><?php echo esc_html($testimonial['testimonial_job']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($settings['testimonial_layout'] == '2'): ?>

                                <?php if (!empty($testimonial['testimonial_title'])) : ?>
                                    <h3 class="title"><?php echo esc_html($testimonial["testimonial_title"]) ?></h3>
                                <?php endif; ?>
                                <?php $this->render_image($settings, $testimonial); ?>
                                <div class="inner">
                                    <?php if (!empty($testimonial['testimonial_content'])) : ?>
                                        <div class="content"><?php echo sprintf('%s', $testimonial['testimonial_content']); ?></div>
                                    <?php endif; ?>
                                    <?php if ($testimonial['testimonial_rating'] && $testimonial['testimonial_rating'] > 0) {
                                        echo '<div class="elementor-testimonial-rating">';
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $testimonial['testimonial_rating']) {
                                                echo '<i class="fas fa-star active" aria-hidden="true"></i>';
                                            }
                                        }
                                        echo '</div>';
                                    } ?>
                                    <div class="testimonial-caption">

                                        <div <?php $this->print_render_attribute_string('details');?>>
                                            <?php
                                            $testimonial_name_html = $testimonial['testimonial_name'];
                                            if (!empty($testimonial['testimonial_link']['url'])) :
                                                $testimonial_name_html = '<a href="' . esc_url($testimonial['testimonial_link']['url']) . '">' . esc_html($testimonial_name_html) . '</a>';
                                            endif;
                                            printf('<div class="name">%s</div>', $testimonial_name_html);
                                            ?>
                                            <?php if ($testimonial['testimonial_job']): ?>
                                                <div class="job"><?php echo esc_html($testimonial['testimonial_job']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($settings['testimonial_layout'] == '3'): ?>
                                <div class="inner">
                                    <?php $this->render_image($settings, $testimonial); ?>

                                    <?php if ($testimonial['testimonial_rating'] && $testimonial['testimonial_rating'] > 0) {
                                        echo '<div class="elementor-testimonial-rating">';
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $testimonial['testimonial_rating']) {
                                                echo '<i class="fas fa-star active" aria-hidden="true"></i>';
                                            }
                                        }
                                        echo '</div>';
                                    } ?>
                                    <?php if (!empty($testimonial['testimonial_title'])) : ?>
                                        <h3 class="title"><?php echo esc_html($testimonial["testimonial_title"]) ?></h3>
                                    <?php endif; ?>
                                    <?php if (!empty($testimonial['testimonial_content'])) : ?>
                                        <div class="content"><?php echo sprintf('%s', $testimonial['testimonial_content']); ?></div>
                                    <?php endif; ?>
                                    <div class="testimonial-caption">
                                        <div <?php echo behub_elementor_get_render_attribute_string('details', $this); // WPCS: XSS ok. ?>>
                                            <?php
                                            $testimonial_name_html = $testimonial['testimonial_name'];
                                            if (!empty($testimonial['testimonial_link']['url'])) :
                                                $testimonial_name_html = '<a href="' . esc_url($testimonial['testimonial_link']['url']) . '">' . esc_html($testimonial_name_html) . '</a>';
                                            endif;
                                            printf('<div class="name">%s</div>', $testimonial_name_html);
                                            ?>
                                            <?php if ($testimonial['testimonial_job']): ?>
                                                <div class="job"><?php echo esc_html($testimonial['testimonial_job']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($settings['testimonial_layout'] == '4'): ?>

                                <div class="inner">
                                    <?php $this->render_image($settings, $testimonial); ?>
                                    <?php if (!empty($testimonial['testimonial_title'])) : ?>
                                        <h3 class="title"><?php echo esc_html($testimonial["testimonial_title"]) ?></h3>
                                    <?php endif; ?>
                                    <?php if ($testimonial['testimonial_rating'] && $testimonial['testimonial_rating'] > 0) {
                                        echo '<div class="elementor-testimonial-rating">';
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $testimonial['testimonial_rating']) {
                                                echo '<i class="fas fa-star active" aria-hidden="true"></i>';
                                            }
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                    <?php if (!empty($testimonial['testimonial_content'])) : ?>
                                        <div class="content"><?php echo sprintf('%s', $testimonial['testimonial_content']); ?></div>
                                    <?php endif; ?>

                                    <div class="testimonial-caption">

                                        <div <?php $this->print_render_attribute_string('details');?>>
                                            <?php
                                            $testimonial_name_html = $testimonial['testimonial_name'];
                                            if (!empty($testimonial['testimonial_link']['url'])) :
                                                $testimonial_name_html = '<a href="' . esc_url($testimonial['testimonial_link']['url']) . '">' . esc_html($testimonial_name_html) . '</a>';
                                            endif;
                                            printf('<div class="name">%s</div>', $testimonial_name_html);
                                            ?>
                                            <?php if ($testimonial['testimonial_job']): ?>
                                                <div class="job"><?php echo esc_html($testimonial['testimonial_job']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>


                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
        }
    }

    private function render_image($settings, $testimonial) {
        if (!empty($testimonial['testimonial_image']['url'])) :
            ?>
            <div class="elementor-testimonial-image">
                <?php
                $testimonial['testimonial_image_size']             = $settings['testimonial_image_size'];
                $testimonial['testimonial_image_custom_dimension'] = $settings['testimonial_image_custom_dimension'];
                echo Group_Control_Image_Size::get_attachment_image_html($testimonial, 'testimonial_image');
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

        $this->add_responsive_control(
            'columns',
            [
                'label'   => esc_html__('Columns', 'behub'),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'condition' => [
                    'enable_carousel' => 'yes',
                    'style_carousel' => '3',
                ]
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
                    '3' => esc_html__('Layout 3', 'behub'),
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
        $style = $settings['style_carousel'];
        if($style == '3'){
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

$widgets_manager->register(new BeHub_Elementor_Testimonials());

