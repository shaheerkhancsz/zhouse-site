<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BeHub_Virtualtour
 */
class BeHub_Virtualtour {
    public $post_type = 'behub_virtualtour';
    protected $options_page = '';
    static $instance;

    public static function getInstance() {
        if (!isset(self::$instance) && !(self::$instance instanceof BeHub_Virtualtour)) {
            self::$instance = new BeHub_Virtualtour();
        }

        return self::$instance;
    }

    public function __construct() {

        add_action('init', [$this, 'create_post_type']);
        if (behub_is_cmb2_activated()) {
            add_action('init', [$this, 'setup_metabox']);
            add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
            add_action( 'cmb2_admin_init', array( &$this, 'add_options_page_metabox' ) );
        }
    }

    public function setup_metabox() {
        add_action('cmb2_admin_init', [$this, 'metabox_virtualtour']);
    }

    public function metabox_virtualtour(){
        $cmb2 = new_cmb2_box(array(
            'id'           => 'behub_virtualtour_setting',
            'title'        => esc_html__('Virtual', 'behub'),
            'object_types' => array('behub_virtualtour'),
        ));

        $cmb2->add_field(array(
            'name' => esc_html__('Virtual Image', 'behub'),
            'id'   => 'virtualtour_image',
            'type' => 'file',
            'preview_size' => 'thumbnail',
        ));
    }


    /**
     * @return void
     */
    public function create_post_type() {

        $labels = array(
            'name'               => esc_html__('Virtual tours', 'behub'),
            'singular_name'      => esc_html__('Virtual tour', 'behub'),
            'add_new'            => esc_html__('Add New Virtual tour', 'behub'),
            'add_new_item'       => esc_html__('Add New Virtual tour', 'behub'),
            'edit_item'          => esc_html__('Edit Virtual tour', 'behub'),
            'new_item'           => esc_html__('New Virtual tour', 'behub'),
            'view_item'          => esc_html__('View Virtual tour', 'behub'),
            'search_items'       => esc_html__('Search Virtual tours', 'behub'),
            'not_found'          => esc_html__('No Virtual tours found', 'behub'),
            'not_found_in_trash' => esc_html__('No Virtual tours found in Trash', 'behub'),
            'parent_item_colon'  => esc_html__('Parent Virtual tour:', 'behub'),
            'menu_name'          => esc_html__('Virtual tours', 'behub'),
        );

        $labels     = apply_filters('behub_virtualtour_labels', $labels);
        $slug_field = apply_filters('behub_virtualtour_slug', 'virtualtour');

        register_post_type($this->post_type,
            array(
                'labels'        => $labels,
                'supports'      => array('title', 'thumbnail'),
                'public'        => true,
                'has_archive'   => true,
                'rewrite'       => array('slug' => $slug_field),
                'exclude_from_search' => true,
                'menu_position' => 5,
            )
        );
    }


    function add_options_page_metabox() {

        // hook in our save notices
        add_action( "cmb2_save_options-page_fields_behub_virtualtour_options", array( $this, 'settings_notices' ), 10, 2 );

        $custom = new_cmb2_box( array(
            'id'         => 'behub_virtualtour_options',
            'hookup'     => false,
            'cmb_styles' => false,
            'show_on'    => array(
                'key'   => 'options-page',
                'value' => array( 'behub_virtualtour_options' )
            ),
        ) );
        $custom->add_field( array(
            'name' => esc_html__('Archive Title','behub'),
            'id' => 'archive-title',
            'type' => 'text',
        ) );
        $custom->add_field( array(
            'name' => esc_html__('Archive description','behub'),
            'id' => 'cpt-content',
            'type' => 'wysiwyg',
        ) );

    }

    public function settings_notices( $object_id, $updated ) {
        if ( $object_id !== 'behub_virtualtour_options' || empty( $updated ) ) {
            return;
        }

        add_settings_error( 'behub_virtualtour_options-notices', '', esc_html__( 'Settings updated.', 'behub' ), 'updated' );
        settings_errors( 'behub_virtualtour_options-notices' );
    }

    public function admin_page_display() {
        ?>
        <div class="wrap cmb2-options-page <?php echo 'behub_virtualtour_options'; ?>">
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
            <?php cmb2_metabox_form( 'behub_virtualtour_options', 'behub_virtualtour_options' ); ?>
        </div>
        <?php
    }

    public function add_options_page() {
        $this->options_page = add_submenu_page( 'edit.php?post_type=' . $this->post_type, esc_html__('Virtual Tour Options','behub'), esc_html__('Virtual Tour Options','behub'), 'manage_options', 'behub_virtualtour_options', array( $this, 'admin_page_display' ) );
        add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
    }
}

BeHub_Virtualtour::getInstance();
