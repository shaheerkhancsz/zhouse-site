<?php

namespace WPFormsSlack\Provider\Settings;

use WPForms\Admin\Notice;
use WPFormsSlack\Api\Auth;
use WPForms\Providers\Provider\Settings\PageIntegrations as PageIntegrationsAbstract;

/**
 * Class PageIntegrations handles functionality on the Settings > Integrations page.
 *
 * @since 1.0.0
 */
class PageIntegrations extends PageIntegrationsAbstract {

	/**
	 * Integrations hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'wpforms_settings_init', [ $this, 'display_notice' ] );
	}

	/**
	 * Display admin error notice if something wrong with the settings.
	 *
	 * @since 1.0.0
	 */
	public function display_notice() {

		if ( ! wpforms_is_admin_page( 'settings', 'integrations' ) ) {
			return;
		}

		$this->display_account_exists_notice();

		$this->display_api_notices();
	}

	/**
	 * Display admin notice if an account already exists.
	 *
	 * @since 1.0.0
	 */
	private function display_account_exists_notice() {
		// Retrieve the transient value.
		$error_message = get_transient( 'wpforms_slack_error_message' );

		// Check if the transient contains a value.
		if ( $error_message ) {
			// Display the error message as a notice.
			Notice::error( esc_html( $error_message ) );

			// Delete the transient after displaying it.
			delete_transient( 'wpforms_slack_error_message' );
		}
	}

	/**
	 * Display admin error notices for errors in the API connection.
	 *
	 * @since 1.1.0
	 */
	private function display_api_notices(): void {

		foreach ( wpforms_slack()->get( 'account' )->get_invalid_accounts() as $account_id => $account ) {

			$this->display_expired_accounts_notice( $account_id, $account );

			$this->display_missing_scope_accounts_notice( $account_id, $account );
		}
	}

	/**
	 * Display an admin error notice if an account is expired.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added $account_id parameter and $account parameter.
	 *
	 * @param string $account_id Account ID.
	 * @param array  $account    Account data.
	 */
	private function display_expired_accounts_notice( string $account_id, array $account ): void {

		if ( empty( $account['account_error'] ) ) {
			return;
		}

		if ( $account['account_error'] !== 'invalid_auth' ) {
			return;
		}

		Notice::error( wpforms_slack()->get( 'account' )->get_expired_account_notice_message( $account_id, $account ) );
	}

	/**
	 * Display an admin error notice if an account is missing required scopes.
	 *
	 * @since 1.1.0
	 *
	 * @param string $account_id Account ID.
	 * @param array  $account    Account data.
	 */
	private function display_missing_scope_accounts_notice( string $account_id, array $account ): void {

		if ( empty( $account['account_error'] ) ) {
			return;
		}

		if ( $account['account_error'] !== 'missing_scope' ) {
			return;
		}

		Notice::error( wpforms_slack()->get( 'account' )->get_missing_scope_account_notice_message( $account_id, $account ) );
	}

	/**
	 * Any new connection should be added.
	 * So display the content of that.
	 *
	 * @since 1.0.0
	 */
	protected function display_add_new() {
		?>
		<p>
			<a class="wpforms-btn wpforms-btn-md wpforms-btn-light-grey wpforms-slack-auth" href="<?php echo esc_url( Auth::get_authorization_url() ); ?>">
				<i class="fa fa-plus"></i> <?php esc_html_e( 'Add New Account', 'wpforms-slack' ); ?>
			</a>
		</p>
		<p>
			<?php
			printf(
				wp_kses( /* translators: %1$s - Documentation URL. */
					__(
						'If you need help connecting WPForms to Slack, <a href="%1$s" rel="noopener noreferrer" target="_blank">read our documentation</a>.',
						'wpforms-slack'
					),
					[
						'a' => [
							'href'   => [],
							'rel'    => [],
							'target' => [],
						],
					]
				),
				esc_url( wpforms_utm_link( 'https://wpforms.com/docs/slack-addon/', 'Settings - Integration', 'Slack Documentation' ) )
			);
			?>
		</p>
		<?php
	}
}
