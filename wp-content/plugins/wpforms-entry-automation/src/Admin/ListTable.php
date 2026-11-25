<?php

namespace WPFormsEntryAutomation\Admin;

use WP_List_Table;
use WPForms\Admin\Notice;
use WPFormsEntryAutomation\Plugin;

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the WP_List_Table class if it's not already available.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Entry Automation List Table.
 *
 * @since 1.0.0
 */
class ListTable extends WP_List_Table {

	/**
	 * Number of items to show per page.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $per_page;

	/**
	 * Counts of tasks by status.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $counts;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

		// Set default values.
		$this->per_page = $this->get_items_per_page( 'wpforms_tasks_per_page' );

		$this->process_bulk_actions();
		$this->remove_get_params();
		$this->display_notice();
	}

	/**
	 * Retrieve the table columns.
	 *
	 * @since 1.0.0
	 *
	 * @return array $columns Array of all the list table columns.
	 */
	public function get_columns(): array {

		return [
			'cb'        => '<input type="checkbox" />',
			'task'      => esc_html__( 'Task', 'wpforms-entry-automation' ),
			'action'    => esc_html__( 'Action', 'wpforms-entry-automation' ),
			'export_to' => esc_html__( 'Export To', 'wpforms-entry-automation' ),
			'schedule'  => esc_html__( 'Schedule', 'wpforms-entry-automation' ),
			'last_run'  => esc_html__( 'Last Run', 'wpforms-entry-automation' ),
			'status'    => esc_html__( 'Status', 'wpforms-entry-automation' ),
		];
	}

	/**
	 * Display the rows in the list table.
	 *
	 * @since 1.0.0
	 */
	public function display_rows(): void {

		$displayed_forms = [];

		foreach ( $this->items as $item ) {

			if ( ! in_array( $item['form_id'], $displayed_forms, true ) ) {
				$this->get_form_name_row( (int) $item['form_id'] );

				$displayed_forms[] = $item['form_id'];
			}

			$this->single_row( $item );
		}
	}

	/**
	 * Render a single row in the list table.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Item data.
	 */
	public function single_row( $item ): void {

		echo '<tr class="wpforms-entry-automation-task-form-' . esc_attr( $item['form_id'] ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Render a single row in the list table.
	 *
	 * @since 1.0.0
	 *
	 * @param int $form_id Form ID to display the name for.
	 */
	private function get_form_name_row( int $form_id ): void {

		$form = wpforms()->obj( 'form' )->get( $form_id );

		$columns = $this->get_columns();
		$hidden  = get_hidden_columns( $this->screen );
		$colspan = count( $columns ) - count( $hidden ) - 1; // -1 for the checkbox column.

		echo '<tr class="wpforms-entry-automation-task-row">';

		echo '<th scope="row" class="check-column">';
		echo '<input type="checkbox" class="wpforms-entry-automation-select-all wpforms-entry-automation-select-form-' . esc_attr( $form_id ) . '" value="' . esc_attr( $form_id ) . '" />';
		echo '</th>';

		$form_name = $form->post_title;

		if ( $form->post_type === 'wpforms-template' ) {
			$form_name .= ' — ' . esc_html__( 'Template', 'wpforms-entry-automation' );
		}

		echo '<td colspan="' . esc_attr( $colspan ) . '" class="wpforms-entry-automation-task-form-name-cell">' . esc_html( $form_name ) . '</td>';

		echo '</tr>';
	}

	/**
	 * Get the list of bulk actions available for this table.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_bulk_actions(): array {

		return [
			'activate'   => esc_html__( 'Activate', 'wpforms-entry-automation' ),
			'deactivate' => esc_html__( 'Deactivate', 'wpforms-entry-automation' ),
		];
	}

	/**
	 * Process bulk actions.
	 *
	 * @since 1.0.0
	 */
	public function process_bulk_actions(): void {

		$this->display_bulk_action_message();

		// Check if the current request is a bulk action.
		if ( empty( $_REQUEST['action'] ) || ! isset( $_REQUEST['automation_id'] ) || ! is_array( $_REQUEST['automation_id'] ) ) {
			return;
		}

		// Verify nonce for security.
		check_admin_referer( 'bulk-' . $this->_args['plural'] );

		$action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) );
		$ids    = array_map( 'absint', $_REQUEST['automation_id'] );

		$this->process_action( $ids, $action );

		$query_args[ rtrim( $action, 'e' ) . 'ed' ] = count( $ids );

		// Redirect to avoid resubmission.
		wp_safe_redirect(
			add_query_arg(
				$query_args,
				remove_query_arg( [ 'action', 'action2', '_wpnonce', 'form_id', 'paged', '_wp_http_referer', 'bulk_action', 'automation_id' ] )
			)
		);
	}

	/**
	 * Remove unnecessary GET parameters from the request.
	 *
	 * @since 1.0.0
	 */
	private function remove_get_params(): void {

		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		// Remove specific GET parameters from the request URI.
		$_SERVER['REQUEST_URI'] = remove_query_arg(
			[
				'action',
				'action2',
				'_wpnonce',
				'_wp_http_referer',
			],
			wp_unslash( $_SERVER['REQUEST_URI'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		);
	}

	/**
	 * Process the selected action for the given IDs.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $ids    Array of IDs to process.
	 * @param string $action Action to perform (activate/deactivate).
	 */
	private function process_action( array $ids, string $action ): void {

		$status = '1';

		if ( $action === 'deactivate' ) {
			$status = '0';
		}

		foreach ( $ids as $id ) {
			$id = absint( $id );

			$task = wpforms_entry_automation()->get( 'tasks' )->get( $id );

			$form_id   = $task->form_id ?? 0;
			$form_data = wpforms()->obj( 'form' )->get( $form_id, [ 'content_only' => true ] );

			wpforms_entry_automation()->get( 'builder' )->update_connection_status( $form_id, $task->connection_id, $form_data, $status );
		}
	}

	/**
	 * Display a message after bulk actions.
	 *
	 * @since 1.0.0
	 */
	private function display_bulk_action_message(): void {

		$activated   = isset( $_REQUEST['activated'] ) ? absint( $_REQUEST['activated'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$deactivated = isset( $_REQUEST['deactivated'] ) ? absint( $_REQUEST['deactivated'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $activated ) {
			Notice::success(
				/* translators: %d: number of tasks activated. */
				esc_html( sprintf( _n( '%d task activated successfully.', '%d tasks activated successfully.', $activated, 'wpforms-entry-automation' ), $activated ) )
			);
		}

		if ( $deactivated ) {
			Notice::success(
				/* translators: %d: number of tasks deactivated. */
				esc_html( sprintf( _n( '%d task deactivated successfully.', '%d tasks deactivated successfully.', $deactivated, 'wpforms-entry-automation' ), $deactivated ) )
			);
		}
	}

	/**
	 * Message to be displayed when there are no items.
	 *
	 * @since 1.0.0
	 */
	public function no_items(): void {

		if ( $this->is_status_tab() ) {
			esc_html_e( 'No automation tasks found for the selected status.', 'wpforms-entry-automation' );

			return;
		}

		printf(
			'%s <a href="#" class="wpforms-entry-automation-task-add">%s</a>',
			esc_html__( 'You currently don’t have an automation tasks.', 'wpforms-entry-automation' ),
			esc_html__( 'Add New Automation Task', 'wpforms-entry-automation' )
		);
	}

	/**
	 * Render the checkbox column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Item data.
	 *
	 * @return string
	 */
	public function column_cb( $item ): string {

		return sprintf(
			'<input type="checkbox" name="automation_id[]" value="%d" data-form="%d" class="wpforms-entry-automation-select">',
			$item['id'],
			$item['form_id']
		);
	}

	/**
	 * Render the task column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Item data.
	 *
	 * @return string
	 */
	public function column_task( array $item ): string {

		$form_data = wpforms()->obj( 'form' )->get( $item['form_id'], [ 'content_only' => true ] );

		$class = 'wpforms-entry-automation-task-form-name';

		if ( $item['status'] === 'failed' ) {
			$class .= ' wpforms-entry-automation-task-form-name--failed';
		}

		return sprintf(
			'<div class="%2$s"><a href="%1$s">%3$s</a></div>',
			esc_url( admin_url( 'admin.php?page=wpforms-builder&view=settings&form_id=' . $item['form_id'] . '&section=' . Plugin::SLUG ) ),
			$class,
			$this->get_task_name( $form_data, $item )
		);
	}

	/**
	 * Get the task name based on the form data and task details.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data.
	 * @param array $task      Task details.
	 *
	 * @return string
	 */
	private function get_task_name( array $form_data, array $task ): string {

		$entry_automation = $form_data['settings'][ Plugin::SLUG ] ?? [];

		$form_id = $task['form_id'] ?? 0;
		$task_id = str_replace( '-' . $form_id, '', $task['connection_id'] );

		$task_data = $entry_automation[ $task_id ] ?? [];

		if ( empty( $task_data ) ) {
			return esc_html__( 'Unknown Task', 'wpforms-entry-automation' );
		}

		return $task_data['name'] ?? '';
	}

	/**
	 * Render the action column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Item data.
	 *
	 * @return string
	 */
	public function column_action( array $item ): string {

		/* translators: %s is the type of task, e.g., 'Export Entries' or 'Delete Entries'. */
		$label = sprintf( esc_html__( '%s Entries', 'wpforms-entry-automation' ), esc_html( ucfirst( $item['type'] ) ) );
		$icon  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0zM216 232l0 102.1 31-31c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-72 72c-9.4 9.4-24.6 9.4-33.9 0l-72-72c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l31 31L168 232c0-13.3 10.7-24 24-24s24 10.7 24 24z"/></svg>';

		if ( $item['type'] === 'delete' ) {
			$icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M170.5 51.6L151.5 80l145 0-19-28.4c-1.5-2.2-4-3.6-6.7-3.6l-93.7 0c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80 368 80l48 0 8 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-8 0 0 304c0 44.2-35.8 80-80 80l-224 0c-44.2 0-80-35.8-80-80l0-304-8 0c-13.3 0-24-10.7-24-24S10.7 80 24 80l8 0 48 0 13.8 0 36.7-55.1C140.9 9.4 158.4 0 177.1 0l93.7 0c18.7 0 36.2 9.4 46.6 24.9zM80 128l0 304c0 17.7 14.3 32 32 32l224 0c17.7 0 32-14.3 32-32l0-304L80 128zm80 64l0 208c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-208c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0l0 208c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-208c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0l0 208c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-208c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg>';
		}

		return sprintf(
			'<span class="wpforms-entry-automation-task-type">%2$s%1$s</span>',
			esc_html( $label ),
			$icon
		);
	}

	/**
	 * Render the export to column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Item data.
	 *
	 * @return string
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function column_export_to( $item ): string {

		if ( $item['type'] === '1' ) {
			return esc_html__( 'N/A', 'wpforms-entry-automation' );
		}

		$export_to   = $item['export_to'] ?? '';
		$destination = $item['destination'] ?? '';

		return $this->get_destination( $export_to, $destination );
	}

	/**
	 * Get the destination link based on the export type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $export_to   Export type.
	 * @param string $destination Destination.
	 *
	 * @return string
	 */
	private function get_destination( string $export_to, string $destination ): string {

		$destination_html = '';

		switch ( $export_to ) {
			case 'email':
				$destination_label = esc_html__( 'Email Address', 'wpforms-entry-automation' );
				$destination_link  = $this->get_emails_html( $destination );
				break;

			case 'ftp':
				$destination_label = esc_html__( 'FTP', 'wpforms-entry-automation' );
				$destination_link  = sprintf(
					'<span class="wpforms-entry-automation-task-destination--ftp">%1$s</span>',
					esc_html( $destination )
				);
				break;

			case 'dropbox':
				$destination_label = esc_html__( 'Dropbox', 'wpforms-entry-automation' );
				$destination_link  = $this->get_dropbox_link( $destination );
				break;

			case 'google':
				$destination_label = esc_html__( 'Google Drive', 'wpforms-entry-automation' );
				$destination_link  = $this->get_google_drive_link( $destination );
				break;

			default:
				$destination_html = sprintf( '<span class="wpforms-entry-automation-task-destination--empty">%1$s</span>', esc_html__( 'N/A', 'wpforms-entry-automation' ) );
		}

		if ( ! empty( $destination_label ) && isset( $destination_link ) ) {
			$destination_html = sprintf(
				'<span class="wpforms-entry-automation-task-destination--%1$s">%2$s</span></br>%3$s',
				esc_attr( $export_to ),
				$destination_label,
				$destination_link
			);
		}

		return $destination_html;
	}

	/**
	 * Generate HTML for email addresses.
	 *
	 * @since 1.0.0
	 *
	 * @param string $destination Comma-separated email addresses.
	 *
	 * @return string
	 */
	private function get_emails_html( string $destination ): string {

		$emails = explode( ',', $destination );
		$emails = array_map( 'sanitize_email', $emails );
		$emails = array_filter( $emails );

		$emails_html = '';

		foreach ( $emails as $email ) {
			$emails_html .= sprintf(
				'<a href="mailto:%1$s">%1$s</a><br>',
				esc_html( $email )
			);
		}

		return $emails_html;
	}

	/**
	 * Generate a Dropbox link based on the provided destination data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $destination JSON-encoded string containing Dropbox link data.
	 *                            If empty or invalid, an empty string will be returned.
	 *
	 * @return string HTML anchor tag linking to the Dropbox URL, or an empty string if the URL is invalid.
	 */
	private function get_dropbox_link( string $destination ): string {

		$parsed_destination = wpforms_is_json( $destination ) ? json_decode( $destination, true ) : [];
		$url                = $parsed_destination['url'] ?? '';

		if ( empty( $url ) ) {
			return '';
		}

		return sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( $url ),
			esc_html__( 'View on Dropbox', 'wpforms-entry-automation' )
		);
	}

	/**
	 * Generate a Google Drive link.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_id Google Drive file ID.
	 *
	 * @return string
	 */
	private function get_google_drive_link( string $file_id ): string {

		if ( empty( $file_id ) ) {
			return '';
		}

		return sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( 'https://drive.google.com/file/d/' . $file_id . '/view' ),
			esc_html__( 'View on Google Drive', 'wpforms-entry-automation' )
		);
	}

	/**
	 * Render the schedule column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Item data.
	 *
	 * @return string
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function column_schedule( $item ): string {

		if ( ! empty( $item['parent_id'] ) ) {
			return esc_html__( 'After previous task', 'wpforms-entry-automation' );
		}

		if ( ! empty( $item['schedule'] ) ) {
			return esc_html( wpforms_entry_automation()->get( 'scheduler' )::get_formatted_time( wpforms_json_decode( $item['schedule'], true ) ) );
		}

		return esc_html__( 'Not Scheduled', 'wpforms-entry-automation' );
	}

	/**
	 * Render the last run column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Item data.
	 *
	 * @return string
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function column_last_run( $item ): string {

		if ( ! empty( $item['last_run'] ) ) {
			return esc_html( wpforms_datetime_format( $item['last_run'], '', true ) );
		}

		return esc_html__( 'Never run', 'wpforms-entry-automation' );
	}

	/**
	 * Render the status column.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Item data.
	 *
	 * @return string
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function column_status( $item ): string {

		return sprintf( '<span class="wpforms-entry-automation-task-status wpforms-entry-automation-task-status--%1$s">%2$s</span>', esc_attr( $item['status'] ), $this->get_status_label( $item['status'] ) );
	}

	/**
	 * Get the label for the task status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Task status.
	 *
	 * @return string Human-readable status label.
	 */
	private function get_status_label( string $status ): string {

		$statuses = [
			'active'   => esc_html__( 'Active', 'wpforms-entry-automation' ),
			'inactive' => esc_html__( 'Inactive', 'wpforms-entry-automation' ),
			'failed'   => esc_html__( 'Failed', 'wpforms-entry-automation' ),
			'running'  => esc_html__( 'Running...', 'wpforms-entry-automation' ),
		];

		return $statuses[ $status ] ?? esc_html__( 'Unknown', 'wpforms-entry-automation' );
	}

	/**
	 * Define the data used in the table.
	 *
	 * @since 1.0.0
	 */
	public function prepare_items(): void {

		// Set column headers.
		$this->_column_headers = [
			$this->get_columns(),
			get_hidden_columns( $this->screen ),
			[
				'last_run' => [
					'last_run',
					false,
				],
			],
		];

		// Get counts of tasks by status.
		$this->counts = wpforms_entry_automation()->get( 'tasks' )->get_counts();

		$this->items = array_filter(
			$this->get_tasks(),
			static function ( $item ) {

				return wpforms_current_user_can( 'edit_form_single', $item['form_id'] );
			}
		);

		$total_items = $this->counts[ $this->get_selected_status() ] ?? 0;

		// Set pagination arguments.
		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'per_page'    => $this->per_page,
				'total_pages' => (int) ceil( $total_items / $this->per_page ),
			]
		);
	}

	/**
	 * Get tasks based on the current request parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_tasks(): array {

		$db_args = $this->get_db_args();

		return $this->get_sorted_tasks( $db_args );
	}

	/**
	 * Get the database query arguments for the current page.
	 *
	 * Assembles the arguments including pagination, ordering, status, and search term,
	 * to be used for querying the database based on the request parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array The query arguments for database retrieval.
	 */
	private function get_db_args(): array {

		// Get current page.
		$current_page = $this->get_pagenum();

		// Slice items array to get only items for current page.
		$offset = ( $current_page - 1 ) * $this->per_page;

		$data_args = array_merge(
			[
				'number' => $this->per_page,
				'offset' => $offset,
            ],
			$this->get_ordering_args()
		);

		$status = $this->get_selected_status();

		if ( ! empty( $status ) && $status !== 'all' ) {
			$data_args['status'] = $status;
		}

		$term = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! empty( $term ) ) {
			$data_args['connection_name'] = $term;
			$data_args['order']           = 'ASC';
		}

		return $data_args;
	}

	/**
	 * Get tasks ordered by the specified column.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_ordering_args(): array {

		if ( ! isset( $_REQUEST['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return [];
		}

		$oder_by = sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$order   = isset( $_REQUEST['order'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return [
			'orderby' => $oder_by,
			'order'   => $order,
		];
	}

	/**
	 * Check if the current request is for the status tab.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the status tab is active, false otherwise.
	 */
	private function is_status_tab(): bool {
		// Check if the 'status' parameter is set in the request.
		return ! empty( $_REQUEST['status'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Get the views for the list table.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_views(): array {

		$views           = [];
		$statuses        = [ 'all', 'active', 'inactive', 'failed' ];
		$selected_status = $this->get_selected_status();

		foreach ( $statuses as $key ) {
			$class = $selected_status === $key ? 'current' : '';
			$count = $this->counts[ $key ] ?? 0;

			$views[ $key ] = sprintf(
				'<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
				add_query_arg( [ 'status' => $key ], admin_url( 'admin.php?page=wpforms-tools&view=entry-automation' ) ),
				esc_attr( $class ),
				esc_html( ucfirst( $key ) ),
				$count
			);
		}

		return $views;
	}

	/**
	 * Retrieve and sort tasks based on provided database arguments.
	 *
	 * This method fetches all tasks using the provided database arguments, organizes them
	 * by form IDs, and populates the sorted tasks array. If no tasks match the arguments,
	 * an empty array is returned.
	 *
	 * @since 1.0.0
	 *
	 * @param array $db_args Database arguments to fetch tasks.
	 *
	 * @return array List of sorted tasks.
	 */
	private function get_sorted_tasks( array $db_args ): array {

		$tasks = wpforms_entry_automation()->get( 'tasks' )->get_all( $db_args );

		if ( empty( $tasks ) ) {
			return [];
		}

		$sorted_tasks = [];
		$connections  = [];

		foreach ( $tasks as $task ) {
			$connections[ $task['connection_id'] . '-' . $task['form_id'] ] = $task;
		}

		$form_ids = array_unique( wp_list_pluck( $tasks, 'form_id' ) );

		foreach ( $form_ids as $form_id ) {
			$form_data = wpforms()->obj( 'form' )->get( $form_id, [ 'content_only' => true ] );

			$entry_automation = $form_data['settings'][ Plugin::SLUG ] ?? [];

			foreach ( $entry_automation as $task ) {
				$id = $task['id'] . '-' . $form_id;

				$connection_data = $connections[ $id ] ?? [];

				$connection_data['form_id'] = $form_id;

				$sorted_tasks[ $id ] = $connection_data;
			}
		}

		$this->fill_empty_tasks( $sorted_tasks, $db_args );

		return $sorted_tasks;
	}

	/**
	 * Fills the empty tasks in the provided sorted tasks array by fetching data from the database.
	 *
	 * This method identifies empty tasks in the supplied array, retrieves the corresponding data
	 * from the database using the provided arguments, and updates the sorted tasks array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sorted_tasks Reference to the array of tasks, where each key represents a task ID and its value a corresponding task.
	 * @param array $db_args      Arguments used to fetch missing tasks from the database.
	 */
	private function fill_empty_tasks( array &$sorted_tasks, array $db_args ): void {

		$empty_tasks = [];

		foreach ( $sorted_tasks as $task_id => $task ) {
			// Empty task has only the "form_id" property.
			if ( empty( $task['id'] ) ) {
				$empty_tasks[] = explode( '-', $task_id )[0];
			}
		}

		if ( empty( $empty_tasks ) ) {
			return;
		}

		$tasks = wpforms_entry_automation()->get( 'tasks' )->get_by_connection_ids( $empty_tasks, $db_args );

		foreach ( $tasks as $task ) {
			$sorted_tasks[ $task['connection_id'] . '-' . $task['form_id'] ] = $task;
		}

		// Clean up empty tasks.
		foreach ( $sorted_tasks as $task_id => $task ) {
			if ( empty( $task['id'] ) ) {
				unset( $sorted_tasks[ $task_id ] );
			}
		}
	}

	/**
	 * Add screen options.
	 *
	 * @since 1.0.0
	 */
	public function screen_option(): void {

		$option = 'per_page';
		$args   = [
			'label'   => esc_html__( 'Number of tasks per page:', 'wpforms-entry-automation' ),
			'default' => $this->per_page,
			'option'  => 'wpforms_tasks_per_page',
		];

		add_screen_option( $option, $args );
	}

	/**
	 * Display list table page.
	 *
	 * @since 1.0.0
	 */
	public function display_page(): void {

		$this->prepare_items();

		$slug = $this->_args['plural'];

		echo '<div class="wpforms-list-table wpforms-list-table--entry-automation">';
		echo '<h2 class="wpforms-entry-automation-title">' . esc_html__( 'Entry Automation', 'wpforms-entry-automation' ) . '</h2>';
			echo '<form id="' . esc_attr( $slug ) . '-filter" method="get">';
				echo '<input type="hidden" name="page" value="wpforms-tools" />';
				echo '<input type="hidden" name="view" value="entry-automation" />';
				$this->search_box( esc_html__( 'Search Tasks', 'wpforms-entry-automation' ), 'wpforms-entry-automation-search' );
				$this->views();
				$this->display();
			echo '</form>';
		echo '</div>';
	}

	/**
	 * Display a notice about WP-Cron.
	 *
	 * @since 1.0.0
	 */
	private function display_notice(): void {

		$notice = sprintf(
			wp_kses( /* translators: %1$s - link to the WordPress Developer documentation. */
				__( 'Unlike a system cron, WP-Cron only runs when someone visits your site. This means tasks may not run exactly on schedule, but will trigger on the next page load. <a href="%1$s" target="_blank" rel="noopener noreferrer">Learn More</a>', 'wpforms-entry-automation' ),
				[
					'a' => [
						'href'   => [],
						'rel'    => [],
						'target' => [],
					],
				]
			),
			esc_url( 'https://developer.wordpress.org/plugins/cron/' )
		);

		$message = sprintf(
			'<p><strong>%1$s</strong><br>%2$s</p>',
			esc_html__( 'Heads Up About Scheduling', 'wpforms-entry-automation' ),
			$notice
		);

		Notice::info(
			$message,
			[
				'dismiss' => Notice::DISMISS_USER,
				'slug'    => 'wpforms-entry-automation-wp-cron-notice',
				'autop'   => false,
			]
		);
	}

	/**
	 * Retrieve the selected status from the request.
	 *
	 * @since 1.0.0
	 *
	 * @return string The sanitized selected status if set, otherwise 'all'.
	 */
	private function get_selected_status(): string {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : 'all';
	}
}
