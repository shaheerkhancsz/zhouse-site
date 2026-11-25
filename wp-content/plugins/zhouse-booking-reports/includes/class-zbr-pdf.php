<?php
/**
 * PDF report layout (A4 landscape) built on the bundled FPDF library.
 *
 * @package Zhouse_Booking_Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FPDF' ) ) {
	require_once ZBR_DIR . 'lib/fpdf/fpdf.php';
}

class ZBR_PDF extends FPDF {

	public $site_name    = '';
	public $report_title = 'Meeting Room Booking Report';
	public $period_label = '';
	public $filter_label = '';
	public $generated    = '';
	public $logo         = '';       // Local PNG/JPG path.
	public $header_style = 'dark';   // dark | light.

	/** Title of the section being printed, repeated as "(continued)" after a page break. */
	private $current_section = '';

	const FONT   = 'Helvetica';
	const MARGIN = 10;

	/** Brand colours (RGB). */
	const DARK   = array( 30, 41, 59 );
	const ACCENT = array( 37, 99, 235 );
	const MUTED  = array( 100, 116, 139 );
	const ZEBRA  = array( 245, 247, 250 );
	const LINE   = array( 226, 232, 240 );
	const GROUP  = array( 219, 234, 254 );

	const STATUS_COLORS = array(
		'approved'  => array( 22, 128, 61 ),
		'pending'   => array( 180, 83, 9 ),
		'cancelled' => array( 185, 28, 28 ),
	);

	/**
	 * Build the complete report.
	 */
	public static function make( array $report, array $records, array $f ) {
		$pdf = new self( 'L', 'mm', 'A4' );

		$pdf->site_name    = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$pdf->logo         = ZBR_Settings::logo_file();
		$pdf->header_style = ZBR_Settings::get()['header_style'];
		if ( '' !== $f['branch'] ) {
			$pdf->report_title = 'Meeting Room Booking Report - ' . ZBR_Data::branch_options()[ $f['branch'] ] . ' branch';
		}
		if ( '' !== $f['company'] && ! empty( $records ) ) {
			$pdf->report_title = 'Company Booking Report - ' . ( '' !== $records[0]['company'] ? $records[0]['company'] : ZBR_Data::NO_COMPANY );
		}
		$pdf->period_label = ZBR_Data::fmt_date( $f['from'] ) . ' - ' . ZBR_Data::fmt_date( $f['to'] );
		$pdf->filter_label = ZBR_Data::describe_filters( $f );
		$user              = wp_get_current_user();
		$pdf->generated    = ZBR_Data::fmt_datetime( wp_date( 'Y-m-d H:i:s' ) ) . ( $user && $user->exists() ? ' by ' . $user->display_name : '' );

		$pdf->SetTitle( $pdf->txt( $pdf->site_name . ' - ' . $pdf->report_title ) );
		$pdf->SetAuthor( $pdf->txt( $pdf->site_name ) );
		$pdf->SetCreator( 'Zhouse Booking Reports ' . ZBR_VERSION );
		$pdf->SetMargins( self::MARGIN, 30, self::MARGIN );
		$pdf->SetAutoPageBreak( true, 16 );
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$t = $report['totals'];
		$pdf->kpis(
			array(
				array( 'Bookings', number_format( $t['bookings'] ), self::status_line( $t ) ),
				array( 'Booked hours', ZBR_Data::fmt_hours( $t['hours'] ), $t['full_day'] ? '+ ' . $t['full_day'] . ' full-day booking(s)' : 'Total meeting time' ),
				array( 'Avg. duration', ZBR_Data::fmt_hours( $t['avg_hours'] ) . ' h', 'Per timed booking' ),
				array( 'Attendees', number_format( $t['persons'] ), 'Sum of persons' ),
				array( 'Companies', number_format( $t['companies'] ), 'Distinct companies' ),
				array( 'Customers', number_format( $t['customers'] ), 'Distinct emails' ),
			)
		);

		if ( empty( $records ) ) {
			$pdf->SetFont( self::FONT, '', 11 );
			$pdf->SetTextColor( ...self::MUTED );
			$pdf->Cell( 0, 10, 'No bookings found for the selected filters.', 0, 1, 'C' );
			return $pdf;
		}

		$sections = ZBR_Report::sections( $report );
		foreach ( array( 'by_branch', 'by_company', 'by_approver', 'by_room', 'by_period', 'by_weekday', 'by_hour' ) as $key ) {
			if ( empty( $report[ $key ] ) ) {
				continue;
			}
			if ( 'by_room' === $key && count( $report[ $key ] ) < 2 ) {
				continue;
			}
			if ( 'by_company' === $key && '' !== $f['company'] ) {
				continue;
			}
			$pdf->breakdown( $sections[ $key ], $report[ $key ], $t['bookings'], in_array( $key, array( 'by_branch', 'by_company' ), true ) );
		}

		$pdf->AddPage();
		$pdf->details( $records, $t, $f );

		return $pdf;
	}

	/* ------------------------------------------------------------------ */
	/* Page furniture                                                      */
	/* ------------------------------------------------------------------ */

	// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function Header() {
		$w     = $this->GetPageWidth();
		$light = ( 'light' === $this->header_style );

		if ( $light ) {
			$this->SetFillColor( 255, 255, 255 );
			$this->Rect( 0, 0, $w, 22, 'F' );
			$this->SetDrawColor( ...self::LINE );
			$this->Line( 0, 22, $w, 22 );
		} else {
			$this->SetFillColor( ...self::DARK );
			$this->Rect( 0, 0, $w, 22, 'F' );
		}
		$this->SetFillColor( ...self::ACCENT );
		$this->Rect( 0, 22, $w, 1.2, 'F' );

		// Logo on the left, text after it.
		$x = self::MARGIN;
		if ( '' !== $this->logo ) {
			$logo_w = $this->draw_logo( $x, 3, 16 );
			if ( $logo_w > 0 ) {
				$x += $logo_w + 4;
			}
		}
		$left_w = 175 - ( $x - self::MARGIN );

		$light ? $this->SetTextColor( 15, 23, 42 ) : $this->SetTextColor( 255, 255, 255 );
		$this->SetXY( $x, 5 );
		$this->SetFont( self::FONT, 'B', 15 );
		$this->Cell( $left_w, 7, $this->fit( $this->txt( $this->site_name ), $left_w ), 0, 0 );
		$this->SetXY( self::MARGIN + 175, 5 );
		$this->SetFont( self::FONT, 'B', 10 );
		$this->Cell( $w - 2 * self::MARGIN - 175, 7, $this->txt( $this->period_label ), 0, 1, 'R' );

		$this->SetX( $x );
		$this->SetFont( self::FONT, '', 9 );
		$light ? $this->SetTextColor( ...self::MUTED ) : $this->SetTextColor( 203, 213, 225 );
		$title_w = 130 - ( $x - self::MARGIN );
		$this->Cell( $title_w, 6, $this->fit( $this->txt( $this->report_title ), $title_w ), 0, 0 );
		$this->SetX( self::MARGIN + 130 );
		$this->SetFont( self::FONT, '', 7.5 );
		$this->Cell( $w - 2 * self::MARGIN - 130, 6, $this->fit( $this->txt( $this->filter_label ), $w - 2 * self::MARGIN - 130 ), 0, 1, 'R' );

		$this->SetTextColor( 15, 23, 42 );
		$this->SetY( 30 );
	}

	/**
	 * Draw the logo at a fixed height. Returns the width used (0 if the image could not be drawn).
	 */
	private function draw_logo( $x, $y, $h ) {
		$size = @getimagesize( $this->logo ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! $size || $size[1] <= 0 ) {
			$this->logo = '';
			return 0;
		}
		// Fixed height; very wide logos are capped at 60 mm wide and centred vertically.
		$w  = min( 60, $h * $size[0] / $size[1] );
		$hh = $w * $size[1] / $size[0];
		try {
			$this->Image( $this->logo, $x, $y + ( $h - $hh ) / 2, $w, $hh );
		} catch ( Exception $e ) {
			$this->logo = ''; // Unsupported image: carry on without a logo.
			return 0;
		}
		return $w;
	}

	// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function Footer() {
		$this->SetY( -12 );
		$this->SetDrawColor( ...self::LINE );
		$this->Line( self::MARGIN, $this->GetY(), $this->GetPageWidth() - self::MARGIN, $this->GetY() );
		$this->SetFont( self::FONT, '', 7.5 );
		$this->SetTextColor( ...self::MUTED );
		$this->SetX( self::MARGIN );
		$this->Cell( 150, 6, $this->txt( 'Generated ' . $this->generated ), 0, 0, 'L' );
		$this->SetX( self::MARGIN );
		$this->Cell( 0, 6, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'R' );
	}

	/* ------------------------------------------------------------------ */
	/* Blocks                                                              */
	/* ------------------------------------------------------------------ */

	public function kpis( array $items ) {
		$n   = count( $items );
		$gap = 4;
		$w   = ( $this->GetPageWidth() - 2 * self::MARGIN - $gap * ( $n - 1 ) ) / $n;
		$h   = 21;
		$x   = self::MARGIN;
		$y   = $this->GetY();

		foreach ( $items as $it ) {
			$this->SetFillColor( 241, 245, 249 );
			$this->SetDrawColor( ...self::LINE );
			$this->Rect( $x, $y, $w, $h, 'DF' );
			$this->SetFillColor( ...self::ACCENT );
			$this->Rect( $x, $y, 1.2, $h, 'F' );

			$this->SetXY( $x + 4, $y + 2.5 );
			$this->SetFont( self::FONT, 'B', 7 );
			$this->SetTextColor( ...self::MUTED );
			$this->Cell( $w - 6, 4, $this->txt( strtoupper( $it[0] ) ) );

			$this->SetXY( $x + 4, $y + 7 );
			$this->SetFont( self::FONT, 'B', 16 );
			$this->SetTextColor( 15, 23, 42 );
			$this->Cell( $w - 6, 8, $this->txt( $it[1] ) );

			if ( ! empty( $it[2] ) ) {
				$this->SetXY( $x + 4, $y + 15 );
				$this->SetFont( self::FONT, '', 6.5 );
				$this->SetTextColor( ...self::MUTED );
				$this->Cell( $w - 6, 4, $this->fit( $this->txt( $it[2] ), $w - 6 ) );
			}
			$x += $w + $gap;
		}
		$this->SetXY( self::MARGIN, $y + $h + 7 );
	}

	public function section_title( $title, $min_space = 30 ) {
		if ( $this->GetY() + $min_space > $this->PageBreakTrigger ) {
			$this->AddPage( $this->CurOrientation );
		}
		$this->current_section = $title;
		$this->SetFont( self::FONT, 'B', 11 );
		$this->SetTextColor( 15, 23, 42 );
		$this->Cell( 0, 7, $this->txt( $title ), 0, 1 );
	}

	public function breakdown( $title, array $rows, $total_bookings, $with_status = false ) {
		$this->section_title( $title );
		$max = 0;
		foreach ( $rows as $r ) {
			$max = max( $max, $r['bookings'] );
		}

		$cols = array( array( 'label' => 'Name', 'w' => 95, 'flex' => true ) );
		$cols[] = array( 'label' => 'Bookings', 'w' => 22, 'align' => 'R' );
		if ( $with_status ) {
			$cols[] = array( 'label' => 'Approved', 'w' => 22, 'align' => 'R' );
			$cols[] = array( 'label' => 'Pending', 'w' => 22, 'align' => 'R' );
		}
		$cols[] = array( 'label' => 'Hours', 'w' => 22, 'align' => 'R' );
		$cols[] = array( 'label' => 'Persons', 'w' => 22, 'align' => 'R' );
		$cols[] = array( 'label' => 'Share', 'w' => 18, 'align' => 'R' );
		$cols[] = array( 'label' => '', 'w' => 54, 'bar' => true );
		$cols   = $this->fit_columns( $cols );

		$data = array();
		foreach ( $rows as $r ) {
			$row = array( $r['label'], number_format( $r['bookings'] ) );
			if ( $with_status ) {
				$row[] = number_format( $r['approved'] );
				$row[] = number_format( $r['pending'] );
			}
			$row[]  = ZBR_Data::fmt_hours( $r['hours'] );
			$row[]  = number_format( $r['persons'] );
			$row[]  = $total_bookings ? round( $r['bookings'] / $total_bookings * 100, 1 ) . '%' : '';
			$row[]  = $max ? $r['bookings'] / $max : 0;
			$data[] = $row;
		}
		$this->table( $cols, $data );
	}

	public function details( array $records, array $t, array $f ) {
		$this->section_title( 'Booking details (' . count( $records ) . ')' );

		$group       = $f['group'];
		$show_branch = '' === $f['branch'] && 'branch' !== $group;
		$show_comp   = '' === $f['company'] && 'company' !== $group;

		$spec = array(
			'id'       => array( 'label' => 'ID', 'w' => 12 ),
			'date'     => array( 'label' => 'Date', 'w' => 25 ),
			'time'     => array( 'label' => 'Time', 'w' => 31 ),
			'hours'    => array( 'label' => 'Hrs', 'w' => 10, 'align' => 'R' ),
			'name'     => array( 'label' => 'Name', 'w' => 30, 'flex' => true ),
			'company'  => array( 'label' => 'Company', 'w' => 34, 'flex' => true ),
			'phone'    => array( 'label' => 'Phone', 'w' => 23 ),
			'persons'  => array( 'label' => 'Pers.', 'w' => 10, 'align' => 'R' ),
			'branch'   => array( 'label' => 'Branch', 'w' => 24, 'flex' => true ),
			'room'     => array( 'label' => 'Room', 'w' => 26, 'flex' => true ),
			'status'   => array( 'label' => 'Status', 'w' => 16 ),
			'approver' => array( 'label' => 'Approved by', 'w' => 30, 'flex' => true ),
			'when'     => array( 'label' => 'On', 'w' => 27 ),
		);
		if ( ! $show_branch ) {
			unset( $spec['branch'] );
		}
		if ( ! $show_comp ) {
			unset( $spec['company'] );
		}
		// Room column only when the bookings span more than one room (e.g. Clifton's three rooms).
		if ( $f['resource'] || count( array_unique( wp_list_pluck( $records, 'resource_id' ) ) ) < 2 ) {
			unset( $spec['room'] );
		}
		$keys = array_keys( $spec );
		$cols = $this->fit_columns( array_values( $spec ) );

		$totals = $group ? ZBR_Report::group_totals( $records, $group ) : array();
		$data   = array();
		$last   = null;

		foreach ( $records as $r ) {
			if ( $group ) {
				$label = ZBR_Data::group_label( $r, $group );
				if ( $label !== $last ) {
					$last   = $label;
					$gt     = $totals[ $label ];
					$data[] = array(
						'_group' => $label,
						'_meta'  => sprintf( '%d booking(s)  |  %s hours  |  %d persons  |  %d approved  |  %d pending', $gt['bookings'], ZBR_Data::fmt_hours( $gt['hours'] ), $gt['persons'], $gt['approved'], $gt['pending'] ),
					);
				}
			}

			$date = ( $r['days'] > 1 )
				? ZBR_Data::fmt_date( $r['date'], 'd M Y' ) . ' +' . ( $r['days'] - 1 ) . 'd'
				: ZBR_Data::fmt_date( $r['date'], 'd M Y' );

			if ( 'cancelled' === $r['status'] && '' !== $r['cancelled_by'] ) {
				$who  = 'Cancelled: ' . $r['cancelled_by'];
				$when = ZBR_Data::fmt_datetime( $r['cancelled_at'], 'd M y' );
			} else {
				$who  = $r['approved_by'];
				$when = ZBR_Data::fmt_datetime( $r['approved_at'], 'd M y' );
			}

			$cells = array(
				'id'       => '#' . $r['id'],
				'date'     => $date,
				'time'     => ZBR_Data::time_range( $r ),
				'hours'    => ZBR_Data::fmt_hours( $r['hours'] ),
				'name'     => $r['name'],
				'company'  => $r['company'],
				'phone'    => $r['phone'],
				'persons'  => $r['persons'] ? (string) $r['persons'] : '',
				'branch'   => $r['branch'],
				'room'     => $r['resource'],
				'status'   => array(
					'text'  => ucfirst( $r['status'] ),
					'color' => self::STATUS_COLORS[ $r['status'] ],
					'bold'  => true,
				),
				'approver' => 'Not recorded' === $who ? array( 'text' => $who, 'color' => self::MUTED ) : $who,
				'when'     => $when,
			);
			$row = array();
			foreach ( $keys as $k ) {
				$row[] = $cells[ $k ];
			}
			$data[] = $row;
		}

		$footer                                   = array_fill( 0, count( $keys ), '' );
		$footer[ array_search( 'id', $keys, true ) ]      = 'Total';
		$footer[ array_search( 'date', $keys, true ) ]    = number_format( count( $records ) ) . ' booking(s)';
		$footer[ array_search( 'hours', $keys, true ) ]   = ZBR_Data::fmt_hours( $t['hours'] );
		$footer[ array_search( 'persons', $keys, true ) ] = number_format( $t['persons'] );

		$this->table( $cols, $data, $footer, 7.5 );
	}

	/* ------------------------------------------------------------------ */
	/* Table engine                                                        */
	/* ------------------------------------------------------------------ */

	/** Stretch 'flex' columns so the table fills the printable width exactly. */
	private function fit_columns( array $cols ) {
		$avail = $this->GetPageWidth() - 2 * self::MARGIN;
		$used  = 0;
		$flex  = 0;
		foreach ( $cols as $c ) {
			$used += $c['w'];
			$flex += empty( $c['flex'] ) ? 0 : $c['w'];
		}
		if ( $flex > 0 ) {
			$ratio = ( $avail - ( $used - $flex ) ) / $flex;
			foreach ( $cols as $i => $c ) {
				if ( ! empty( $c['flex'] ) ) {
					$cols[ $i ]['w'] = $c['w'] * $ratio;
				}
			}
		}
		return $cols;
	}

	private function table_head( array $cols, $size ) {
		$this->SetFont( self::FONT, 'B', $size );
		$this->SetFillColor( ...self::DARK );
		$this->SetTextColor( 255, 255, 255 );
		foreach ( $cols as $c ) {
			$this->Cell( $c['w'], 7, $this->fit( $this->txt( $c['label'] ), $c['w'] - 1 ), 0, 0, isset( $c['align'] ) ? $c['align'] : 'L', true );
		}
		$this->Ln();
	}

	/** Start a new page in the middle of a table: repeat the section title and column headings. */
	private function continue_table( array $cols, $size ) {
		$this->AddPage( $this->CurOrientation );
		if ( '' !== $this->current_section ) {
			$this->SetFont( self::FONT, 'B', 11 );
			$this->SetTextColor( 15, 23, 42 );
			$this->Cell( 0, 7, $this->txt( $this->current_section . ' (continued)' ), 0, 1 );
		}
		$this->table_head( $cols, $size );
	}

	/**
	 * @param array      $cols   [ [ 'label', 'w', 'align'?, 'bar'? ] ]
	 * @param array      $rows   Row arrays aligned with $cols. A cell may be [ 'text', 'color', 'bold' ].
	 *                           A row [ '_group' => label, '_meta' => text ] prints a group heading bar.
	 * @param array|null $footer Optional bold total row.
	 * @param float      $size   Font size.
	 */
	public function table( array $cols, array $rows, $footer = null, $size = 8 ) {
		$lh    = 6;
		$width = 0;
		foreach ( $cols as $c ) {
			$width += $c['w'];
		}
		$this->table_head( $cols, $size );
		$i = 0;

		foreach ( $rows as $row ) {
			$is_group = isset( $row['_group'] );
			$need     = $is_group ? $lh + 1 + $lh : $lh; // Keep a group heading with its first row.
			if ( $this->GetY() + $need > $this->PageBreakTrigger ) {
				$this->continue_table( $cols, $size );
			}

			if ( $is_group ) {
				$this->SetFillColor( ...self::GROUP );
				$this->SetTextColor( 15, 23, 42 );
				$this->SetFont( self::FONT, 'B', $size + 1 );
				$label_w = min( 90, $this->GetStringWidth( $this->txt( $row['_group'] ) ) + 6 );
				$this->Cell( $label_w, $lh + 1, $this->fit( $this->txt( $row['_group'] ), $label_w - 2 ), 0, 0, 'L', true );
				$this->SetFont( self::FONT, '', $size );
				$this->SetTextColor( ...self::MUTED );
				$this->Cell( $width - $label_w, $lh + 1, $this->fit( $this->txt( $row['_meta'] ), $width - $label_w - 2 ), 0, 1, 'R', true );
				$i = 0;
				continue;
			}

			$fill = ( 1 === $i++ % 2 );
			$this->SetFillColor( ...self::ZEBRA );

			foreach ( $cols as $k => $c ) {
				$value = isset( $row[ $k ] ) ? $row[ $k ] : '';
				$align = isset( $c['align'] ) ? $c['align'] : 'L';

				if ( ! empty( $c['bar'] ) ) {
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Cell( $c['w'], $lh, '', 0, 0, 'L', $fill );
					$bar = max( 0.5, ( $c['w'] - 4 ) * (float) $value );
					$this->SetFillColor( ...self::ACCENT );
					$this->Rect( $x + 2, $y + 1.8, $bar, $lh - 3.6, 'F' );
					$this->SetFillColor( ...self::ZEBRA );
					continue;
				}

				$bold = false;
				$this->SetTextColor( 30, 41, 59 );
				if ( is_array( $value ) ) {
					$bold = ! empty( $value['bold'] );
					if ( ! empty( $value['color'] ) ) {
						$this->SetTextColor( ...$value['color'] );
					}
					$value = $value['text'];
				}
				$this->SetFont( self::FONT, $bold ? 'B' : '', $size );
				$this->Cell( $c['w'], $lh, $this->fit( $this->txt( $value ), $c['w'] - 1.5 ), 0, 0, $align, $fill );
			}
			$this->Ln();
		}

		if ( $footer ) {
			if ( $this->GetY() + $lh > $this->PageBreakTrigger ) {
				$this->AddPage( $this->CurOrientation );
			}
			$this->SetDrawColor( ...self::DARK );
			$this->SetFont( self::FONT, 'B', $size );
			$this->SetTextColor( 15, 23, 42 );
			foreach ( $cols as $k => $c ) {
				$this->Cell( $c['w'], $lh + 1, $this->txt( isset( $footer[ $k ] ) ? $footer[ $k ] : '' ), 'T', 0, isset( $c['align'] ) ? $c['align'] : 'L' );
			}
			$this->Ln();
		}
		$this->Ln( 6 );
	}

	/* ------------------------------------------------------------------ */
	/* Text helpers                                                        */
	/* ------------------------------------------------------------------ */

	/** "71 approved / 2 pending" - zero counts left out so it fits the card. */
	private static function status_line( array $t ) {
		$parts = array();
		foreach ( array( 'approved', 'pending', 'cancelled' ) as $s ) {
			if ( $t[ $s ] ) {
				$parts[] = $t[ $s ] . ' ' . $s;
			}
		}
		return implode( ' / ', $parts );
	}

	/** UTF-8 -> Windows-1252 for the built-in PDF fonts. */
	public function txt( $s ) {
		$s = (string) $s;
		if ( '' === $s || ! preg_match( '/[^\x00-\x7F]/', $s ) ) {
			return $s;
		}
		if ( function_exists( 'iconv' ) ) {
			$c = @iconv( 'UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $s ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( false !== $c ) {
				return $c;
			}
		}
		if ( function_exists( 'mb_convert_encoding' ) ) {
			return mb_convert_encoding( $s, 'Windows-1252', 'UTF-8' );
		}
		return preg_replace( '/[^\x20-\x7E]/', '?', $s );
	}

	/** Truncate (already-encoded) text to fit a width, adding "...". */
	public function fit( $s, $w ) {
		if ( $this->GetStringWidth( $s ) <= $w ) {
			return $s;
		}
		while ( '' !== $s && $this->GetStringWidth( $s . '...' ) > $w ) {
			$s = substr( $s, 0, -1 );
		}
		return $s . '...';
	}
}
