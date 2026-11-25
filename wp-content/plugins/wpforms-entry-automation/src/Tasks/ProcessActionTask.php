<?php

namespace WPFormsEntryAutomation\Tasks;

use WPForms\Tasks\Task;
use WPForms\Tasks\Meta;
use WPFormsEntryAutomation\Plugin;

/**
 * Class ProcessActionTask.
 *
 * @since 1.0.0
 */
class ProcessActionTask extends Task {

	/**
	 * Task action.
	 *
	 * @since 1.0.0
	 */
	public const ACTION = 'wpforms_entry_automation_process_action_task';

	/**
	 * Task to create tasks.
	 *
	 * @since 1.0.0
	 */
	public const CREATE_TASKS_ACTION = 'wpforms_entry_automation_action_create_tasks';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks(): void {

		// Register the task to recreate all entry automation tasks only after ActionScheduler is usable.
		add_action( 'action_scheduler/migration_complete', [ $this, 'add_recreate_task' ] );

		add_action( self::ACTION, [ $this, 'process' ] );
		add_action( self::CREATE_TASKS_ACTION, [ $this, 'create_tasks' ] );
		add_filter( 'wpforms_entry_handler_get_entries_where', [ $this, 'set_correct_dates' ], 10, 2 );
	}

	/**
	 * Process the task.
	 *
	 * @since 1.0.0
	 *
	 * @param int $meta_id Task meta ID.
	 */
	public function process( int $meta_id ): void {

		// Retrieve AS task meta data.
		$task_data = $this->get_task_meta_data( $meta_id );

		// Check if the task is allowed to run based on the schedule.
		if ( ! $this->process_allowed( $task_data ) ) {
			return;
		}

		$task_id = $task_data['task_id'] ?? 0;

		// Get the Entry Automation task from custom table.
		$task = wpforms_entry_automation()->get( 'tasks' )->get_by_id( $task_id );

		if ( ! $this->is_active( $task ) ) {
			$this->log_inactive( $task );

			return;
		}

		$form_id       = $task['form_id'] ?? 0;
		$connection_id = $task['connection_id'] ?? 0;

		// Get the form data.
		$form_data = wpforms()->obj( 'form' )->get( $form_id, [ 'content_only' => true ] );

		// Get the entry automation task from the form data.
		$connection = $this->get_connection( $connection_id, $form_data );

		// Process the action.
		$this->process_action( $form_id, $task_id, $connection, $form_data );
	}

	/**
	 * Log inactive task.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task Task data.
	 */
	private function log_inactive( array $task ): void {

		wpforms_log(
			'Entry Automation Task: Process stopped',
			[
				'form_id'       => $task['form_id'] ?? 0,
				'connection_id' => $task['connection_id'] ?? 0,
				'task_id'       => $task['id'] ?? 0,
				'action_id'     => $task['action_id'] ?? 0,
				'status'        => $task['status'] ?? 'unknown',
			],
			[ 'type' => 'log' ]
		);
	}

	/**
	 * Check if the task is active.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task Task data.
	 *
	 * @return bool True if the task is active or failed, false otherwise.
	 */
	private function is_active( $task ): bool {

		if ( empty( $task ) ) {
			return false;
		}

		// Allow the task to run if it is active or failed.
		return in_array( $task['status'], [ 'active', 'failed' ], true );
	}

	/**
	 * Get task meta data.
	 *
	 * @since 1.0.0
	 *
	 * @param int $meta_id Task meta ID.
	 *
	 * @return array Task meta data.
	 */
	private function get_task_meta_data( int $meta_id ): array {

		$task_meta = new Meta();
		$meta      = $task_meta->get( $meta_id );

		// We should actually receive something.
		if ( empty( $meta ) || empty( $meta->data ) || ! is_array( $meta->data ) ) {
			return [];
		}

		return $meta->data[0] ?? [];
	}

	/**
	 * Get the entry automation task from the form data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $connection_id Connection ID.
	 * @param array  $form_data     Form data.
	 *
	 * @return array Entry automation task.
	 */
	private function get_connection( string $connection_id, array $form_data ): array {

		$entry_automation = $form_data['settings'][ Plugin::SLUG ] ?? [];

		if ( empty( $entry_automation ) ) {
			return [];
		}

		$connection = $entry_automation[ $connection_id ] ?? [];

		$status = $connection['status'] ?? '1';

		// Stop if the task is not active.
		if ( ! (int) $status ) {
			return [];
		}

		return $connection;
	}

	/**
	 * Process the action.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $form_id    Form ID.
	 * @param int   $task_id    Task ID.
	 * @param array $connection Connection data.
	 * @param array $form_data  Form data.
	 */
	private function process_action( int $form_id, int $task_id, array $connection, array $form_data ): void {

		if ( empty( $connection ) ) {
			return;
		}

		$action       = $connection['action'] ?? 'export';
		$task_manager = wpforms_entry_automation()->get( 'tasks' );

		try {
			$result = $this->execute( $form_id, $task_id, $action, $connection, $form_data );
		} catch ( \Throwable $e ) {
			wpforms_log(
				'Entry Automation Task: Execution error',
				[
					'form_id' => $form_id,
					'task_id' => $task_id,
					'error'   => $e->getMessage(),
				],
				[ 'type' => 'error' ]
			);

			$result = false;
		}

		// If the task was successful, update the status to active and process related tasks.
		if ( $result ) {
			$task_manager->update_status( $task_id, 'active' );

			// Get related tasks.
			$related_tasks = $task_manager->get_all(
				[
					'form_id'   => $form_id,
					'parent_id' => $connection['id'],
				]
			);

			foreach ( $related_tasks as $related_task ) {
				// Retrieve the related task data from the form data.
				$related_connection = $this->get_connection( $related_task['connection_id'], $form_data );

				$this->process_action(
					$form_id,
					$related_task['id'],
					$related_connection,
					$form_data
				);
			}
		} else {
			$task_manager->update_status( $task_id, 'failed' );
		}

		// Update the last run time.
		$task_manager->update_last_run( $task_id );
	}

	/**
	 * Execute the task based on the action.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $form_id    Form ID.
	 * @param int    $task_id    Task ID.
	 * @param string $action     Action type (export or delete).
	 * @param array  $connection Connection data.
	 * @param array  $form_data  Form data.
	 *
	 * @return bool
	 */
	private function execute( int $form_id, int $task_id, string $action, array $connection, array $form_data ): bool {

		$filters = $connection['filters'] ?? [];

		$task = wpforms_entry_automation()->get( 'tasks' )->get_by_id( $task_id );

		if ( empty( $task ) || ! $this->is_active( $task ) ) {
			return false;
		}

		$args = $this->prepare_args( $form_id, $filters, $connection, $task );

		wpforms_entry_automation()->get( 'tasks' )->update_status( $task_id, 'running' );

		if ( $action === 'export' ) {
			$connection['task_id'] = $task_id;

			return ( new ExportTask() )->execute( $connection, $form_data, $args, $task );
		}

		if ( $action === 'delete' ) {
			return ( new DeleteTask() )->execute( $args );
		}

		return false;
	}

	/**
	 * Prepare arguments for the export.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $form_id    Form ID.
	 * @param array $filters    Filters.
	 * @param array $connection Connection data.
	 * @param array $task       Task data.
	 *
	 * @return array Arguments.
	 */
	private function prepare_args( int $form_id, array $filters, array $connection, array $task ): array {

		$statuses = $filters['statuses'] ?? [];

		// Replace `published` with empty string.
		$statuses = array_map(
			function ( $status ) {
				return $status === 'published' ? '' : $status;
			},
			$statuses
		);

		$args = [
			'form_id'         => $form_id,
			'entry_id'        => 0,
			'additional_info' => $connection['additional_field'] ?? [],
			'status'          => $statuses,
			'dates'           => $this->get_dates( $connection, $task ),
			'fields'          => $connection['form_field'] ?? [],
			'search'          => [ 'term' => '' ],
			'number'          => -1,
		];

		if ( $filters['value'] !== '' ) {
			$args['search']['term']       = $filters['value'];
			$args['search']['comparison'] = $filters['operator'];
			$args['search']['field']      = $filters['field'];
		}

		return $args;
	}

	/**
	 * Get the dates for the task.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection            Connection data.
	 * @param array $entry_automation_task Entry automation task.
	 *
	 * @return array Dates.
	 */
	private function get_dates( array $connection, array $entry_automation_task ): array {

		$entries_number = $connection['entries_number'] ?? false;

		if ( $entries_number === 'new' && ! empty( $entry_automation_task['last_run'] ) ) {
			return [
				$entry_automation_task['last_run'],
				gmdate( 'Y-m-d H:i:s', strtotime( '+1 day' ) ), // Any date in the future.
				/**
				 * Additional parameter to prevent the date from being rounded to the start of the day.
				 * Default method check if the date array contains only two elements.
				 */
				'period' => false,
			];
		}

		return [];
	}

	/**
	 * Set the correct dates for the query.
	 * Default request using `wpforms_get_day_period_date` function.
	 * This means that the date round to the start of the day.
	 * We need to set the correct date range for the query.
	 *
	 * @since 1.0.0
	 *
	 * @param array $where Query where clause.
	 * @param array $args  Query arguments.
	 *
	 * @return array Modified where clause.
	 */
	public function set_correct_dates( $where, $args ): array {

		if ( empty( $args['date'] ) ) {
			return $where;
		}

		if ( is_array( $args['date'] ) && count( $args['date'] ) === 3 && isset( $args['date']['period'] ) && $args['date']['period'] === false ) {
			$table_name = wpforms()->obj( 'entry' )->table_name;

			$date_start = gmdate( 'Y-m-d H:i:s', strtotime( $args['date'][0] ) );
			$date_end   = gmdate( 'Y-m-d H:i:s', strtotime( $args['date'][1] ) );

			if ( ! empty( $date_start ) && ! empty( $date_end ) ) {
				$where['arg_date_start'] = "$table_name.date >= '$date_start'";
				$where['arg_date_end']   = "$table_name.date <= '$date_end'";
			}
		}

		return $where;
	}

	/**
	 * Check if the task is allowed to run based on the schedule.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Task data.
	 *
	 * @return bool
	 */
	private function process_allowed( array $data ): bool {

		if ( empty( $data ) ) {
			return false;
		}

		$current_date = strtotime( gmdate( 'Y-m-d' ) );

		if ( ! $this->is_in_date_range( $data, $current_date ) ) {
			return false;
		}

		$frequency = $data['frequency'] ?? '';
		$days      = $data['days'] ?? [];

		return $this->check_frequency_condition( $frequency, $days );
	}

	/**
	 * Check if current date is within the configured date range.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data         Task data.
	 * @param int   $current_date Current date timestamp.
	 *
	 * @return bool
	 */
	private function is_in_date_range( array $data, int $current_date ): bool {

		if ( ! $this->is_after_start_date( $data['start'] ?? '', $current_date ) ) {
			return false;
		}

		if ( ! $this->is_before_end_date( $data['end'] ?? '', $current_date ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if current date is after the start date.
	 *
	 * @since 1.0.0
	 *
	 * @param string $start_date   Start date string.
	 * @param int    $current_date Current date timestamp.
	 *
	 * @return bool
	 */
	private function is_after_start_date( string $start_date, int $current_date ): bool {

		if ( empty( $start_date ) ) {
			return true;
		}

		$start_timestamp = strtotime( $start_date );

		return $start_timestamp <= $current_date;
	}

	/**
	 * Check if current date is before the end date.
	 *
	 * @since 1.0.0
	 *
	 * @param string $end_date     End date string.
	 * @param int    $current_date Current date timestamp.
	 *
	 * @return bool
	 */
	private function is_before_end_date( string $end_date, int $current_date ): bool {

		if ( empty( $end_date ) ) {
			return true;
		}

		$end_timestamp = strtotime( $end_date );

		return $end_timestamp >= $current_date;
	}

	/**
	 * Check if the current day matches the frequency condition.
	 *
	 * @since 1.0.0
	 *
	 * @param string $frequency Frequency setting (week, month, first, last).
	 * @param array  $days      Days configuration.
	 *
	 * @return bool
	 */
	private function check_frequency_condition( string $frequency, array $days ): bool {

		switch ( $frequency ) {
			case 'week':
				$condition = $this->is_matching_weekday( $days );
				break;

			case 'month':
				$condition = $this->is_matching_day_of_month( $days );
				break;

			case 'first':
				$condition = $this->is_first_day_of_month();
				break;

			case 'last':
				$condition = $this->is_last_day_of_month();
				break;

			default:
				$condition = false;
		}

		return $condition;
	}

	/**
	 * Check if current day is in the list of allowed weekdays.
	 *
	 * @since 1.0.0
	 *
	 * @param array $days List of allowed weekdays.
	 *
	 * @return bool
	 */
	private function is_matching_weekday( array $days ): bool {

		$current_week_day = strtolower( gmdate( 'D' ) );

		return in_array( $current_week_day, $days, true );
	}

	/**
	 * Check if current day is in the list of allowed days of month.
	 *
	 * @since 1.0.0
	 *
	 * @param array $days List of allowed days of month.
	 *
	 * @return bool
	 */
	private function is_matching_day_of_month( array $days ): bool {

		$current_day = strtolower( gmdate( 'd' ) );

		return in_array( (int) $current_day, $days, true );
	}

	/**
	 * Check if today is the first day of the month.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_first_day_of_month(): bool {

		$today              = gmdate( 'Y-m-d' );
		$first_day_of_month = gmdate( 'Y-m-01' );

		return $today === $first_day_of_month;
	}

	/**
	 * Check if today is the last day of the month.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_last_day_of_month(): bool {

		$today             = gmdate( 'Y-m-d' );
		$last_day_of_month = gmdate( 'Y-m-t' );

		return $today === $last_day_of_month;
	}

	/**
	 * Add a task to the queue.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $task_id  Task ID.
	 * @param array $schedule Task schedule.
	 */
	public function add( $task_id, $schedule ): void {

		$params = array_merge( $schedule, [ 'task_id' => $task_id ] );

		$time      = $schedule['time'];
		$timestamp = date_create( 'today ' . $time, wp_timezone() )->format( 'U' );

		// If the time has already passed today, schedule for tomorrow.
		if ( $timestamp < time() ) {
			$timestamp = date_create( 'tomorrow ' . $time, wp_timezone() )->format( 'U' );
		}

		$action_id = wpforms()->obj( 'tasks' )
			->create( self::ACTION )
			->recurring( $timestamp, DAY_IN_SECONDS )
			->params( $params )
			->register();

		wpforms_entry_automation()->get( 'tasks' )->update( $task_id, [ 'action_id' => $action_id ] );
	}

	/**
	 * Create tasks for all entry automation tasks.
	 *
	 * @since 1.0.0
	 */
	public function create_tasks(): void {

		$tasks = wpforms_entry_automation()->get( 'tasks' )->get_all_parents();

		if ( empty( $tasks ) ) {
			return;
		}

		foreach ( $tasks as $task ) {
			$task_id = (int) ( $task['id'] ?? 0 );

			if ( empty( $task_id ) ) {
				continue;
			}

			$schedule = json_decode( $task['schedule'], true );

			if ( empty( $schedule ) ) {
				continue;
			}

			$this->add( $task_id, $schedule );
		}
	}

	/**
	 * Schedule a one-time task to recreate all entry automation tasks.
	 *
	 * @since 1.0.0
	 */
	public function add_recreate_task(): void {

		wpforms()->obj( 'tasks' )
			->create( self::CREATE_TASKS_ACTION )
			->once( time() )
			->register();
	}

	/**
	 * Unschedule a task.
	 *
	 * @since 1.0.0
	 *
	 * @param int $action_id Action ID.
	 */
	public function unschedule( $action_id ): void {

		$action = wpforms()->obj( 'tasks' )->fetch_action( $action_id );

		if ( $action === null ) {
			return;
		}

		as_unschedule_action( self::ACTION, $action->get_args() );
	}
}
