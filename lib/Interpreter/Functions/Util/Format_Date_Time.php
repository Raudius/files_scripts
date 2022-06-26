<?php

namespace OCA\FilesScripts\Interpreter\Functions\Util;

use IntlDateFormatter;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `format_date_time(Date date, [String locale], [String timezone], [String pattern]): String`
 *
 * Returns a formatted date string.
 *
 *   - **Date:** See [create_date_time](#create_date_time)
 *   - **Locale:** A valid CLDR locale (if nil, the default PHP local will be used).
 *   - **Timezone:** A string containing any value in the ICU timezone database, or any offset of "GMT" (e.g `GMT-05:30`)
 *   - **Pattern:** A string containing an [ICU-valid pattern](https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax).
 */
class Format_Date_Time extends RegistrableFunction {
	public function run($date = [], $locale = null, $timezone = null, $pattern = '') {
		$locale = is_string($locale) ? $locale : null;
		$fmt = datefmt_create(
			$locale,
			IntlDateFormatter::FULL,
			IntlDateFormatter::FULL,
			$timezone,
			null,
			$pattern
		);

		return datefmt_format($fmt, $this->unpackDate($date)) ?: '';
	}
}
