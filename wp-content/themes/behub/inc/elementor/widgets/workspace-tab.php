<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;


/**
 * Class BeHub_Elementor_Workspace
 */
class BeHub_Elementor_Workspace_Tab extends Elementor\Widget_Base
{

    public function get_name()
    {
        return 'behub-workspace-tab';
    }

    public function get_title()
    {
        return esc_html__('Workspace Tab', 'behub');
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
        return ['behub-elementor-workspace-tab', 'slick', 'isotope', 'imagesloaded'];
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

        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__('Posts Per Page', 'behub'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        $this->add_control(
            'categories',
            [
                'label' => __('Include Categories', 'behub'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_workspace_categories(),
                'multiple' => true,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_tab_button',
            [
                'label' => esc_html__('Button', 'behub'),
            ]
        );

        $this->add_control(
            'tab_button_text',
            [
                'label' => esc_html__('Button Text', 'behub'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'tab_button_link',
            [
                'label' => esc_html__('Button Link', 'behub'),
                'placeholder' => esc_html__('https://your-link.com', 'behub'),
                'type' => Controls_Manager::URL,
            ]
        );

        $this->add_responsive_control(
            'tab_button_padding',
            [
                'label' => esc_html__('Padding', 'behub'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .tab-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'tab_button_margin',
            [
                'label' => esc_html__('Margin', 'behub'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .tab-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_section();
    }

    public static function get_query_args($settings)
    {
        $query_args = [
            'post_type' => 'behub_workspace',
            'ignore_sticky_posts' => 1,
            'post_status' => 'publish', // Hide drafts/private posts for admins
        ];

        $query_args['posts_per_page'] = $settings['posts_per_page'];

        if (!empty($settings['categories'])) {
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'behub_workspace_cat',
                    'field' => 'slug',
                    'terms' => $settings['categories'],
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

    public function query_posts()
    {
        $query_args = $this->get_query_args($this->get_settings());
        return new WP_Query($query_args);
    }

    protected function get_workspace_categories()
    {
        $categories = get_terms(array(
                'taxonomy' => 'behub_workspace_cat',
                'hide_empty' => false,
            )
        );
        $results = array();
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $results[$category->slug] = $category->name;
            }
        }
        return $results;
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $query = $this->query_posts();

        if (!$query->found_posts) {
            return;
        }
        $this->add_render_attribute('wrapper', 'class', 'elementor-workspace-tab-wrapper');

        ?>
        <div <?php echo behub_elementor_get_render_attribute_string('wrapper', $this); ?>>
            <?php
            $count = 1;
            $counts = 1;
            echo '<div class="tab-title">';
            while ($query->have_posts()) {
                $query->the_post();
                include get_theme_file_path('template-parts/workspace/item-workspace-style-6.php');
                $counts++;
            }
            if ($settings['tab_button_text'] != '') {
                echo '<div class="workspace-button">';
                $button_html = '<span>' . $settings['tab_button_text'] . '</span>';
                $this->add_render_attribute('link', 'class', 'button-more-link');
                if (!empty($settings['tab_button_link']['url'])) {
                    $this->add_link_attributes('link', $settings['tab_button_link']);
                }

                echo '<a ' . $this->get_render_attribute_string('link') . '>' . $button_html . '</a>';
                echo '</div>';
            }
            echo '</div>';
            echo '<div class="tab-content">';
            while ($query->have_posts()) {
                $query->the_post();
                include get_theme_file_path('template-parts/workspace/item-workspace-style-5.php');
                $count++;
            }
            echo '</div>'
            ?>
        </div>
        <?php
        wp_reset_postdata();

    }

}

$widgets_manager->register(new BeHub_Elementor_Workspace_Tab());