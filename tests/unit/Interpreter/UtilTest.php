<?php

namespace OCA\FilesScripts\Interpreter;

use OCA\FilesScripts\Interpreter\Functions\Util\Create_Date_Time;
use OCA\FilesScripts\Interpreter\Functions\Util\Format_Date_Time;
use OCA\FilesScripts\Interpreter\Functions\Util\Format_Price;
use OCA\FilesScripts\Interpreter\Functions\Util\Json;
use OCA\FilesScripts\Interpreter\Functions\Util\Sort;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase {
	public function testDateFunctions(): void {
		$date = (new Create_Date_Time())->run(2022, 01, 02, 12, 42, 10);
		$this->assertEquals(
			[
				'year' => 2022, 'month' => 1, 'day' => 2,
				'hour' => 12, 'minute' => 42, 'second' => 10
			],
			$date
		);

		$date_formatted = (new Format_Date_Time())->run($date, 'en', 'GMT+0', 'MM yyyy');
		$this->assertEquals('01 2022', $date_formatted);
	}

	public function testJson(): void {
		$arrayIn = [ 'foo' => 'bar', 'numbers' => [1,2,3,4,5] ];
		$stringIn = json_encode($arrayIn);

		$arrayLuaIndex = [ 'foo' => 'bar', 'numbers' => [1 => 1,2 => 2,3 => 3,4 => 4,5 => 5] ];

		$arrayOut = (new Json())->run($stringIn);
		$this->assertNotEquals($arrayIn, $arrayOut);
		$this->assertEquals($arrayLuaIndex, $arrayOut);
	}

	public function testSort(): void {
		$fruits = ['orange', 'apple', 'banana', 'grapes', 'mango'];
		$fruitsAssoc = [ ['name' => 'orange'], ['name' => 'apple'], ['name' => 'banana'], ['name' => 'grapes'], ['name' => 'mango'] ];

		$fruitsSorted = [1 => 'apple', 2 => 'banana', 3 => 'grapes', 4 => 'mango', 5 => 'orange' ];
		$fruitsAssocSorted = [ 1 => ['name' => 'apple'], 2 => ['name' => 'banana'], 3 => ['name' => 'grapes'],  4 => ['name' => 'mango'], 5 => ['name' => 'orange'] ];
		$fruitsSortedDesc = [ 1 => 'orange', 2 => 'mango', 3 => 'grapes', 4 => 'banana', 5 => 'apple' ];

		$this->assertEquals(
			$fruitsSorted,
			(new Sort())->run($fruits)
		);

		$this->assertEquals(
			$fruitsAssocSorted,
			(new Sort())->run($fruitsAssoc, 'name')
		);

		$this->assertEquals(
			$fruitsSortedDesc,
			(new Sort())->run($fruits, null, false)
		);
	}

	public function testPriceFormat(): void {
		$this->assertEquals(
			'42.99',
			(new Format_Price())->run(42.99)
		);

		$this->assertEquals(
			"42,99\xc2\xa0€",
			(new Format_Price())->run(42.99, '€', 'EUR', 'es_ES')
		);
	}
}
