/* global WPFormsCalculations */

/**
 * WPForms Calculation feature.
 *
 * Inner functions module.
 *
 * @since 1.0.0
 *
 * @return {Object} Inner Functions module.
 */
export default function() { // eslint-disable-line max-lines-per-function
	/**
	 * Fewest decimals a product may be rounded to.
	 *
	 * Two decimals are the granularity money is written in, so a product that is exact within them
	 * is never rounded away even when a trailing zero was lost in the float (276.90 * 0.2 = 55.38).
	 *
	 * @since 1.13.0
	 *
	 * @type {number}
	 */
	const MIN_PRODUCT_DECIMALS = 2;

	/**
	 * Scaled value from which every float is an integer, so there is nothing left to round.
	 *
	 * @since 1.13.0
	 *
	 * @type {number}
	 */
	const ROUNDING_LIMIT = Math.pow( 2, 53 );

	/**
	 * Math functions.
	 *
	 * @since 1.0.0
	 *
	 * @type {Object}
	 */
	const math = {
		/**
		 * Plus operation.
		 *
		 * @since 1.0.0
		 *
		 * @param {string|number} left  The left argument.
		 * @param {string|number} right The right argument.
		 *
		 * @return {number} Result of the operation.
		 */
		plus( left, right ) {
			const arg = math.getPreparedMathArgs( left, right );

			return math.round( arg.leftNum + arg.rightNum, arg.precision );
		},

		/**
		 * Minus operation.
		 *
		 * @since 1.0.0
		 *
		 * @param {string|number} left  The left argument.
		 * @param {string|number} right The right argument.
		 *
		 * @return {number} Result of the operation.
		 */
		minus( left, right ) {
			const arg = math.getPreparedMathArgs( left, right );

			return math.round( arg.leftNum - arg.rightNum, arg.precision );
		},

		/**
		 * Multiply operation.
		 *
		 * @since 1.0.0
		 *
		 * @param {string|number} left  The left argument.
		 * @param {string|number} right The right argument.
		 *
		 * @return {number} Result of the operation.
		 */
		mul( left, right ) {
			const arg = math.getPreparedMathArgs( left, right );

			// A product carries the decimals of its most precise operand, but at least MIN_PRODUCT_DECIMALS, so a
			// two-decimal price whose trailing zero the float dropped still multiplies exactly (276.90 * 0.2 = 55.38).
			return math.round( arg.leftNum * arg.rightNum, Math.max( arg.precision, MIN_PRODUCT_DECIMALS ) );
		},

		/**
		 * Division operation.
		 *
		 * @since 1.0.0
		 *
		 * @param {string|number} left  The left argument.
		 * @param {string|number} right The right argument.
		 *
		 * @return {number} Result of the operation.
		 */
		div( left, right ) {
			const arg = math.getPreparedMathArgs( left, right );

			return math.round( arg.leftNum / arg.rightNum );
		},

		/**
		 * Modulo operation.
		 *
		 * @since 1.0.0
		 *
		 * @param {string|number} left  The left argument.
		 * @param {string|number} right The right argument.
		 *
		 * @return {number} Result of the operation.
		 */
		mod( left, right ) {
			const arg = math.getPreparedMathArgs( left, right );

			return math.round( arg.leftNum % arg.rightNum, arg.precision );
		},

		/**
		 * Rounds a float.
		 *
		 * Exponents are shifted arithmetically, so values already in exponent notation do not turn into NaN.
		 *
		 * @since 1.0.0
		 *
		 * @param {number} num       The value to round.
		 * @param {number} precision The optional number of decimal digits to round to.
		 *
		 * @return {number} The rounded value.
		 */
		round( num, precision = 12 ) {
			// Currency decimals arrive as a string, which the exponent arithmetic below would concatenate.
			precision = Number( precision );
			num = Number( num );

			// Shifting such a number would only perturb its last digit; the server keeps it as it is too.
			if ( Math.abs( num ) * Math.pow( 10, precision ) >= ROUNDING_LIMIT ) {
				return num;
			}

			const [ mantissa, exponent ] = num.toExponential().split( 'e' );
			const shifted = Math.round( Number( mantissa + 'e' + ( Number( exponent ) + precision ) ) );
			const [ shiftedMantissa, shiftedExponent ] = shifted.toExponential().split( 'e' );

			return Number( shiftedMantissa + 'e' + ( Number( shiftedExponent ) - precision ) );
		},

		/**
		 * Convert string to a number.
		 *
		 * This function is a wrapper for WPFormsCalculations.parseFloat,
		 * but in the case of str === '' it returns 0.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} str       String to convert.
		 * @param {number} precision Round number to $precision decimal digits. Defaults to 12. Optional.
		 *
		 * @return {number} The converted number.
		 */
		parseFloat( str, precision = 12 ) {
			return Number( WPFormsCalculations.parseFloat( str, precision ) );
		},

		/**
		 * Get prepared math operation arguments.
		 *
		 * @since 1.0.0
		 *
		 * @param {string|number} left  The left argument.
		 * @param {string|number} right The right argument.
		 *
		 * @return {Object} Prepared args.
		 */
		getPreparedMathArgs( left, right ) {
			const leftNum = math.parseFloat( left ),
				rightNum = math.parseFloat( right );

			return {
				leftNum,
				rightNum,
				precision: Math.max( math.getPrecision( leftNum ), math.getPrecision( rightNum ) ),
			};
		},

		/**
		 * Get prepared math operation arguments.
		 *
		 * @since 1.0.0
		 *
		 * @param {number} num Number value.
		 *
		 * @return {number} Number precision.
		 */
		getPrecision( num ) {
			// A tiny number prints in exponent notation, which hides its decimals, so the plain spelling is counted.
			const chunks = WPFormsCalculations.toPlainDecimal( num ).split( '.' );

			return chunks[ 1 ] ? chunks[ 1 ].length : 0;
		},

		/**
		 * Boolean OR operation.
		 *
		 * @since 1.7.0
		 *
		 * @param {boolean} left  The left argument.
		 * @param {boolean} right The right argument.
		 *
		 * @return {number} Result of the operation.
		 */
		booleanor( left, right ) {
			return Number( !! ( left || right ) );
		},

		/**
		 * Boolean AND operation.
		 *
		 * @since 1.7.0
		 *
		 * @param {boolean} left  The left argument.
		 * @param {boolean} right The right argument.
		 *
		 * @return {number} Result of the operation.
		 */
		booleanand( left, right ) {
			return Number( !! ( left && right ) );
		},
	};

	return { ...math };
}
