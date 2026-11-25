<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BeHub_Workspace
 */
class BeHub_Workspace {
    public $post_type = 'behub_workspace';
    public $taxonomy  = 'behub_workspace_cat';
    static $instance;

    public static function getInstance() {
        if (!isset(self::$instance) && !(self::$instance instanceof BeHub_Workspace)) {
            self::$instance = new BeHub_Workspace();
        }

        return self::$instance;
    }

    public function __construct() {
        add_action('init', [$this, 'create_post_type']);
        add_action('init', [$this, 'create_taxonomy']);
        if (behub_is_cmb2_activated()) {
            add_action('init', [$this, 'setup_metabox']);
        }
    }

    public function setup_metabox() {
        add_action('cmb2_admin_init', [$this, 'metabox_workspace']);
    }

    public function metabox_workspace(){
        $cmb2 = new_cmb2_box(array(
            'id'           => 'behub_workspace_setting',
            'title'        => esc_html__('Infomation', 'behub'),
            'object_types' => array('behub_workspace'),
        ));

        $cmb2->add_field( array(
            'name'    => 'Ideal for team size',
            'id'      => 'workspace_team',
            'type'    => 'text_small',
        ) );

        $cmb2->add_field( array(
            'name'    => 'Price',
            'id'      => 'workspace_price',
            'type'    => 'text_small',
        ) );

        $cmb2->add_field( array(
            'name'    => 'Minimum Term',
            'id'      => 'workspace_minimum_term',
            'type'    => 'text_small',
        ) );

        $cmb2->add_field( array(
            'name'    => 'Sale',
            'id'      => 'workspace_sale',
            'type'    => 'text_small',
        ) );

        $cmb2->add_field(array(
            'name' => esc_html__('Description', 'behub'),
            'id'   => 'workspace_description',
            'type' => 'textarea_small',
        ));

        $cmb2->add_field( array(
            'name' => 'Gallery',
            'desc' => '',
            'id'   => 'workspace_gallery',
            'type' => 'file_list',
        ) );

    }


    /**
     * @return void
     */
    public function create_post_type() {

        $labels = array(
            'name'               => esc_html__('Workspaces', 'behub'),
            'singular_name'      => esc_html__('Workspace', 'behub'),
            'add_new'            => esc_html__('Add New Workspace', 'behub'),
            'add_new_item'       => esc_html__('Add New Workspace', 'behub'),
            'edit_item'          => esc_html__('Edit Workspace', 'behub'),
            'new_item'           => esc_html__('New Workspace', 'behub'),
            'view_item'          => esc_html__('View Workspace', 'behub'),
            'search_items'       => esc_html__('Search Workspaces', 'behub'),
            'not_found'          => esc_html__('No Workspaces found', 'behub'),
            'not_found_in_trash' => esc_html__('No Workspaces found in Trash', 'behub'),
            'parent_item_colon'  => esc_html__('Parent Workspace:', 'behub'),
            'menu_name'          => esc_html__('Workspaces', 'behub'),
        );

        $labels     = apply_filters('behub_workspace_labels', $labels);
        $slug_field = apply_filters('behub_workspace_slug', 'workspace');

        register_post_type($this->post_type,
            array(
                'labels'        => $labels,
                'supports'      => array('title', 'editor', 'thumbnail'),
                'public'        => true,
                'has_archive'   => true,
                'rewrite'       => array('slug' => $slug_field),
                'menu_position' => 5,
                'categories'    => array(),
            )
        );
    }

    /**
     * @return void
     */
    public function create_taxonomy() {
        $labels = array(
            'name'              => esc_html__('Categories', 'behub'),
            'singular_name'     => esc_html__('Category', 'behub'),
            'search_items'      => esc_html__('Search Category', 'behub'),
            'all_items'         => esc_html__('All Categories', 'behub'),
            'parent_item'       => esc_html__('Parent Category', 'behub'),
            'parent_item_colon' => esc_html__('Parent Category:', 'behub'),
            'edit_item'         => esc_html__('Edit Category', 'behub'),
            'update_item'       => esc_html__('Update Category', 'behub'),
            'add_new_item'      => esc_html__('Add New Category', 'behub'),
            'new_item_name'     => esc_html__('New Category Name', 'behub'),
            'menu_name'         => esc_html__('Categories', 'behub'),
        );
        $labels = apply_filters('behub_workspace_cat_labels', $labels);

        $slug_cat_field = apply_filters('behub_cat_workspace_slug', 'workspace-cat');

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_nav_menus' => true,
            'rewrite'           => array('slug' => $slug_cat_field)
        );
        // Now register the taxonomy
        register_taxonomy($this->taxonomy, array($this->post_type), $args);
    }

    public function get_term_workspace($post_id) {
        $terms = get_the_terms($post_id, $this->taxonomy);
        $output = '';
        if (!is_wp_error($terms) && is_array($terms)) {
            foreach ($terms as $key => $term) {
                $term_link = get_term_link($term);
                if (is_wp_error($term_link)) {
                    continue;
                }
                $output .= '<a href="' . esc_url($term_link) . '">' . $term->name . '</a>';
                if ($key < count($terms) - 1) {
                    $output .= ', ';
                }
            }

        }
        return $output;
    }

}

BeHub_Workspace::getInstance();
