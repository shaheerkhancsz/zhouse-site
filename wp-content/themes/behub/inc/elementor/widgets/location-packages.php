<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Behub_Elementor_Location_Packages extends Elementor\Widget_Base
{

    public function get_name()
    {
        return 'behub-location-packages';
    }

    public function get_title()
    {
        return esc_html__('Location Packages', 'behub');
    }

    public function get_categories()
    {
        return array('behub-addons');
    }

    public function get_script_depends()
    {
        return ['behub-elementor-location-packages'];
    }

    protected function register_controls()
    {

        //title
        $this->start_controls_section(
            'section_location_packages',
            [
                'label' => esc_html__('Packages', 'behub'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'default' => 'full',
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'behub'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'price',
            [
                'label' => esc_html__('Price', 'behub'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => esc_html__('Description', 'behub'),
                'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
                'rows' => '10',
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => esc_html__('Choose Image', 'behub'),
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'type' => Controls_Manager::MEDIA,
                'show_label' => false,
            ]
        );

        $this->add_control(
            'show_detail',
            [
                'label' => esc_html__('Show Detail', 'behub'),
                'default' => 'yes',
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'features',
            [
                'label' => esc_html__('Features', 'behub'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'detail',
            [
                'label' => esc_html__('Detail', 'behub'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'condition' => [
                    'show_detail' => 'yes'
                ],
                'title_field' => '{{{ features }}}',

            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'elementor-location-packages-wrapper');
        ?>
        <div <?php echo behub_elementor_get_render_attribute_string('wrapper', $this); ?>>
            <?php $this->render_style($settings) ?>
        </div>
        <?php
    }

    protected function render_style($settings)
    {
        ?>
        <div class="inner">
            <?php if (!empty($settings['image']['url'])) : ?>
                <div class="team-image">
                    <?php echo Group_Control_Image_Size::get_attachment_image_html($settings, 'thumbnail', 'image'); ?>
                </div>
            <?php endif; ?>
            <div class="content">
                <?php if (!empty($settings['title']) || !empty($settings['price'])): ?>
                    <div class="content-top">
                        <?php if (!empty($settings['title'])): ?>
                            <h5 class="title"><?php printf('%s', $settings['title']); ?></h5>
                        <?php endif; ?>
                        <?php if (!empty($settings['price'])): ?>
                            <div class="price">
                                <label><?php echo esc_html__('from', 'behub'); ?></label>
                                <span><?php printf('%s', $settings['price']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($settings['description'])): ?>
                    <div class="description"><?php printf('%s', $settings['description']); ?></div>
                <?php endif; ?>
                <?php if ($settings['show_detail'] == 'yes'): ?>
                    <a href="#" class="button-detail">
                        <span><?php echo esc_html__('More detail', 'behub'); ?></span>
                        <i class="behub-icon-arrow-down"></i>
                        <i class="behub-icon-arrow-up"></i>
                    </a>
                    <ul class="detail">
                        <li class="text-features"><?php echo esc_html__('Features', 'behub'); ?></li>
                        <?php
                        if (!empty($settings['detail']) && is_array($settings['detail'])) {
                            foreach ($settings['detail'] as $detail): ?>
                                <li class="features-item">
                                    <span><?php printf('%s', $detail['features']); ?></span>
                                </li>
                            <?php endforeach;
                        } ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}

$widgets_manager->register(new Behub_Elementor_Location_Packages());
