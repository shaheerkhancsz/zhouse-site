<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;


/**
 * Class BeHub_Elementor_VirtualTour
 */
class BeHub_Elementor_VirtualTour extends Elementor\Widget_Base {

    public function get_name() {
        return 'behub-virtualTour';
    }

    public function get_title() {
        return esc_html__('Virtual Tour', 'behub');
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
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return array('behub-addons');
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('VirtualTour', 'behub'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label'   => esc_html__('Posts Per Page', 'behub'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        $this->add_control(
            'virtualTour_style',
            [
                'label'   => esc_html__('Style', 'behub'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'content' => esc_html__('Style 1', 'behub'),
                    'virtualtour-style-2' => esc_html__('Style 2', 'behub'),
                ],
                'default' => 'content'
            ]
        );

        $this->add_responsive_control(
            'column',
            [
                'label'   => esc_html__('Columns', 'behub'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 3,
                'options' => [1 => 1, 2 => 2, 3 => 3, 4 => 4],
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
                    '{{WRAPPER}} .row'         => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
                    '{{WRAPPER}} .column-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2); margin-bottom: calc({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_control(
            'style_special',
            [
                'label'      => esc_html__('Style special', 'behub'),
                'type'       => \Elementor\Controls_Manager::SWITCHER,
                'prefix_class' => 'virtualtour-style-special-',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_pagination',
            [
                'label' => esc_html__('Pagination', 'behub'),
            ]

        );

        $this->add_control(
            'pagination_type',
            [
                'label'   => esc_html__('Pagination', 'behub'),
                'type'    => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    ''                      => esc_html__('None', 'behub'),
                    'numbers'               => esc_html__('Numbers', 'behub'),
                    'prev_next'             => esc_html__('Previous/Next', 'behub'),
                    'numbers_and_prev_next' => esc_html__('Numbers', 'behub') . ' + ' . esc_html__('Previous/Next', 'behub'),
                ],
            ]
        );

        $this->add_control(
            'pagination_page_limit',
            [
                'label'     => esc_html__('Page Limit', 'behub'),
                'default'   => '5',
                'condition' => [
                    'pagination_type!' => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_numbers_shorten',
            [
                'label'     => esc_html__('Shorten', 'behub'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => '',
                'condition' => [
                    'pagination_type' => [
                        'numbers',
                        'numbers_and_prev_next',
                    ],
                ],
            ]
        );

        $this->add_control(
            'pagination_prev_label',
            [
                'label'     => esc_html__('Previous Label', 'behub'),
                'default'   => esc_html__('&laquo; Previous', 'behub'),
                'condition' => [
                    'pagination_type' => [
                        'prev_next',
                        'numbers_and_prev_next',
                    ],
                ],
            ]
        );

        $this->add_control(
            'pagination_next_label',
            [
                'label'     => esc_html__('Next Label', 'behub'),
                'default'   => esc_html__('Next &raquo;', 'behub'),
                'condition' => [
                    'pagination_type' => [
                        'prev_next',
                        'numbers_and_prev_next',
                    ],
                ],
            ]
        );

        $this->add_control(
            'pagination_align',
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
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .elementor-pagination' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'pagination_type!' => '',
                ],
            ]
        );

        $this->end_controls_section();

    }

    public static function get_query_args($settings) {
        $query_args = [
            'post_type'           => 'behub_virtualTour',
            'ignore_sticky_posts' => 1,
            'post_status'         => 'publish', // Hide drafts/private posts for admins
        ];

        $query_args['posts_per_page'] = $settings['posts_per_page'];


        if (is_front_page()) {
            $query_args['paged'] = (get_query_var('page')) ? get_query_var('page') : 1;
        } else {
            $query_args['paged'] = (get_query_var('paged')) ? get_query_var('paged') : 1;
        }

        return $query_args;
    }

    public function get_current_page() {
        if ('' === $this->get_settings('pagination_type')) {
            return 1;
        }

        return max(1, get_query_var('paged'), get_query_var('page'));
    }

    public function get_posts_nav_link($page_limit = null) {
        if (!$page_limit) {
            $page_limit = $this->query_posts()->max_num_pages;
        }

        $return = [];

        $paged = $this->get_current_page();

        $link_template     = '<a class="page-numbers %s" href="%s">%s</a>';
        $disabled_template = '<span class="page-numbers %s">%s</span>';

        if ($paged > 1) {
            $next_page = intval($paged) - 1;
            if ($next_page < 1) {
                $next_page = 1;
            }

            $return['prev'] = sprintf($link_template, 'prev', get_pagenum_link($next_page), $this->get_settings('pagination_prev_label'));
        } else {
            $return['prev'] = sprintf($disabled_template, 'prev', $this->get_settings('pagination_prev_label'));
        }

        $next_page = intval($paged) + 1;

        if ($next_page <= $page_limit) {
            $return['next'] = sprintf($link_template, 'next', get_pagenum_link($next_page), $this->get_settings('pagination_next_label'));
        } else {
            $return['next'] = sprintf($disabled_template, 'next', $this->get_settings('pagination_next_label'));
        }

        return $return;
    }

    protected function render_loop_footer() {

        $parent_settings = $this->get_settings();
        if ('' === $parent_settings['pagination_type']) {
            return;
        }

        $page_limit = $this->query_posts()->max_num_pages;
        if ('' !== $parent_settings['pagination_page_limit']) {
            $page_limit = min($parent_settings['pagination_page_limit'], $page_limit);
        }

        if (2 > $page_limit) {
            return;
        }

        $this->add_render_attribute('pagination', 'class', 'elementor-pagination');

        $has_numbers   = in_array($parent_settings['pagination_type'], ['numbers', 'numbers_and_prev_next']);
        $has_prev_next = in_array($parent_settings['pagination_type'], ['prev_next', 'numbers_and_prev_next']);

        $links = [];

        if ($has_numbers) {
            $links = paginate_links([
                'type'               => 'array',
                'current'            => $this->get_current_page(),
                'total'              => $page_limit,
                'prev_next'          => false,
                'show_all'           => 'yes' !== $parent_settings['pagination_numbers_shorten'],
                'before_page_number' => '<span class="elementor-screen-only">' . esc_html__('Page', 'behub') . '</span>',
            ]);
        }

        if ($has_prev_next) {
            $prev_next = $this->get_posts_nav_link($page_limit);
            array_unshift($links, $prev_next['prev']);
            $links[] = $prev_next['next'];
        }

        ?>
        <div class="pagination">
            <nav class="elementor-pagination" role="navigation"
                 aria-label="<?php esc_attr_e('Pagination', 'behub'); ?>">
                <?php echo implode(PHP_EOL, $links); ?>
            </nav>
        </div>
        <?php
    }

    public function query_posts() {
        $query_args = $this->get_query_args($this->get_settings());
        return new WP_Query($query_args);
    }


    protected function render() {
        $settings = $this->get_settings_for_display();

        $query = $this->query_posts();

        if (!$query->found_posts) {
            return;
        }

        $this->add_render_attribute('wrapper', 'class', 'elementor-post-wrapper');

        $this->add_render_attribute('row', 'class', 'row');

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

        ?>
        <div <?php  $this->print_render_attribute_string('wrapper'); ?>>
            <div <?php $this->print_render_attribute_string('row'); ?>>
                <?php
                $style = $settings['virtualTour_style'];
                while ($query->have_posts()) {
                    $query->the_post();
                    get_template_part('/template-parts/virtualtour/' . $style);
                }
                ?>
            </div>

            <?php if ($settings['pagination_type'] && !empty($settings['pagination_type'])): ?>
                <div class="pagination">
                    <?php $this->render_loop_footer(); ?>
                </div>
            <?php endif; ?>
        </div>

        <?php
        wp_reset_postdata();

    }

}

$widgets_manager->register(new BeHub_Elementor_VirtualTour());