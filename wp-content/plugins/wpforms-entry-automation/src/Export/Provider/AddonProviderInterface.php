<?php

namespace WPFormsEntryAutomation\Export\Provider;

interface AddonProviderInterface {

	/**
	 * Checks whether the required addon is activated.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the addon is activated, false otherwise.
	 */
	public function is_activated(): bool;

	/**
	 * Retrieve addon data.
	 *
	 * @since 1.0.0
	 *
	 * @return array Addon data.
	 */
	public function get_data(): array;
}
