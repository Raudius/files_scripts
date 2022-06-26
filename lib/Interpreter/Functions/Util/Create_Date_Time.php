<?php

namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `create_date_time(Number year, [Number month], [Number day], [Number hour], [Number minute], [Number second]): Date`
 *
 * Creates a `Date` object containing date and time information. If no values are specified the current date-time is returned.
 *
 * The `Date` object is a Lua table containing the following values:
 * ```lua
 * date = {
 *   year= 2022,
 *   month= 06,
 *   day= 25,
 *   hour= 16,
 *   minute= 48,
 *   second= 27
 * }
 * ```
 */
class Create_Date_Time extends RegistrableFunction {
	public function run($year = null, $month = 1, $day = 1, $hour = 0, $minute = 0, $second = 0) {
		$date = date_create();
		if ($year !== null) {
			$date = $date->setDate($year, $month, $day);
			$date = $date->setTime($hour, $minute, $second);
		}

		return $this->packDate($date);
	}
}
