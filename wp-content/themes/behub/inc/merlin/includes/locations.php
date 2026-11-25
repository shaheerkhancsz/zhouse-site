<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BeHub_Location
 */
class BeHub_Location {
    public $post_type = 'behub_location';
    public $taxonomy  = 'behub_location_cat';
    protected $options_page = '';
    static $instance;

    public static function getInstance() {
        if (!isset(self::$instance) && !(self::$instance instanceof BeHub_Location)) {
            self::$instance = new BeHub_Location();
        }

        return self::$instance;
    }

    public function __construct() {
        add_action('init', [$this, 'create_post_type']);
        add_action('init', [$this, 'create_taxonomy']);
        if (behub_is_cmb2_activated()) {
            add_action('init', [$this, 'setup_metabox']);
            add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
            add_action( 'cmb2_admin_init', array( &$this, 'add_options_page_metabox' ) );
        }
    }

    public function setup_metabox() {
        add_action('cmb2_admin_init', [$this, 'metabox_location']);
    }

    public function metabox_location(){
        $cmb2 = new_cmb2_box(array(
            'id'           => 'behub_location_setting',
            'title'        => esc_html__('Infomation', 'behub'),
            'object_types' => array('behub_location'),
        ));

        $cmb2->add_field(array(
            'name' => esc_html__('Address', 'behub'),
            'id'   => 'location_address',
            'type' => 'text',
        ));

        $cmb2->add_field(array(
            'name' => esc_html__('Available Time', 'behub'),
            'id'   => 'location_available_time',
            'type' => 'text',
        ));

        $cmb2->add_field( array(
            'name' => 'Gallery',
            'desc' => '',
            'id'   => 'location_gallery',
            'type' => 'file_list',
        ) );

        $cmb2->add_field( array(
            'name' => 'Location',
            'desc' => esc_html__('Drag the marker to set the exact location','behub'),
            'id' => 'behub_location',
            'type' => 'pw_map',
            'split_values' => true,
        ) );

    }


    /**
     * @return void
     */
    public function create_post_type() {

        $labels = array(
            'name'               => esc_html__('Locations', 'behub'),
            'singular_name'      => esc_html__('Location', 'behub'),
            'add_new'            => esc_html__('Add New Location', 'behub'),
            'add_new_item'       => esc_html__('Add New Location', 'behub'),
            'edit_item'          => esc_html__('Edit Location', 'behub'),
            'new_item'           => esc_html__('New Location', 'behub'),
            'view_item'          => esc_html__('View Location', 'behub'),
            'search_items'       => esc_html__('Search Locations', 'behub'),
            'not_found'          => esc_html__('No Locations found', 'behub'),
            'not_found_in_trash' => esc_html__('No Locations found in Trash', 'behub'),
            'parent_item_colon'  => esc_html__('Parent Location:', 'behub'),
            'menu_name'          => esc_html__('Locations', 'behub'),
        );

        $labels     = apply_filters('behub_location_labels', $labels);
        $slug_field = apply_filters('behub_location_slug', 'locations');

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
        $labels = apply_filters('behub_location_cat_labels', $labels);

        $slug_cat_field = apply_filters('behub_cat_location_slug', 'location-cat');

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

    public function get_term_location($post_id) {
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

    function add_options_page_metabox() {

        // hook in our save notices
        add_action( "cmb2_save_options-page_fields_behub_location_options", array( $this, 'settings_notices' ), 10, 2 );

        $cmb2 = new_cmb2_box( array(
            'id'         => 'behub_location_options',
            'hookup'     => false,
            'cmb_styles' => false,
            'show_on'    => array(
                'key'   => 'options-page',
                'value' => array( 'behub_location_options' )
            ),
        ) );


        $cmb2->add_field( array(
            'name' => esc_html__('Google Map','behub'),

            'type' => 'title',
            'id'   => 'behub_location_title'
        ) );

        $cmb2->add_field( array(
            'name' => esc_html__('Google API key (see ','behub').'<a href="https://developers.google.com/maps/documentation/embed/get-api-key" target="blank">Google Maps API docs</a>'.esc_html__(' for details).','behub'),
            'id' => 'google_api',
            'type' => 'text',
        ) );

    }

    public function settings_notices( $object_id, $updated ) {
        if ( $object_id !== 'behub_location_options' || empty( $updated ) ) {
            return;
        }

        add_settings_error( 'behub_location_options-notices', '', esc_html__( 'Settings updated.', 'behub' ), 'updated' );
        settings_errors( 'behub_location_options-notices' );
    }

    public function admin_page_display() {
        ?>
        <div class="wrap cmb2-options-page <?php echo 'behub_location_options'; ?>">
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
            <?php cmb2_metabox_form( 'behub_location_options', 'behub_location_options' ); ?>
        </div>
        <?php
    }

    public function add_options_page() {
        $this->options_page = add_submenu_page( 'edit.php?post_type=' . $this->post_type, esc_html__('Location Options','behub'), esc_html__('Location Options','behub'), 'manage_options', 'behub_location_options', array( $this, 'admin_page_display' ) );
        add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
    }

}

BeHub_Location::getInstance();
