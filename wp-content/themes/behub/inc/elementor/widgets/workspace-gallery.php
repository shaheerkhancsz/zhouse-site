<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;


/**
 * Class BeHub_Elementor_Workspace
 */
class BeHub_Elementor_Workspace_Gallery extends Elementor\Widget_Base
{

    public function get_name()
    {
        return 'behub-workspace-gallery';
    }

    public function get_title()
    {
        return esc_html__('Workspace Gallery', 'behub');
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
    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_categories()
    {
        return array('behub-addons');
    }

    public function get_script_depends()
    {
        return ['behub-elementor-workspace-gallery', 'slick', 'isotope', 'imagesloaded'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Workspace', 'behub'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => esc_html__( 'Width', 'behub' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ '%', 'px', 'vw' ],
                'range' => [
                    '%' => [
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
                'selectors' => [
                    '{{WRAPPER}} .elementor-workspace-gallery-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label' => esc_html__( 'Height', 'behub' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'size_units' => [ 'px', 'vh' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 900,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .workspace-gallery-content-item img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }


    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'elementor-workspace-gallery-wrapper');
        $this->add_render_attribute( 'link', 'data-elementor-lightbox-slideshow', $this->get_id() );
        ?>
        <div <?php echo behub_elementor_get_render_attribute_string('wrapper', $this); ?>>
            <?php
                include get_theme_file_path('template-parts/workspace/single-gallery.php');
            ?>
        </div>
        <?php
    }

}

$widgets_manager->register(new BeHub_Elementor_Workspace_Gallery());