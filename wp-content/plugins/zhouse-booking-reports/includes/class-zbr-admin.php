<?php
/**
 * Admin screen: Booking Calendar > Reports  (tabs: Report | Settings).
 *
 * @package Zhouse_Booking_Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZBR_Admin {

	const SLUG     = 'zbr-reports';
	const PER_PAGE = 50;

	private static $hook = '';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 ); // After Booking Calendar (priority 10).
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	public static function menu() {
		$cap = ZBR_Data::capability();
		if ( ZBR_Data::booking_calendar_active() ) {
			self::$hook = add_submenu_page( 'wpbc', 'Booking Reports', 'Reports', $cap, self::SLUG, array( __CLASS__, 'render' ) );
		} else {
			self::$hook = add_menu_page( 'Booking Reports', 'Booking Reports', $cap, self::SLUG, array( __CLASS__, 'render' ), 'dashicons-chart-bar', 58 );
		}
	}

	public static function assets( $hook_suffix ) {
		if ( $hook_suffix !== self::$hook ) {
			return;
		}
		wp_enqueue_style( 'zbr-admin', ZBR_URL . 'assets/admin.css', array(), ZBR_VERSION );
		wp_register_script( 'zbr-admin', false, array(), ZBR_VERSION, true );
		wp_enqueue_script( 'zbr-admin' );
		wp_add_inline_script(
			'zbr-admin',
			"(function(){var r=document.getElementById('zbr-range');if(!r)return;['zbr-from','zbr-to'].forEach(function(id){var el=document.getElementById(id);if(el){el.addEventListener('change',function(){r.value='custom';});}});r.addEventListener('change',function(){if(r.value!=='custom'){r.form.submit();}});})();"
		);
		if ( isset( $_GET['tab'] ) && 'settings' === $_GET['tab'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			ZBR_Settings::enqueue_picker();
		}
	}

	public static function export_url( $format, array $filters ) {
		$args = array_merge(
			array(
				'action' => 'zbr_export',
				'format' => $format,
			),
			ZBR_Data::query_args( $filters )
		);
		return wp_nonce_url( add_query_arg( rawurlencode_deep( $args ), admin_url( 'admin-post.php' ) ), 'zbr_export' );
	}

	public static function render() {
		if ( ! current_user_can( ZBR_Data::capability() ) ) {
			wp_die( esc_html__( 'You do not have permission to view booking reports.', 'zhouse-booking-reports' ) );
		}

		$tab = ( isset( $_GET['tab'] ) && 'settings' === $_GET['tab'] ) ? 'settings' : 'report'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		echo '<div class="wrap zbr-wrap">';
		echo '<h1 class="wp-heading-inline">Booking Reports</h1>';
		echo '<nav class="nav-tab-wrapper">';
		printf( '<a href="%s" class="nav-tab%s">Report</a>', esc_url( admin_url( 'admin.php?page=' . self::SLUG ) ), 'report' === $tab ? ' nav-tab-active' : '' );
		printf( '<a href="%s" class="nav-tab%s">Settings (branches)</a>', esc_url( admin_url( 'admin.php?page=' . self::SLUG . '&tab=settings' ) ), 'settings' === $tab ? ' nav-tab-active' : '' );
		echo '</nav>';

		if ( ! ZBR_Data::is_ready() ) {
			echo '<div class="notice notice-error"><p>Booking Calendar tables were not found. Please install and activate the Booking Calendar plugin.</p></div></div>';
			return;
		}

		if ( 'settings' === $tab ) {
			ZBR_Settings::render();
			echo '</div>';
			return;
		}

		$filters = ZBR_Data::sanitize_filters( wp_unslash( $_GET ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only report filters.
		$records = ZBR_Data::get_records( $filters );
		$report  = ZBR_Report::build( $records, $filters );

		self::render_filters( $filters );
		self::render_downloads( $filters, count( $records ) );
		self::render_unassigned_notice( $records );
		self::render_kpis( $report['totals'] );

		if ( empty( $records ) ) {
			echo '<div class="zbr-empty">No bookings found for the selected filters.</div></div>';
			return;
		}

		$sections = ZBR_Report::sections( $report );
		echo '<div class="zbr-grid">';
		self::render_breakdown( $sections['by_branch'], $report['by_branch'], 'Branch', 0, true );
		self::render_breakdown( $sections['by_company'], $report['by_company'], 'Company', 10 );
		self::render_breakdown( $sections['by_approver'], $report['by_approver'], 'Staff member' );
		self::render_breakdown( $sections['by_weekday'], $report['by_weekday'], 'Day' );
		self::render_breakdown( $sections['by_hour'], $report['by_hour'], 'Start time' );
		if ( count( $report['by_room'] ) > 1 ) {
			self::render_breakdown( $sections['by_room'], $report['by_room'], 'Room' );
		}
		echo '</div>';
		self::render_breakdown( $sections['by_period'], $report['by_period'], 'month' === $report['period_mode'] ? 'Month' : 'Date', 0, false, true );

		self::render_table( $records, $filters );
		echo '</div>';
	}

	/* ------------------------------------------------------------------ */

	private static function select( $label, $name, array $options, $current, $id = '' ) {
		$id = $id ? $id : 'zbr-' . $name;
		?>
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?>
			<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>">
				<?php foreach ( $options as $key => $text ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( (string) $current, (string) $key ); ?>><?php echo esc_html( $text ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<?php
	}

	private static function render_filters( array $f ) {
		$resources = ZBR_Data::get_resources();
		$companies = ZBR_Data::company_options();
		if ( '' !== $f['company'] && ! isset( $companies[ $f['company'] ] ) ) {
			$companies[ $f['company'] ] = $f['company'];
		}
		?>
		<form method="get" class="zbr-filters">
			<input type="hidden" name="page" value="<?php echo esc_attr( self::SLUG ); ?>">

			<?php self::select( 'Period', 'range', ZBR_Data::presets(), $f['range'] ); ?>
			<label for="zbr-from">From <input type="date" name="from" id="zbr-from" value="<?php echo esc_attr( $f['from'] ); ?>"></label>
			<label for="zbr-to">To <input type="date" name="to" id="zbr-to" value="<?php echo esc_attr( $f['to'] ); ?>"></label>
			<?php self::select( 'Filter dates by', 'basis', ZBR_Data::BASES, $f['basis'] ); ?>
			<?php self::select( 'Status', 'status', ZBR_Data::STATUSES, $f['status'] ); ?>
			<?php self::select( 'Branch', 'branch', array( '' => 'All branches' ) + ZBR_Data::branch_options(), $f['branch'] ); ?>
			<?php if ( count( $resources ) > 1 ) : ?>
				<?php self::select( 'Room', 'resource', array( 0 => 'All rooms' ) + $resources, $f['resource'] ); ?>
			<?php endif; ?>
			<?php self::select( 'Company', 'company', array( '' => 'All companies' ) + $companies, $f['company'] ); ?>
			<?php self::select( 'Group booking list by', 'group', ZBR_Data::GROUPS, ZBR_Data::group_param( $f ) ); ?>

			<label for="zbr-s">Search
				<input type="search" name="s" id="zbr-s" value="<?php echo esc_attr( $f['s'] ); ?>" placeholder="Name, email, phone, ID">
			</label>

			<button type="submit" class="button button-primary">Apply filters</button>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::SLUG ) ); ?>">Reset</a>
		</form>
		<?php
	}

	private static function render_downloads( array $f, $count ) {
		?>
		<div class="zbr-downloads">
			<span class="zbr-desc"><?php echo esc_html( ZBR_Data::describe_filters( $f ) ); ?> &mdash; <strong><?php echo esc_html( number_format_i18n( $count ) ); ?></strong> booking(s)</span>
			<span class="zbr-buttons">
				<a class="button button-primary" href="<?php echo esc_url( self::export_url( 'pdf', $f ) ); ?>"><span class="dashicons dashicons-pdf"></span> Download PDF report</a>
				<a class="button" href="<?php echo esc_url( self::export_url( 'csv', $f ) ); ?>"><span class="dashicons dashicons-media-spreadsheet"></span> Bookings CSV</a>
				<a class="button" href="<?php echo esc_url( self::export_url( 'summary', $f ) ); ?>"><span class="dashicons dashicons-chart-bar"></span> Summary CSV</a>
			</span>
		</div>
		<?php
	}

	private static function render_unassigned_notice( array $records ) {
		$n = 0;
		foreach ( $records as $r ) {
			if ( 'none' === $r['branch_id'] ) {
				$n++;
			}
		}
		if ( $n ) {
			printf(
				'<div class="notice notice-warning inline"><p>%s <a href="%s">Assign each booking calendar to a branch in Settings</a>.</p></div>',
				esc_html( sprintf( '%d booking(s) in this report are not linked to a branch and show as "Unassigned".', $n ) ),
				esc_url( admin_url( 'admin.php?page=' . self::SLUG . '&tab=settings' ) )
			);
		}
	}

	private static function render_kpis( array $t ) {
		$cards = array(
			array( 'Bookings', number_format_i18n( $t['bookings'] ), sprintf( '%d approved · %d pending · %d cancelled', $t['approved'], $t['pending'], $t['cancelled'] ) ),
			array( 'Booked hours', ZBR_Data::fmt_hours( $t['hours'] ), $t['full_day'] ? sprintf( '+ %d full-day booking(s)', $t['full_day'] ) : 'Total meeting time' ),
			array( 'Avg. duration', ZBR_Data::fmt_hours( $t['avg_hours'] ) . ' h', 'Per timed booking' ),
			array( 'Attendees', number_format_i18n( $t['persons'] ), 'Sum of "Number of Person"' ),
			array( 'Companies', number_format_i18n( $t['companies'] ), 'Distinct company names' ),
			array( 'Customers', number_format_i18n( $t['customers'] ), 'Distinct email addresses' ),
		);
		echo '<div class="zbr-kpis">';
		foreach ( $cards as $c ) {
			printf(
				'<div class="zbr-kpi"><div class="zbr-kpi-label">%s</div><div class="zbr-kpi-value">%s</div><div class="zbr-kpi-sub">%s</div></div>',
				esc_html( $c[0] ),
				esc_html( $c[1] ),
				esc_html( $c[2] )
			);
		}
		echo '</div>';
	}

	private static function render_breakdown( $title, array $rows, $label_heading, $limit = 0, $with_status = false, $full_width = false ) {
		$total_rows = count( $rows );
		if ( $limit > 0 ) {
			$rows = array_slice( $rows, 0, $limit );
		}
		$max = 0;
		foreach ( $rows as $r ) {
			$max = max( $max, $r['bookings'] );
		}
		?>
		<div class="zbr-card<?php echo $full_width ? ' zbr-card-wide' : ''; ?>">
			<h2><?php echo esc_html( $title ); ?></h2>
			<?php if ( empty( $rows ) ) : ?>
				<p class="zbr-muted">No data.</p>
			<?php else : ?>
			<table class="widefat striped zbr-breakdown">
				<thead><tr>
					<th><?php echo esc_html( $label_heading ); ?></th>
					<th class="num">Bookings</th>
					<?php if ( $with_status ) : ?><th class="num">Approved</th><th class="num">Pending</th><?php endif; ?>
					<th class="num">Hours</th><th class="num">Persons</th><th class="zbr-bar-col"></th>
				</tr></thead>
				<tbody>
				<?php foreach ( $rows as $r ) : ?>
					<tr>
						<td><?php echo esc_html( $r['label'] ); ?></td>
						<td class="num"><?php echo esc_html( number_format_i18n( $r['bookings'] ) ); ?></td>
						<?php if ( $with_status ) : ?>
							<td class="num"><?php echo esc_html( number_format_i18n( $r['approved'] ) ); ?></td>
							<td class="num"><?php echo esc_html( number_format_i18n( $r['pending'] ) ); ?></td>
						<?php endif; ?>
						<td class="num"><?php echo esc_html( ZBR_Data::fmt_hours( $r['hours'] ) ); ?></td>
						<td class="num"><?php echo esc_html( number_format_i18n( $r['persons'] ) ); ?></td>
						<td class="zbr-bar-col"><span class="zbr-bar" style="width:<?php echo esc_attr( $max ? round( $r['bookings'] / $max * 100 ) : 0 ); ?>%"></span></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
				<?php if ( $limit > 0 && $total_rows > $limit ) : ?>
					<p class="zbr-muted">Showing top <?php echo esc_html( $limit ); ?> of <?php echo esc_html( $total_rows ); ?>. The PDF and Summary CSV include all.</p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	private static function render_table( array $records, array $f ) {
		$total = count( $records );
		$pages = (int) ceil( $total / self::PER_PAGE );
		$paged = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$paged = min( $paged, max( 1, $pages ) );
		$rows  = array_slice( $records, ( $paged - 1 ) * self::PER_PAGE, self::PER_PAGE );

		$group  = $f['group'];
		$totals = $group ? ZBR_Report::group_totals( $records, $group ) : array();
		$cols   = 12;

		$listing_url = function_exists( 'wpbc_get_bookings_url' ) ? wpbc_get_bookings_url() : admin_url( 'admin.php?page=wpbc' );
		?>
		<div class="zbr-card zbr-card-wide">
			<h2>Booking details</h2>
			<div class="zbr-scroll">
			<table class="widefat zbr-details">
				<thead>
				<tr>
					<th>ID</th><th>Date</th><th>Time</th><th class="num">Hours</th><th>Name</th><th>Company</th><th>Contact</th><th class="num">Persons</th><th>Branch / room</th><th>Status</th><th>Approved by</th><th>Booked on</th>
				</tr>
				</thead>
				<tbody>
				<?php $current_group = null; ?>
				<?php foreach ( $rows as $r ) : ?>
					<?php
					if ( $group ) {
						$label = ZBR_Data::group_label( $r, $group );
						if ( $label !== $current_group ) {
							$current_group = $label;
							$gt            = $totals[ $label ];
							printf(
								'<tr class="zbr-group-row"><td colspan="%d"><strong>%s</strong> <span>%s</span></td></tr>',
								(int) $cols,
								esc_html( $label ),
								esc_html( sprintf( '%d booking(s) · %s hours · %d persons · %d approved · %d pending', $gt['bookings'], ZBR_Data::fmt_hours( $gt['hours'] ), $gt['persons'], $gt['approved'], $gt['pending'] ) )
							);
						}
					}
					$date = ZBR_Data::fmt_date( $r['date'], 'D, d M Y' );
					if ( $r['days'] > 1 ) {
						$date .= ' → ' . ZBR_Data::fmt_date( $r['date_end'], 'd M Y' );
					}
					$link = $listing_url . '&view_mode=vm_listing&tab=actions&wh_booking_id=' . $r['id'];
					?>
					<tr>
						<td><a href="<?php echo esc_url( $link ); ?>">#<?php echo esc_html( $r['id'] ); ?></a></td>
						<td><?php echo esc_html( $date ); ?></td>
						<td><?php echo esc_html( ZBR_Data::time_range( $r ) ); ?></td>
						<td class="num"><?php echo esc_html( ZBR_Data::fmt_hours( $r['hours'] ) ); ?></td>
						<td><?php echo esc_html( $r['name'] ); ?></td>
						<td><?php echo esc_html( $r['company'] ); ?></td>
						<td>
							<?php if ( '' !== $r['email'] ) : ?>
								<a href="<?php echo esc_url( 'mailto:' . $r['email'] ); ?>"><?php echo esc_html( $r['email'] ); ?></a><br>
							<?php endif; ?>
							<?php echo esc_html( $r['phone'] ); ?>
						</td>
						<td class="num"><?php echo esc_html( $r['persons'] ? $r['persons'] : '' ); ?></td>
						<td><?php echo esc_html( $r['branch'] ); ?><br><small><?php echo esc_html( $r['resource'] ); ?></small></td>
						<td><span class="zbr-status zbr-status-<?php echo esc_attr( $r['status'] ); ?>"><?php echo esc_html( ucfirst( $r['status'] ) ); ?></span></td>
						<td>
							<?php if ( 'cancelled' === $r['status'] && '' !== $r['cancelled_by'] ) : ?>
								<span class="zbr-muted-inline">Cancelled by</span> <?php echo esc_html( $r['cancelled_by'] ); ?><br><small><?php echo esc_html( ZBR_Data::fmt_datetime( $r['cancelled_at'] ) ); ?></small>
							<?php else : ?>
								<?php echo esc_html( $r['approved_by'] ); ?>
								<?php if ( '' !== $r['approved_at'] ) : ?><br><small><?php echo esc_html( ZBR_Data::fmt_datetime( $r['approved_at'] ) ); ?></small><?php endif; ?>
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( $r['created'] ? ZBR_Data::fmt_datetime( $r['created'] ) : '' ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			</div>
			<?php
			if ( $pages > 1 ) {
				$base = add_query_arg( rawurlencode_deep( array_merge( array( 'page' => self::SLUG ), ZBR_Data::query_args( $f ) ) ), admin_url( 'admin.php' ) );
				echo '<div class="tablenav"><div class="tablenav-pages">';
				echo wp_kses_post(
					paginate_links(
						array(
							'base'      => $base . '%_%',
							'format'    => '&paged=%#%',
							'current'   => $paged,
							'total'     => $pages,
							'prev_text' => '&laquo;',
							'next_text' => '&raquo;',
						)
					)
				);
				echo '</div></div>';
			}
			?>
		</div>
		<?php
	}
}
