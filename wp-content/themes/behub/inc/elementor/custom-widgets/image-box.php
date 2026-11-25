<?php

use Elementor\Controls_Manager;

add_action( 'elementor/element/image-box/section_image/before_section_end', function ($element, $args ) {
	$element->add_control(
		'image_style',
		[
			'label'   => esc_html__( 'Style', 'behub' ),
			'type'    => Controls_Manager::SELECT,
			'default'   => 'style-1',
			'options' => [
				'style-1'       => esc_html__( 'Style 1', 'behub' ),
				'style-2'       => esc_html__( 'Style 2', 'behub' ),
                'style-3'       => esc_html__( 'Style 3', 'behub' ),
                'style-4'       => esc_html__( 'Style 4', 'behub' ),
			],
			'prefix_class' => 'behub-image-box-',
		]
	);
}, 10, 2 );
