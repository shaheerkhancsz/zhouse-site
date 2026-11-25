<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;

/**
 * Elementor tabs widget.
 *
 * Elementor widget that displays vertical or horizontal tabs with different
 * pieces of content.
 *
 * @since 1.0.0
 */
class BeHub_Elementor_Amenities extends Behub_Base_Widgets_Carousel {

    public function get_categories() {
        return array('behub-addons');
    }

    /**
     * Get widget name.
     *
     * Retrieve tabs widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_name() {
        return 'behub-amenities';
    }

    /**
     * Get widget title.
     *
     * Retrieve tabs widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_title() {
        return esc_html__('Amenitiess', 'behub');
    }

    /**
     * Get widget icon.
     *
     * Retrieve tabs widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_icon() {
        return 'eicon-tabs';
    }

    /**
     * Enqueue scripts.
     *
     * Registers all the scripts defined as element dependencies and enqueues
     * them. Use `get_script_depends()` method to add custom script dependencies.
     *
     * @since 1.3.0
     * @access public
     */

    public function get_script_depends() {
        return ['behub-elementor-amenities', 'slick'];
    }

    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {

        $this->start_controls_section(
            'section_amenitiess',
            [
                'label' => esc_html__('Amenitiess', 'behub'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'amenities_title',
            [
                'label'       => esc_html__('Amenities name', 'behub'),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__('Amenities Name', 'behub'),
                'placeholder' => esc_html__('Amenities Name', 'behub'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'icon_font',
            [
                'label'       => esc_html__('Icon Font', 'behub'),
                'type'        => Controls_Manager::ICONS,
            ]
        );

        $repeater->add_control(
            'amenities_image',
            [
                'label'   => esc_html__('Choose Image', 'behub'),
                'type'    => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label'       => esc_html__('Link to', 'behub'),
                'type'        => Controls_Manager::URL,
                'dynamic'     => [
                    'active' => true,
                ],
                'placeholder' => esc_html__('https://your-link.com', 'behub'),
            ]
        );

        $this->add_control(
            'amenitiess',
            [
                'label'       => esc_html__('Items', 'behub'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ amenities_title }}}',
            ]
        );


        $this->add_control(
            'heading_settings',
            [
                'label'     => esc_html__('Settings', 'behub'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name'      => 'amenities_image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `amenities_image_size` and `amenities_image_custom_dimension`.
                'default'   => 'full',
                'separator' => 'none',
            ]
        );

        $this->add_responsive_control(
            'column',
            [
                'label'   => esc_html__('Columns', 'behub'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 3,
                'options' => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9],
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
                    '{{WRAPPER}} .elementor-amenities-wrapper .row'         => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
                    '{{WRAPPER}} .elementor-amenities-wrapper .column-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2); margin-bottom: calc({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->end_controls_section();

        $this->add_control_carousel();

    }

    /**
     * Render tabs widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        if (!empty($settings['amenitiess']) && is_array($settings['amenitiess'])) {

            $this->add_render_attribute('wrapper', 'class', 'elementor-amenities-wrapper');

            $this->get_data_elementor_columns();

            $this->add_render_attribute('item', 'class', 'elementor-amenities-item column-item');


            ?>
            <div class="elementor-amenitiess">
                <div <?php $this->print_render_attribute_string('wrapper'); ?>>

                    <div <?php $this->print_render_attribute_string('row'); ?>>
                        <?php foreach ($settings['amenitiess'] as $item) : ?>
                            <div <?php $this->print_render_attribute_string('item'); ?>>
                                <div class="inner">
                                    <?php
                                    $item['image_size']             = $settings['amenities_image_size'];
                                    $item['image_custom_dimension'] = $settings['amenities_image_custom_dimension']; ?>
                                    <div class="image">
                                        <?php if (!empty($item['amenities_image']['url'])) {
                                            echo Elementor\Group_Control_Image_Size::get_attachment_image_html($item, 'image', 'amenities_image');
                                        } ?>
                                    </div>
                                    <div class="content">
                                        <?php Icons_Manager::render_icon( $item['icon_font'], [ 'aria-hidden' => 'true' ] ); ?>
                                        <div class="title"><?php echo esc_html__($item['amenities_title'], 'behub'); ?></div>
                                        <span class="line"></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}

$widgets_manager->register(new BeHub_Elementor_Amenities());
