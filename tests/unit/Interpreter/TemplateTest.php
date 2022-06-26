<?php
namespace OCA\FilesScripts;

use OCA\FilesScripts\Interpreter\Functions\Template\Mustache;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase {
	public function testMustache(): void {
		$data = [
			'foo' => 'bar',
			'arr' => [ 1=>['id'=>'a'], 2=>['id'=>'b'], 3=>['id'=>'c'] ]
		];
		$template = "{{ foo }}
{{# arr}}
  {{ id }}
{{/ arr}}";
		$expected = "bar
  a
  b
  c
";


		$this->assertEquals(
			$expected,
			(new Mustache())->run($template, $data)
		);
	}
}
