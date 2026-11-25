<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Behub_Elementor_Workspace_Type extends Elementor\Widget_Base
{

    public function get_name()
    {
        return 'behub-workspace-type';
    }

    public function get_title()
    {
        return esc_html__('Workspace Type', 'behub');
    }

    public function get_categories()
    {
        return array('behub-addons');
    }

    protected function register_controls()
    {

        //title
        $this->start_controls_section(
            'section_type_workspace_type',
            [
                'label' => esc_html__('Type', 'behub'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'workspace_type',
            [
                'label' => esc_html__('Type', 'behub'),
                'type' => Controls_Manager::SELECT,
                'default' => 'workspace_price',
                'options' => [
                    'workspace_price' => esc_html__('Price', 'behub'),
                    'workspace_minimum_term' => esc_html__('Minimum Term', 'behub'),
                    'workspace_team' => esc_html__('Ideal for team size', 'behub'),
                ],
            ]
        );

        $this->add_control(
            'workspace_type_color',
            [
                'label' => esc_html__('Color', 'behub'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .workspace-detail-wrapper .workspace-type' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $id = get_the_ID();
        if (get_post_type() !== 'behub_workspace') {
            return;
        }
        $type = $settings['workspace_type'];
        ?>
        <div class="workspace-detail-wrapper">
            <div class="workspace-type">
                <?php echo get_post_meta($id, $type, true); ?>
            </div>
        </div>
        <?php
    }
}

$widgets_manager->register(new Behub_Elementor_Workspace_Type());
