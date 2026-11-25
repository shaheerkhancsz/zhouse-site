<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;

class BeHub_Elementor_Team_Box extends Behub_Base_Widgets_Carousel {

    /**
     * Get widget name.
     *
     * Retrieve teambox widget name.
     *
     * @return string Widget name.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_name() {
        return 'behub-team-box';
    }

    /**
     * Get widget title.
     *
     * Retrieve teambox widget title.
     *
     * @return string Widget title.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_title() {
        return esc_html__('Team Box', 'behub');
    }

    /**
     * Get widget icon.
     *
     * Retrieve teambox widget icon.
     *
     * @return string Widget icon.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_icon() {
        return 'eicon-person';
    }

    public function get_script_depends() {
        return ['behub-elementor-team-box', 'slick'];
    }

    public function get_categories() {
        return array('behub-addons');
    }

    /**
     * Register teambox widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function register_controls() {
        $this->start_controls_section(
            'section_team',
            [
                'label' => esc_html__('Team', 'behub'),
            ]
        );
        $repeater = new Repeater();


        $repeater->add_control(
            'teambox_image',
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
            'job',
            [
                'label'   => esc_html__('Job', 'behub'),
                'default' => 'Designer',
                'type'    => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'name',
            [
                'label'   => esc_html__('Name', 'behub'),
                'default' => 'John Doe',
                'type'    => Controls_Manager::TEXT,
            ]
        );
        $repeater->add_control(
            'link',
            [
                'label'       => esc_html__('Link to', 'behub'),
                'placeholder' => esc_html__('https://your-link.com', 'behub'),
                'type'        => Controls_Manager::URL,
            ]
        );


        $repeater->add_control(
            'facebook',
            [
                'label'       => esc_html__('Facebook', 'behub'),
                'placeholder' => esc_html__('https://www.facebook.com/opalwordpress', 'behub'),
                'default'     => 'https://www.facebook.com/opalwordpress',
                'type'        => Controls_Manager::TEXT,
            ]
        );
        $repeater->add_control(
            'twitter',
            [
                'label'       => esc_html__('Twitter', 'behub'),
                'placeholder' => esc_html__('https://twitter.com/opalwordpress', 'behub'),
                'default'     => 'https://twitter.com/opalwordpress',
                'type'        => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'instagram',
            [
                'label'       => esc_html__('Instagram', 'behub'),
                'placeholder' => esc_html__('https://www.instagram.com/user/WPOpalTheme', 'behub'),
                'default'     => 'https://www.instagram.com/user/WPOpalTheme',
                'type'        => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'pinterest',
            [
                'label'       => esc_html__('Pinterest', 'behub'),
                'placeholder' => esc_html__('https://plus.pinterest.com/u/0/+WPOpal', 'behub'),
                'default'     => 'https://plus.pinterest.com/u/0/+WPOpal',
                'type'        => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'teambox',
            [
                'label'       => esc_html__('Items', 'behub'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ name }}}',
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name'      => 'teambox_image',
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

        $this->add_responsive_control(
            'item_spacing',
            [
                'label' => esc_html__('Spacing', 'behub'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-teambox-item-wrapper .row' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
                    '{{WRAPPER}} .elementor-teambox-item-wrapper .column-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2); margin-bottom: calc({{SIZE}}{{UNIT}});',
                ],
            ]
        );


        $this->end_controls_section();

        $this->add_control_carousel();

    }

    /**
     * Render teambox widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        if (!empty($settings['teambox']) && is_array($settings['teambox'])) {
            $this->add_render_attribute('wrapper', 'class', 'elementor-teambox-item-wrapper');

            $this->get_data_elementor_columns();
            // Item
            $this->add_render_attribute('item', 'class', 'column-item elementor-teambox-item');
            $this->add_render_attribute('details', 'class', 'details');
            ?>

            <div <?php $this->print_render_attribute_string('wrapper'); ?>>
                <div <?php $this->print_render_attribute_string('row'); ?>>
                    <?php foreach ($settings['teambox'] as $index => $teambox):?>
                        <div <?php $this->print_render_attribute_string('item'); ?>>
                            <div class="team-top">
                                <?php
                                if(!empty($teambox['link']['url'])) {
                                    $this->add_link_attributes('link_'.$index,$teambox['link']);
                                    echo '<a '.$this->get_render_attribute_string('link_'.$index).'>';
                                }
                                ?>
                                <?php $this->render_image($settings, $teambox); ?>
                                <?php
                                if(!empty($teambox['link']['url'])) {
                                    echo '</a>';
                                }
                                ?>
                            </div>
                            <div class="team-caption">
                                <div class="team-name"><?php echo esc_html($teambox['name']); ?></div>
                                <div class="team-job"><?php echo esc_html($teambox['job']); ?></div>
                                <div class="team-icon-socials">
                                    <ul>
                                        <?php foreach ($this->get_socials() as $key => $social): ?>
                                            <?php if (!empty($teambox[$key])) : ?>
                                                <li class="social">
                                                    <a href="<?php echo esc_url($teambox[$key]) ?>">
                                                        <i class="behub-icon-<?php echo esc_attr($social); ?>"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php }
    }

    private function render_image($settings, $teambox) {
        if (!empty($teambox['teambox_image']['url'])) :
            ?>
            <div class="team-image">
                <?php
                $teambox['teambox_image_size']             = $settings['teambox_image_size'];
                $teambox['teambox_image_custom_dimension'] = $settings['teambox_image_custom_dimension'];
                echo Group_Control_Image_Size::get_attachment_image_html($teambox, 'teambox_image');
                ?>
            </div>
        <?php
        endif;
    }

    private function get_socials() {
        return array(
            'facebook'  => 'facebook',
            'twitter'   => 'twitter',
            'instagram' => 'instagram',
            'pinterest' => 'pinterest-p',
        );
    }

}

$widgets_manager->register_widget_type(new BeHub_Elementor_Team_Box());
