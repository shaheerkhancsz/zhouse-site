<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;


/**
 * Class BeHub_Elementor_Location_Map
 */
class BeHub_Elementor_Location_Map extends Elementor\Widget_Base {

    public function get_name() {
        return 'behub-location-map';
    }

    public function get_title() {
        return esc_html__('Location Map', 'behub');
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

    public function get_script_depends() {
        return ['behub-elementor-location-map', 'behub-google-maps-api'];
    }


    protected function register_controls() {
        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Location', 'behub'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'style',
            [
                'label'   => esc_html__('Map Style', 'behub'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'standard',
                'options' => [
                    'standard'            => esc_html__('Standard', 'behub'),
                    'light_grey_and_blue' => esc_html__('Light Grey and Blue', 'behub'),
                    'ultra_light'         => esc_html__('Ultra Light', 'behub'),
                    'shades_of_grey'      => esc_html__('Shades Of Grey', 'behub'),
                    'blue_water'          => esc_html__('Blue Water', 'behub'),
                    'farma_gray'          => esc_html__('Farma Gray', 'behub'),
                    'community'           => esc_html__('Community', 'behub'),
                    'chilled'             => esc_html__('Chilled', 'behub'),
                ],
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label'      => esc_html__('Map Height', 'behub'),
                'type'       => Controls_Manager::SLIDER,
                'default'    => [
                    'unit' => 'px',
                    'size' => 400,
                ],
                'size_units' => ['%', 'px', 'vh'],
                'range'      => [
                    'px' => [
                        'min' => 1,
                        'max' => 2500,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    '%'  => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .map-data-location' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'zoom',
            [
                'label'   => esc_html__('Map Zoom', 'behub'),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'range'   => [
                    'px' => [
                        'min' => 1,
                        'max' => 18,
                    ]
                ],
            ]
        );


        $this->end_controls_section();

    }

    protected function style_google_map() {
        $settings            = $this->get_settings_for_display();
        $key                 = $settings['style'];
        $behub_style_map_arr = [
            'standard' => '[]',

            'light_grey_and_blue' => '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#dde6e8"},{"visibility":"on"}]}]',

            'ultra_light' => '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]',

            'shades_of_grey' => '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]',

            'blue_water' => '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}]',

            'farma_gray' => '[{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"simplified"},{"gamma":"1.00"}]},{"featureType":"administrative.locality","elementType":"labels","stylers":[{"color":"#ba5858"}]},{"featureType":"administrative.neighborhood","elementType":"labels","stylers":[{"color":"#e57878"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"visibility":"simplified"},{"lightness":"65"},{"saturation":"-100"},{"hue":"#ff0000"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"visibility":"simplified"},{"saturation":"-100"},{"lightness":"80"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi.attraction","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#dddddd"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway.controlled_access","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#dddddd"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#eeeeee"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#ba5858"},{"saturation":"-100"}]},{"featureType":"transit.station","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit.station","elementType":"labels.text.fill","stylers":[{"color":"#ba5858"},{"visibility":"simplified"}]},{"featureType":"transit.station","elementType":"labels.icon","stylers":[{"hue":"#ff0036"}]},{"featureType":"water","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#dddddd"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#ba5858"}]}]',

            'community' => '[{"featureType":"administrative.country","elementType":"labels.text.fill","stylers":[{"color":"#2c52a2"}]},{"featureType":"administrative.province","elementType":"labels.text.fill","stylers":[{"color":"#2c52a2"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#2c52a2"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text.fill","stylers":[{"color":"#2c52a2"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"hue":"#ff0000"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.stroke","stylers":[{"color":"#2c52a2"}]},{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#e0efef"}]},{"featureType":"landscape.natural.landcover","elementType":"geometry.fill","stylers":[{"color":"#f5f5f5"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"hue":"#1900ff"},{"color":"#c0e8e8"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#c6ebbd"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":700}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#7dcdcd"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#addbf1"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#2c52a2"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]}]',

            'chilled' => '[{"featureType":"road","elementType":"geometry","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","stylers":[{"hue":149},{"saturation":-78},{"lightness":0}]},{"featureType":"road.highway","stylers":[{"hue":-31},{"saturation":-40},{"lightness":2.8}]},{"featureType":"poi","elementType":"label","stylers":[{"visibility":"off"}]},{"featureType":"landscape","stylers":[{"hue":163},{"saturation":-26},{"lightness":-1.1}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"water","stylers":[{"hue":3},{"saturation":-24.24},{"lightness":-38.57}]}]'
        ];
        return $behub_style_map_arr[$key];
    }


    protected function render() {
        $settings = $this->get_settings_for_display();
        if (is_singular('behub_location')) {

            $latitude  = get_post_meta(get_the_ID(), 'behub_location_latitude', true);
            $longitude = get_post_meta(get_the_ID(), 'behub_location_longitude', true);
            $style     = $this->style_google_map();
            $zoom      = $settings['zoom'];
            ?>
            <div class="google-map-address">
                <div class="map-data-location"
                     data-color="<?php echo esc_attr($style); ?>"
                     data-lat="<?php echo esc_attr($latitude); ?>"
                     data-lng="<?php echo esc_attr($longitude); ?>"
                     data-zoom="<?php echo esc_attr($zoom['size']); ?>">
                </div>
            </div>
            <?php
        }
    }


}

$widgets_manager->register(new BeHub_Elementor_Location_Map());