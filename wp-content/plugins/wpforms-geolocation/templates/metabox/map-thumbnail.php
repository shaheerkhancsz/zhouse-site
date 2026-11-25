<?php
/**
 * Map thumbnail in entry metabox.
 *
 * @since 2.13.0
 *
 * @var string $map_url Embed map URL.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<iframe frameborder="0" title="<?php esc_html_e( 'Map Thumbnail', 'wpforms-geolocation' ); ?>" data-src="<?php echo esc_url( $map_url ); ?>" class="wpforms-entry-field-value-iframe"></iframe>
