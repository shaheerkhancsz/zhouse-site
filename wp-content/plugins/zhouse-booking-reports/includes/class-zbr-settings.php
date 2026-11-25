<?php
/**
 * Report settings: branches, which booking calendar (resource) belongs to which branch,
 * and the logo / header style used on the PDF report.
 *
 * @package Zhouse_Booking_Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZBR_Settings {

	const OPTION = 'zbr_settings';

	private static $cache = null;

	public static function init() {
		add_action( 'admin_post_zbr_save_settings', array( __CLASS__, 'save' ) );
	}

	/**
	 * Defaults match the Z Houses website (z-houses.com/all-locations and /meetings):
	 *   Z House – Clifton:          Z Meeting Room (1), Z Conference Room (5), Recreation Room (8)
	 *   Z Signature – DHA:          Z Signature (6)
	 *   Z Collective – Tariq Road:  Z Collective – Tariq Road (7)
	 */
	public static function defaults() {
		return array(
			'branches'        => array(
				'b1' => 'Z House – Clifton',
				'b2' => 'Z Signature – DHA',
				'b3' => 'Z Collective – Tariq Road',
			),
			'resource_branch' => array(
				1 => 'b1',
				5 => 'b1',
				8 => 'b1',
				6 => 'b2',
				7 => 'b3',
			),
			'branch_field'    => 'branch',      // Optional booking-form field holding the branch name.
			'next_id'         => 4,
			'logo_id'         => 0,             // 0 = website logo automatically, -1 = no logo, >0 = media attachment.
			'header_style'    => 'dark',        // dark (for white logos) | light (for dark logos).
		);
	}

	public static function get() {
		if ( null === self::$cache ) {
			$saved = get_option( self::OPTION );
			$saved = is_array( $saved ) ? $saved : array();

			// Settings saved by v1.1.0 before branches were set up: use the new defaults.
			if ( isset( $saved['branches'] ) && array( 'b1' => 'Clifton', 'b2' => 'DHA' ) === $saved['branches'] && empty( $saved['resource_branch'] ) ) {
				unset( $saved['branches'], $saved['resource_branch'], $saved['next_id'] );
			}
			self::$cache = array_merge( self::defaults(), $saved );
		}
		return self::$cache;
	}

	/** @return array id => name, in display order. */
	public static function branches() {
		return self::get()['branches'];
	}

	/* ------------------------------------------------------------------ */
	/* Logo                                                                */
	/* ------------------------------------------------------------------ */

	/** Attachment ID of the logo to use (chosen one, or the website's logo / site icon). */
	public static function logo_attachment_id() {
		$id = (int) self::get()['logo_id'];
		if ( $id < 0 ) {
			return 0;
		}
		if ( 0 === $id ) {
			$id = (int) get_theme_mod( 'custom_logo' );
		}
		if ( ! $id ) {
			$id = (int) get_option( 'site_icon' );
		}
		return $id;
	}

	/**
	 * Local PNG/JPG path of the logo for the PDF, resized and cached in uploads/zbr-cache.
	 * Returns '' when there is no usable logo (e.g. SVG).
	 */
	public static function logo_file() {
		$id = self::logo_attachment_id();
		if ( ! $id ) {
			return '';
		}
		$file = get_attached_file( $id );
		if ( ! $file || ! file_exists( $file ) ) {
			return '';
		}
		$ext = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

		$upload = wp_upload_dir();
		if ( empty( $upload['error'] ) ) {
			$dir  = trailingslashit( $upload['basedir'] ) . 'zbr-cache';
			$dest = $dir . '/logo-' . $id . '-' . filemtime( $file ) . '.png';
			if ( file_exists( $dest ) ) {
				return $dest;
			}
			if ( wp_mkdir_p( $dir ) ) {
				$editor = wp_get_image_editor( $file );
				if ( ! is_wp_error( $editor ) ) {
					$size = $editor->get_size();
					if ( $size['width'] > 600 || $size['height'] > 240 ) {
						$editor->resize( 600, 240, false );
					}
					$saved = $editor->save( $dest, 'image/png' );
					if ( ! is_wp_error( $saved ) && ! empty( $saved['path'] ) ) {
						return $saved['path'];
					}
				}
			}
		}
		return in_array( $ext, array( 'png', 'jpg', 'jpeg' ), true ) ? $file : '';
	}

	/* ------------------------------------------------------------------ */
	/* Save                                                                */
	/* ------------------------------------------------------------------ */

	public static function save() {
		if ( ! current_user_can( ZBR_Data::capability() ) ) {
			wp_die( esc_html__( 'You do not have permission to change report settings.', 'zhouse-booking-reports' ), 403 );
		}
		check_admin_referer( 'zbr_save_settings' );

		$in       = wp_unslash( $_POST );
		$current  = self::get();
		$branches = array();

		foreach ( (array) ( isset( $in['branch'] ) ? $in['branch'] : array() ) as $id => $name ) {
			$id   = sanitize_key( $id );
			$name = sanitize_text_field( $name );
			if ( '' !== $name && preg_match( '/^b\d+$/', $id ) ) {
				$branches[ $id ] = $name;
			}
		}

		$next = max( 1, (int) $current['next_id'] );
		foreach ( (array) ( isset( $in['branch_new'] ) ? $in['branch_new'] : array() ) as $name ) {
			$name = sanitize_text_field( $name );
			if ( '' === $name ) {
				continue;
			}
			while ( isset( $branches[ 'b' . $next ] ) || isset( $current['branches'][ 'b' . $next ] ) ) {
				$next++;
			}
			$branches[ 'b' . $next ] = $name;
			$next++;
		}

		$map = array();
		foreach ( (array) ( isset( $in['resource_branch'] ) ? $in['resource_branch'] : array() ) as $rid => $bid ) {
			$bid = sanitize_key( $bid );
			if ( isset( $branches[ $bid ] ) ) {
				$map[ absint( $rid ) ] = $bid;
			}
		}

		$field   = isset( $in['branch_field'] ) ? sanitize_key( $in['branch_field'] ) : '';
		$logo_id = isset( $in['logo_id'] ) ? (int) $in['logo_id'] : 0;
		if ( $logo_id > 0 && ! wp_attachment_is_image( $logo_id ) ) {
			$logo_id = 0;
		}
		$style = ( isset( $in['header_style'] ) && 'light' === $in['header_style'] ) ? 'light' : 'dark';

		update_option(
			self::OPTION,
			array(
				'branches'        => $branches,
				'resource_branch' => $map,
				'branch_field'    => '' !== $field ? $field : 'branch',
				'next_id'         => $next,
				'logo_id'         => max( -1, $logo_id ),
				'header_style'    => $style,
			),
			false
		);
		self::$cache = null;

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => ZBR_Admin::SLUG,
					'tab'     => 'settings',
					'updated' => 1,
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/* ------------------------------------------------------------------ */
	/* Page                                                                */
	/* ------------------------------------------------------------------ */

	public static function render() {
		$s         = self::get();
		$resources = ZBR_Data::get_resources();
		$logo_id   = self::logo_attachment_id();
		$logo_url  = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
		$logo_ok   = $logo_id ? '' !== self::logo_file() : false;
		?>
		<?php if ( ! empty( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
			<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="zbr-settings">
			<input type="hidden" name="action" value="zbr_save_settings">
			<?php wp_nonce_field( 'zbr_save_settings' ); ?>

			<div class="zbr-card">
				<h2>Branches</h2>
				<p class="zbr-muted">The names used in reports. Clear a name to remove that branch.</p>
				<table class="form-table" role="presentation">
					<?php $i = 0; ?>
					<?php foreach ( $s['branches'] as $id => $name ) : ?>
						<?php $i++; ?>
						<tr>
							<th scope="row"><label for="zbr-branch-<?php echo esc_attr( $id ); ?>">Branch <?php echo esc_html( $i ); ?></label></th>
							<td><input type="text" class="regular-text" id="zbr-branch-<?php echo esc_attr( $id ); ?>" name="branch[<?php echo esc_attr( $id ); ?>]" value="<?php echo esc_attr( $name ); ?>"></td>
						</tr>
					<?php endforeach; ?>
					<?php for ( $n = 0; $n < 2; $n++ ) : ?>
						<?php $i++; ?>
						<tr>
							<th scope="row"><label for="zbr-branch-new-<?php echo esc_attr( $n ); ?>">Branch <?php echo esc_html( $i ); ?></label></th>
							<td><input type="text" class="regular-text" id="zbr-branch-new-<?php echo esc_attr( $n ); ?>" name="branch_new[]" value="" placeholder="New branch name"></td>
						</tr>
					<?php endfor; ?>
				</table>
			</div>

			<div class="zbr-card">
				<h2>Which branch is each booking calendar in?</h2>
				<p class="zbr-muted">
					Each meeting room is a separate calendar in Booking Calendar (<em>Booking &rsaquo; Resources</em>).
					Choose the branch for each one. Bookings in a calendar without a branch show as "Unassigned".
				</p>
				<table class="widefat striped zbr-map">
					<thead><tr><th>Booking calendar (room)</th><th>ID</th><th>Branch</th></tr></thead>
					<tbody>
					<?php foreach ( $resources as $rid => $title ) : ?>
						<tr>
							<td><label for="zbr-res-<?php echo esc_attr( $rid ); ?>"><?php echo esc_html( $title ); ?></label></td>
							<td><?php echo esc_html( $rid ); ?></td>
							<td>
								<select id="zbr-res-<?php echo esc_attr( $rid ); ?>" name="resource_branch[<?php echo esc_attr( $rid ); ?>]">
									<option value="">&mdash; Not assigned &mdash;</option>
									<?php foreach ( $s['branches'] as $id => $name ) : ?>
										<option value="<?php echo esc_attr( $id ); ?>" <?php selected( isset( $s['resource_branch'][ $rid ] ) ? $s['resource_branch'][ $rid ] : '', $id ); ?>><?php echo esc_html( $name ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<p class="zbr-muted">Save once after adding new branch names, then assign them here.</p>
			</div>

			<div class="zbr-card">
				<h2>PDF logo</h2>
				<p class="zbr-muted">Shown in the header of every PDF report. By default the website's own logo is used. PNG or JPG work best; SVG cannot be used in PDFs.</p>
				<input type="hidden" name="logo_id" id="zbr-logo-id" value="<?php echo esc_attr( (int) $s['logo_id'] ); ?>">
				<div class="zbr-logo-row">
					<div class="zbr-logo-preview zbr-logo-<?php echo esc_attr( $s['header_style'] ); ?>" id="zbr-logo-preview">
						<?php if ( $logo_url ) : ?>
							<img src="<?php echo esc_url( $logo_url ); ?>" alt="Current report logo">
						<?php else : ?>
							<span>No logo</span>
						<?php endif; ?>
					</div>
					<div class="zbr-logo-actions">
						<button type="button" class="button" id="zbr-logo-pick">Choose from Media Library</button>
						<button type="button" class="button-link" id="zbr-logo-auto">Use website logo</button>
						<button type="button" class="button-link zbr-danger" id="zbr-logo-none">No logo</button>
						<p class="zbr-muted" id="zbr-logo-status">
							<?php
							if ( (int) $s['logo_id'] < 0 ) {
								echo 'The PDF header shows the business name only.';
							} elseif ( 0 === (int) $s['logo_id'] ) {
								echo $logo_id ? 'Using the website logo.' : 'No website logo found. Please choose one.';
							} else {
								echo 'Using the logo chosen from the Media Library.';
							}
							if ( $logo_id && ! $logo_ok ) {
								echo ' <strong>This image type cannot be used in PDFs. Please choose a PNG or JPG.</strong>';
							}
							?>
						</p>
					</div>
				</div>
				<p>
					<label for="zbr-header-style"><strong>PDF header colour</strong></label><br>
					<select id="zbr-header-style" name="header_style">
						<option value="dark" <?php selected( $s['header_style'], 'dark' ); ?>>Dark (for a white logo)</option>
						<option value="light" <?php selected( $s['header_style'], 'light' ); ?>>White (for a dark logo)</option>
					</select>
				</p>
			</div>

			<div class="zbr-card">
				<h2>Branch field in the booking form (optional)</h2>
				<p class="zbr-muted">
					If the booking form asks customers to choose a branch, enter that field's name here (for example <code>branch</code> for
					<code>[selectbox branch "Clifton" "DHA"]</code>). The chosen branch then takes priority over the calendar setting above.
					A field named <code>location</code> is also recognised.
				</p>
				<p><label for="zbr-branch-field">Field name </label><input type="text" id="zbr-branch-field" name="branch_field" value="<?php echo esc_attr( $s['branch_field'] ); ?>"></p>
			</div>

			<?php submit_button( 'Save settings' ); ?>
		</form>
		<?php
	}

	/** Media Library picker for the logo (settings tab only). */
	public static function enqueue_picker() {
		wp_enqueue_media();
		wp_add_inline_script(
			'zbr-admin',
			"(function(){var id=document.getElementById('zbr-logo-id'),pv=document.getElementById('zbr-logo-preview'),st=document.getElementById('zbr-logo-status'),frame;if(!id)return;
function show(url,msg){pv.innerHTML=url?'<img src=\"'+url.replace(/\"/g,'&quot;')+'\" alt=\"Selected logo\">':'<span>'+(msg||'No logo')+'</span>';}
document.getElementById('zbr-logo-pick').addEventListener('click',function(){if(!window.wp||!wp.media)return;if(!frame){frame=wp.media({title:'Choose the report logo',button:{text:'Use this logo'},library:{type:'image'},multiple:false});frame.on('select',function(){var a=frame.state().get('selection').first().toJSON();id.value=a.id;var u=(a.sizes&&a.sizes.medium)?a.sizes.medium.url:a.url;show(u);st.textContent=(/\\.svg$/i.test(a.url)?'SVG cannot be used in PDFs. Please choose a PNG or JPG. ':'')+'Click Save settings to keep this logo.';});}frame.open();});
document.getElementById('zbr-logo-auto').addEventListener('click',function(){id.value=0;show('','Website logo');st.textContent='The website logo will be used. Click Save settings.';});
document.getElementById('zbr-logo-none').addEventListener('click',function(){id.value=-1;show('','No logo');st.textContent='The PDF header will show the business name only. Click Save settings.';});
var hs=document.getElementById('zbr-header-style');if(hs){hs.addEventListener('change',function(){pv.className='zbr-logo-preview zbr-logo-'+hs.value;});}
})();"
		);
	}
}
