<?php

namespace WPFormsEntryAutomation\Helpers;

use WPForms\Helpers\File;
use WPFormsEntryAutomation\Vendor\Cezpdf;

/**
 * VendorPDF class extends the Cezpdf class to provide additional functionality, such as Unicode support.
 * This class is designed to create PDF documents with customized settings.
 *
 * @since 1.0.0
 */
final class VendorPDF extends Cezpdf {

	/**
	 * Initializes a new instance of the class with the provided parameters.
	 *
	 * @since 1.0.0
	 *
	 * @param string $paper       The paper size (default is 'a4').
	 * @param string $orientation The orientation of the document (default is 'portrait').
	 * @param string $type        The type of the document (default is 'none').
	 * @param array  $options     Additional options for the document configuration.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function __construct( $paper = 'a4', $orientation = 'portrait', $type = 'none', $options = [] ) {

		parent::__construct( $paper, $orientation, $type, $options );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$this->isUnicode = true;
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$this->tempPath = $this->get_cache_path();
	}

	/**
	 * Apply font settings with filter support.
	 *
	 * @since 1.0.0
	 */
	public function apply_font(): void {

		$default_font = [
			'name' => 'FreeSerif',
			'path' => WPFORMS_ENTRY_AUTOMATION_PATH . 'vendor_prefixed/src/fonts',
		];

		/**
		 * Filter the font settings for PDF export.
		 *
		 * @since 1.0.0
		 *
		 * @param array $font Font settings array.
		 */
		$font = (array) apply_filters( 'wpforms_entry_automation_helpers_vendor_pdf_font', $default_font );

		$font['path'] = rtrim( $font['path'], '/' );

		if ( ! $this->is_font_valid( $font ) ) {
			$this->debug(
				sprintf( 'Font: %s inside %s is not exist.', $font['name'] ?? '', $font['path'] ),
				\E_USER_ERROR
			);
			$font = $default_font;
		}

		$this->fontPath = $font['path']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		// Apply the font settings.
		$this->setFontFamily( $font['name'] );
		$this->selectFont( $font['name'] );
	}

	/**
	 * Logs debug messages based on the provided message and error type if within the debug level.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message    The debug message to be logged.
	 * @param int    $error_type The type of the error (default is \E_USER_NOTICE).
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	protected function debug( $message, $error_type = \E_USER_NOTICE ) {

		if ( $error_type > $this->DEBUGLEVEL ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return;
		}

		wpforms_log(
			'Entry Automation - PDF Generation Error',
			[
				'message'    => $message,
				'error_type' => $error_type,
			],
			[ 'type' => 'error' ]
		);
	}

	/**
	 * Get the cache path for font data.
	 *
	 * @since 1.2.0
	 *
	 * @return string The cache directory path.
	 */
	private function get_cache_path(): string {

		$cache_path = File::get_cache_dir();

		wp_mkdir_p( $cache_path );

		return $cache_path;
	}

	/**
	 * Checks if the given font is valid based on its name and path.
	 *
	 * @since 1.0.0
	 *
	 * @param array $font An associative array containing font information, where
	 *                    'name' is the font name and 'path' is the directory path to the font.
	 *
	 * @return bool True if the font is valid, otherwise false.
	 */
	private function is_font_valid( array $font ): bool {

		if ( empty( $font['name'] ) || empty( $font['path'] ) ) {
			return false;
		}

		$full_path = $font['path'] . '/' . $font['name'];

		return File::exists( $full_path . '.ttf' ) || File::exists( $full_path . '.afm' );
	}
}
