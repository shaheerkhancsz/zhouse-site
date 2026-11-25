<?php

namespace WPFormsEntryAutomation\Database;

use WPForms_DB;

/**
 * Class Tasks.
 *
 * @since 1.0.0
 */
class Tasks extends WPForms_DB {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		global $wpdb;

		parent::__construct();

		$this->table_name  = $wpdb->prefix . 'wpforms_entry_automation_tasks';
		$this->primary_key = 'id';
		$this->type        = 'entry_automation_tasks';
	}

	/**
	 * Get table columns.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_columns(): array {

		return [
			'id'              => '%d',
			'form_id'         => '%d',
			'action_id'       => '%d',
			'connection_id'   => '%s',
			'connection_name' => '%s',
			'type'            => '%s',
			'export_to'       => '%s',
			'destination'     => '%s',
			'file'            => '%s',
			'status'          => '%s',
			'parent_id'       => '%s',
			'schedule'        => '%s',
			'last_run'        => '%s',
			'created_at'      => '%s',
			'updated_at'      => '%s',
		];
	}

	/**
	 * Default column values.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_column_defaults(): array {

		return [
			'form_id'         => 0,
			'action_id'       => 0,
			'connection_id'   => '',
			'connection_name' => '',
			'type'            => '',
			'export_to'       => null,
			'destination'     => null,
			'file'            => null,
			'status'          => 'active',
			'parent_id'       => '',
			'schedule'        => null,
			'last_run'        => null,
			'created_at'      => gmdate( 'Y-m-d H:i:s' ),
			'updated_at'      => gmdate( 'Y-m-d H:i:s' ),
		];
	}

	/**
	 * Create a new task.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Task data.
	 *
	 * @return int
	 */
	public function create( array $data ): int {

		return (int) $this->add( $data, 'entry_automation_task' );
	}

	/**
	 * Get tasks by form ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $form_id Form ID.
	 * @param array $args    Additional arguments.
	 *
	 * @return array
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function get_by_form_id( $form_id, array $args = [] ): array {

		global $wpdb;

		$defaults = [
			'number'  => 30,
			'offset'  => 0,
			'orderby' => 'id',
			'order'   => 'DESC',
			'status'  => '',
			'type'    => '',
		];

		$args = wp_parse_args( $args, $defaults );

		$limit = '';

		if ( $args['number'] !== -1 ) {
			$limit = $wpdb->prepare( 'LIMIT %d,%d', $args['offset'], $args['number'] );
		}

		$where = $wpdb->prepare( 'WHERE form_id = %d', $form_id );

		if ( ! empty( $args['status'] ) ) {
			$where .= $wpdb->prepare( ' AND status = %s', $args['status'] );
		}

		if ( ! empty( $args['type'] ) ) {
			$where .= $wpdb->prepare( ' AND type = %s', $args['type'] );
		}

		$orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
		$orderby = ! empty( $orderby ) ? "ORDER BY {$orderby}" : '';

		$sql = "SELECT * FROM {$this->table_name} {$where} {$orderby} {$limit}";

		$tasks = $this->get_results( $sql, ARRAY_A );

		return array_reduce(
			$tasks,
			static function ( $carry, $task ) {
				$carry[ $task['connection_id'] ] = $task;

				return $carry;
			},
			[]
		);
	}

	/**
	 * Get action ID by task ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $task_id Task ID.
	 *
	 * @return string|null Action ID.
	 */
	public function get_action_id( int $task_id ): ?string {

		global $wpdb;

		$sql = $wpdb->prepare( "SELECT action_id FROM {$this->table_name} WHERE id = %d", $task_id ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return $this->get_var( $sql );
	}

	/**
	 * Get the task data by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $task_id Task ID.
	 *
	 * @return mixed
	 */
	public function get_by_id( int $task_id ) {

		global $wpdb;

		$sql = $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $task_id ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return $this->get_row( $sql, ARRAY_A );
	}

	/**
	 * Get all tasks.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Query arguments.
	 *
	 * @return array|object|null
	 */
	public function get_all( array $args = [] ) {

		global $wpdb;

		$defaults = [
			'number'          => 30,
			'offset'          => 0,
			'orderby'         => 'id',
			'order'           => 'DESC',
			'status'          => '',
			'type'            => '',
			'form_id'         => '',
			'parent_id'       => '',
			'connection_name' => '',
		];

		$args = wp_parse_args( $args, $defaults );

		$limit = '';

		if ( $args['number'] !== -1 ) {
			$limit = $wpdb->prepare( 'LIMIT %d,%d', $args['offset'], $args['number'] );
		}

		$where = 'WHERE 1=1 AND status != "trashed"';

		if ( ! empty( $args['status'] ) ) {
			$where .= $wpdb->prepare( ' AND status = %s', $args['status'] );
		}

		if ( ! empty( $args['type'] ) ) {
			$where .= $wpdb->prepare( ' AND type = %s', $args['type'] );
		}

		if ( ! empty( $args['form_id'] ) ) {
			$where .= $wpdb->prepare( ' AND form_id = %d', $args['form_id'] );
		}

		if ( ! empty( $args['parent_id'] ) ) {
			$where .= $wpdb->prepare( ' AND parent_id = %s', $args['parent_id'] );
		}

		if ( ! empty( $args['form_title'] ) ) {
			$where .= $wpdb->prepare( ' AND form_id IN (SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title LIKE %s)', '%' . $wpdb->esc_like( $args['form_title'] ) . '%' );
		}

		if ( ! empty( $args['connection_name'] ) ) {
			$where .= $wpdb->prepare( ' AND connection_name LIKE %s', '%' . $wpdb->esc_like( $args['connection_name'] ) . '%' );
		}

		$orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
		$orderby = ! empty( $orderby ) ? "ORDER BY {$orderby}" : '';

		$sql = "SELECT * FROM {$this->table_name} {$where} {$orderby} {$limit}";

		return $this->get_results( $sql, ARRAY_A );
	}

	/**
	 * Get all parent tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return array|object|null
	 */
	public function get_all_parents() {

		$sql = "SELECT * FROM {$this->table_name} WHERE parent_id IS NULL";

		return $this->get_results( $sql, ARRAY_A );
	}

	/**
	 * Retrieve tasks by connection IDs with optional filters.
	 *
	 * @since 1.0.0
	 *
	 * @param array $ids     List of connection IDs to filter by.
	 * @param array $db_args Database query arguments, including:
	 *                       - 'status': (string) Filter by record status.
	 *                       - 'number': (int) Number of records to retrieve (-1 for no limit).
	 *                       - 'offset': (int) Offset for the records retrieval.
	 *
	 * @return array Array of records matching the specified connection IDs and query filters.
	 */
	public function get_by_connection_ids( array $ids, array $db_args ): array {

		global $wpdb;

		$ids    = array_map( 'sanitize_key', $ids );
		$ids_in = "'" . implode( "', '", $ids ) . "'";

		$where = 'WHERE 1=1 AND status != "trashed"';
		$limit = '';

		if ( ! empty( $db_args['status'] ) ) {
			$where .= $wpdb->prepare( ' AND status = %s', $db_args['status'] );
		}

		if ( $db_args['number'] !== -1 ) {
			$limit = $wpdb->prepare( 'LIMIT %d,%d', $db_args['offset'], $db_args['number'] );
		}

		$sql = "SELECT * FROM {$this->table_name} {$where} AND connection_id IN ({$ids_in}) {$limit}";

		return $this->get_results( $sql, ARRAY_A );
	}

	/**
	 * Update task status.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $task_id Task ID.
	 * @param string $status  New status.
	 */
	public function update_status( int $task_id, string $status ): void {

		$this->update(
			$task_id,
			[ 'status' => $status ],
			'',
			'entry_automation_task'
		);
	}

	/**
	 * Update the last run time for a task.
	 *
	 * @since 1.0.0
	 *
	 * @param int $task_id The ID of the task to update.
	 *
	 * @return bool False on failure, or the number of rows updated on success.
	 */
	public function update_last_run( int $task_id ): bool {

		return $this->update(
			$task_id,
			[
				'last_run'   => gmdate( 'Y-m-d H:i:s' ),
				'updated_at' => gmdate( 'Y-m-d H:i:s' ),
			],
			'',
			'entry_automation_task'
		);
	}

	/**
	 * Get counts of tasks by status.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array with counts for 'all', 'active', 'inactive', and 'failed' statuses.
	 */
	public function get_counts(): array {

		$statuses = [ 'active', 'inactive', 'failed' ];
		$counts   = array_fill_keys( $statuses, 0 );

		$sql = "SELECT status, COUNT(*) as count FROM {$this->table_name}
				WHERE status IN ('active', 'inactive', 'failed') AND status != 'trashed'
				GROUP BY status";

		$results = $this->get_results( $sql );

		if ( $results ) {
			foreach ( $results as $result ) {
				$counts[ $result->status ] = (int) $result->count;
			}
		}

		$counts['all'] = array_sum( $counts );

		return $counts;
	}
}
