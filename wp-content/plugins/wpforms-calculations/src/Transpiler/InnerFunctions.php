<?php

namespace WPFormsCalculations\Transpiler;

/**
 * Inner functions class.
 *
 * @since 1.0.0
 */
class InnerFunctions {

	/**
	 * Fewest decimals a product may be rounded to.
	 *
	 * @since 1.13.0
	 */
	private const MIN_PRODUCT_DECIMALS = 2;

	/**
	 * Scaled value from which every float is an integer, so there is nothing left to round.
	 *
	 * @since 1.13.0
	 */
	private const ROUNDING_LIMIT = 2 ** 53;

	/**
	 * Convert the given string to a number.
	 *
	 * It finds the first number OR formatted money amount with the current currency symbol
	 * in the given string and converts it to the decimal number value.
	 *
	 * Examples:
	 *   The current currency is USD:
	 *     - "123,123.45"                   -> 123123.45
	 *     - "-10 is a -1 * 10"             -> -10
	 *     - "price is $1,000.00"           -> 1000
	 *     - "Your balance is -$2,555.99"   -> -2555.99
	 *
	 *   The current currency is EUR:
	 *     - "Ticket to Mars 1.000.000,99€" -> 1000000.99
	 *     - "1000,00€ and $50.00"          -> 1000
	 *     - "Negative amount -100€"        -> -100
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $str       String to convert.
	 * @param int   $precision Precision.
	 *
	 * @return float|string Floating point number value OR empty string.
	 */
	public function parse_float( $str, $precision = 12 ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		// If the given value is a zero, return zero.
		if ( in_array( $str, [ 0, 0.0, '0', '0.0' ], true ) ) {
			return (int) $str;
		}

		if ( empty( $str ) ) {
			return '';
		}

		$precision = $precision || $precision === 0 ? (int) $precision : 12;

		// If the given value is already a numeric, just round it.
		if ( is_numeric( $str ) ) {
			return $this->round( $str, $precision );
		}

		$default_currency = [
			'symbol'              => '&#36;',
			'symbol_pos'          => 'left',
			'thousands_separator' => ',',
			'decimal_separator'   => '.',
			'decimals'            => 2,
		];

		// Get currency data.
		$currency_code = strtoupper( wpforms_get_currency() );
		$currencies    = wpforms_get_currencies();
		$currency      = $currencies[ $currency_code ] ?? $default_currency;
		$currency      = wp_parse_args( $currency, $default_currency );

		// Prepare regexp pattern chunks.
		$symbol       = html_entity_decode( $currency['symbol'] ?? '$' );
		$str          = str_replace( $currency['symbol'], $symbol, (string) $str );
		$slash_symbol = str_replace( '$', '\$', $symbol );
		$left_symbol  = $currency['symbol_pos'] === 'left' ? $slash_symbol . '[ ]?' : '';
		$right_symbol = $currency['symbol_pos'] === 'right' ? '[ ]?' . $slash_symbol : '';
		$matches      = [];

		// Prepare regex pattern.
		$amount_pattern =
			// Match amount with currency symbol, decimal and thousands separator.
			"#(-?$left_symbol(\\d*)([{$currency['thousands_separator']}]?\\d{3})*([{$currency['decimal_separator']}]\\d*)?($right_symbol))" .
			// OR match regular number values with a standard decimal and thousands separator.
			'|(-?(\\d+)(,?\\d{3})*([.]?\\d*)?)#';

		preg_match_all( $amount_pattern, $str, $matches );

		if ( empty( $matches[0] ) || ! is_array( $matches[0] ) ) {
			return '';
		}

		// Detect first not empty match. A plain array_filter() would also drop a legitimate "0"
		// match, which the front end keeps.
		$matches[0] = array_filter( $matches[0], 'strlen' );
		$found      = reset( $matches[0] );

		// If the number OR amount is not found, return empty string.
		if ( $found === false ) {
			return '';
		}

		if ( strpos( $found, $symbol ) !== false ) {
			return wpforms_sanitize_amount( $found );
		}

		// Remove a thousand separator and space.
		// At this point, we should have only a numeric value with decimal point.
		$found = str_replace( [ ',', ' ' ], '', $found );

		return $this->round( $found, $precision );
	}

	/**
	 * Convert the given string to a floating point number.
	 *
	 * It is a wrapper for the `parse_float` method, but it always returns a float value.
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $str       String to convert.
	 * @param int   $precision Precision.
	 *
	 * @return float Floating point number.
	 */
	public function to_float( $str, $precision = 12 ): float {

		return (float) $this->parse_float( $str, $precision );
	}

	/**
	 * Rounds a float.
	 *
	 * @since 1.1.0
	 *
	 * @param int|float|string $num       The value to round.
	 * @param int              $precision Precision.
	 *
	 * @return float Floating point number.
	 */
	public function round( $num, $precision = 12 ): float {

		$num       = (float) $num;
		$precision = (int) $precision;

		if ( ! is_finite( $num ) || $num === 0.0 ) {
			return $num;
		}

		// Shifting such a number would only perturb its last digit; the browser keeps it as it is too.
		if ( abs( $num ) * 10 ** $precision >= self::ROUNDING_LIMIT ) {
			return $num;
		}

		// The browser shifts the decimal point in the number's shortest spelling, rounds halves towards positive
		// infinity and shifts back. PHP's round() pre-rounds and treats halves differently, so it is mirrored here.
		$shifted = $this->shift_decimal_point( $num, $precision );
		$rounded = floor( $shifted );

		if ( $shifted - $rounded >= 0.5 ) {
			++$rounded;
		}

		return $this->shift_decimal_point( $rounded, -$precision );
	}

	/**
	 * Move the decimal point of a float by the given number of places, keeping its shortest digits exactly.
	 *
	 * @since 1.13.0
	 *
	 * @param float $num    Number.
	 * @param int   $places Places to move the point to the right, negative to the left.
	 *
	 * @return float Shifted number.
	 */
	private function shift_decimal_point( float $num, int $places ): float {

		if ( $num === 0.0 ) {
			return 0.0;
		}

		[ $sign, $digits, $point ] = $this->get_shortest_digits( $num );

		return (float) sprintf( '%s%s.%se%d', $sign, $digits[0], substr( $digits, 1 ) . '0', $point - 1 + $places );
	}

	/**
	 * Get the number precision.
	 *
	 * @since 1.0.0
	 *
	 * @param int|float $num Number value.
	 *
	 * @return int Number precision.
	 */
	public function get_precision( $num ): int {

		if ( ! is_numeric( $num ) ) {
			return 0;
		}

		// A float is counted in the spelling the browser uses, so both sides round the same operands alike.
		$chunks = explode( '.', $this->to_plain_decimal( (float) $num ) );

		return strlen( $chunks[1] ?? '' );
	}

	/**
	 * Write a float as a plain decimal, the way the browser prints a number, never in exponent notation.
	 *
	 * The shortest decimal that reads back to the same float is used, which is what JavaScript's
	 * String( number ) prints, while PHP's own conversion stops at 14 significant digits.
	 *
	 * @since 1.13.0
	 *
	 * @param float $num Number.
	 *
	 * @return string Plain decimal.
	 */
	public function to_plain_decimal( float $num ): string {

		if ( ! is_finite( $num ) ) {
			return (string) $num;
		}

		if ( $num === 0.0 ) {
			return '0';
		}

		[ $sign, $digits, $point ] = $this->get_shortest_digits( $num );

		if ( $point <= 0 ) {
			return $sign . '0.' . str_repeat( '0', -$point ) . $digits;
		}

		if ( $point >= strlen( $digits ) ) {
			return $sign . $digits . str_repeat( '0', $point - strlen( $digits ) );
		}

		return $sign . substr( $digits, 0, $point ) . '.' . substr( $digits, $point );
	}

	/**
	 * Get the shortest significant digits of a float and the position of its decimal point.
	 *
	 * @since 1.13.0
	 *
	 * @param float $num Non-zero finite number.
	 *
	 * @return array The sign, the digits without leading or trailing zeros, and the decimal point position.
	 */
	private function get_shortest_digits( float $num ): array {

		$repr = '';

		// The first precision that reads back to the same float yields the shortest digits.
		for ( $decimals = 0; $decimals <= 16; $decimals++ ) {
			$repr = sprintf( "%.{$decimals}e", $num );

			if ( (float) $repr === $num ) {
				break;
			}
		}

		preg_match( '/^(-?)(\d)(?:\.(\d+))?e([+-]\d+)$/', $repr, $parts );

		$digits = rtrim( $parts[2] . ( $parts[3] ?? '' ), '0' );

		return [ $parts[1], $digits === '' ? '0' : $digits, (int) $parts[4] + 1 ];
	}

	/**
	 * Get prepared math operation arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param int|float|string $left  The left argument.
	 * @param int|float|string $right The right argument.
	 *
	 * @return array Prepared args.
	 */
	private function get_prepared_math_args( $left, $right ): array {

		$left_num  = $this->to_float( $left );
		$right_num = $this->to_float( $right );

		return [
			'left_num'  => $left_num,
			'right_num' => $right_num,
			'precision' => max( $this->get_precision( $left_num ), $this->get_precision( $right_num ) ),
		];
	}

	/**
	 * Get inner functions as an array.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of functions and their metadata.
	 */
	public function get(): array {

		return [

			/**
			 * Plus operation.
			 *
			 * @since 1.0.0
			 *
			 * @param int|float|string $left  The left argument.
			 * @param int|float|string $right The right argument.
			 *
			 * @return int|float
			 */
			'plus'       => function ( $left, $right ) {

				$arg = $this->get_prepared_math_args( $left, $right );

				return $this->round( $arg['left_num'] + $arg['right_num'], $arg['precision'] );
			},

			/**
			 * Minus operation.
			 *
			 * @since 1.0.0
			 *
			 * @param int|float|string $left  The left argument.
			 * @param int|float|string $right The right argument.
			 *
			 * @return int|float
			 */
			'minus'      => function ( $left, $right ) {

				$arg = $this->get_prepared_math_args( $left, $right );

				return $this->round( $arg['left_num'] - $arg['right_num'], $arg['precision'] );
			},

			/**
			 * Multiply operation.
			 *
			 * @since 1.0.0
			 *
			 * @param int|float|string $left  The left argument.
			 * @param int|float|string $right The right argument.
			 *
			 * @return int|float
			 */
			'mul'        => function ( $left, $right ) {

				$arg = $this->get_prepared_math_args( $left, $right );

				// A product carries the decimals of its most precise operand, but at least MIN_PRODUCT_DECIMALS, so a
				// two-decimal price whose trailing zero the float dropped still multiplies exactly (276.90 * 0.2 = 55.38).
				return $this->round( $arg['left_num'] * $arg['right_num'], max( $arg['precision'], self::MIN_PRODUCT_DECIMALS ) );
			},

			/**
			 * Division operation.
			 *
			 * @since 1.0.0
			 *
			 * @param int|float|string $left  The left argument.
			 * @param int|float|string $right The right argument.
			 *
			 * @return int|float
			 */
			'div'        => function ( $left, $right ) {

				$arg = $this->get_prepared_math_args( $left, $right );

				return $this->round( $arg['left_num'] / $arg['right_num'] );
			},

			/**
			 * Modulo operation.
			 *
			 * @since 1.0.0
			 *
			 * @param int|float|string $left  The left argument.
			 * @param int|float|string $right The right argument.
			 *
			 * @return int|float
			 */
			'mod'        => function ( $left, $right ) {

				$arg = $this->get_prepared_math_args( $left, $right );

				$div = $arg['left_num'] / $arg['right_num'];
				$mod = $arg['left_num'] - floor( $div ) * $arg['right_num'];

				return $this->round( $mod, $arg['precision'] );
			},

			/**
			 * Boolean OR operation.
			 *
			 * @since 1.7.0
			 *
			 * @param bool $left  The left argument.
			 * @param bool $right The right argument.
			 *
			 * @return int
			 */
			'booleanor'  => function ( $left, $right ) {
				return (int) ( $left || $right );
			},

			/**
			 * Boolean AND operation.
			 *
			 * @since 1.7.0
			 *
			 * @param bool $left  The left argument.
			 * @param bool $right The right argument.
			 *
			 * @return int
			 */
			'booleanand' => function ( $left, $right ) {
				return (int) ( $left && $right );
			},
		];
	}
}
