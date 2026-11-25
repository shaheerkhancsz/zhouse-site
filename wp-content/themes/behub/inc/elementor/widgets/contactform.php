<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!behub_is_contactform_activated()) {
    return;
}

use Elementor\Controls_Manager;


class BeHub_Elementor_ContactForm extends Elementor\Widget_Base {

    public function get_name() {
        return 'behub-contactform';
    }

    public function get_title() {
        return esc_html__('BeHub Contact Form', 'behub');
    }

    public function get_categories() {
        return array('behub-addons');
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'contactform7',
            [
                'label' => esc_html__('General', 'behub'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $cf7               = get_posts('post_type="wpcf7_contact_form"&numberposts=-1');
        $contact_forms[''] = esc_html__('Please select form', 'behub');
        if ($cf7) {
            foreach ($cf7 as $cform) {
                $contact_forms[$cform->ID] = $cform->post_title;
            }
        } else {
            $contact_forms[0] = esc_html__('No contact forms found', 'behub');
        }

        $this->add_control(
            'cf_id',
            [
                'label'   => esc_html__('Select contact form', 'behub'),
                'type'    => Controls_Manager::SELECT,
                'options' => $contact_forms,
                'default' => ''
            ]
        );

        $this->add_control(
            'form_name',
            [
                'label'   => esc_html__('Form name', 'behub'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Contact form', 'behub'),
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label'        => esc_html__('Alignment', 'behub'),
                'type'         => Controls_Manager::CHOOSE,
                'options'      => [
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
                'prefix_class' => 'contact-form-align-',
                'default'      => '',
            ]
        );

        $this->add_control(
            'form-style',
            [
                'label' => __('Style Border', 'behub'),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('Off', 'behub'),
                'label_on' => __('On', 'behub'),
                'prefix_class' => 'form-style-border-',
                'separator' => 'before',

            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (!$settings['cf_id']) {
            return;
        }
        $args['id']    = $settings['cf_id'];
        $args['title'] = $settings['form_name'];

        echo behub_do_shortcode('contact-form-7', $args);
    }
}

$widgets_manager->register(new BeHub_Elementor_ContactForm());
