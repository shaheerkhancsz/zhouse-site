<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Behub_Elementor_Location_Type extends Elementor\Widget_Base
{

    public function get_name()
    {
        return 'behub-location-type';
    }

    public function get_title()
    {
        return esc_html__('Location Type', 'behub');
    }

    public function get_categories()
    {
        return array('behub-addons');
    }

    protected function register_controls()
    {

        //title
        $this->start_controls_section(
            'section_type_location_type',
            [
                'label' => esc_html__('Type', 'behub'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'location_type',
            [
                'label' => esc_html__('Type', 'behub'),
                'type' => Controls_Manager::SELECT,
                'default' => 'location_address',
                'options' => [
                    'location_address' => esc_html__('Address', 'behub'),
                    'location_available_time' => esc_html__('Available Time', 'behub'),
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $id = get_the_ID();
        if (get_post_type() !== 'behub_location') {
            return;
        }
        $type = $settings['location_type'];
        ?>
        <div class="location-detail-wrapper">
            <div class="location-type">
                <?php echo get_post_meta($id, $type, true); ?>
            </div>
        </div>
        <?php
    }
}

$widgets_manager->register(new Behub_Elementor_Location_Type());
