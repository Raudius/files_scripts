<?php

namespace OCA\FilesScripts\Interpreter\Functions\Util;

use NumberFormatter;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `format_price(Number value, [String symbol], [String currency], [String locale]): String`
 *
 * Formats a number to a formatted string. The symbol, currency and locale can be specified for more precise formatting.
 * By default, locale is set to `en`, and no symbol/currency are specified.
 *
 * **Symbol:** any string is allowed. It is be used as the currency symbol in the output string
 * **Currency:** a string containing a valid ISO 4217 currency code. It is used for calculating currency subdivisions (cents, pennies, etc.)
 * **Locale:** a string containing a valid CLDR locale. It is used for formatting in a locale specific way (e.g. symbol before or after value)
 */
class Format_Price extends RegistrableFunction {
	public function run($value = 0.0, $symbol = '', $currency = 'EUR', $locale = 'en_US') {
		$symbol = $symbol ?: '';

		$fmt = is_string($symbol) && !empty($symbol)
			? numfmt_create($locale, NumberFormatter::CURRENCY)
			: numfmt_create($locale, NumberFormatter::DECIMAL);

		$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, $symbol);
		return $fmt->formatCurrency($value, $currency);
	}
}
