<?php

namespace BlueSpice\Tests\WikiText;

class TemplateTest extends \PHPUnit\Framework\TestCase {
	public function testSimpleIndexedParams() {
		$template = new \BlueSpice\WikiText\Template( 'TestTemplate', [
			'Value 1', 'Value 2', 'Value 3'
		] );

		$wikiText = $template->render();
		$expectedWikiText = <<<HERE
{{TestTemplate
|Value 1
|Value 2
|Value 3
}}
HERE;

		$this->assertEquals( $expectedWikiText, $wikiText );

		$template->setRenderFormatted( false );
		$wikiText = $template->render();
		$expectedWikiText = <<<HERE
{{TestTemplate|Value 1|Value 2|Value 3}}
HERE;

		$this->assertEquals( $expectedWikiText, $wikiText );
	}

	public function testSimpleNamedParams() {
		$template = new \BlueSpice\WikiText\Template( 'TestTemplate', [
			'param1' => 'Value 1',
			'param2' => 'Value 2',
			'param3' => 'Value 3'
		] );

		$wikiText = $template->render();
		$expectedWikiText = <<<HERE
{{TestTemplate
|param1 = Value 1
|param2 = Value 2
|param3 = Value 3
}}
HERE;

		$this->assertEquals( $expectedWikiText, $wikiText );

		$template->setRenderFormatted( false );
		$wikiText = $template->render();
		$expectedWikiText = <<<HERE
{{TestTemplate|param1 = Value 1|param2 = Value 2|param3 = Value 3}}
HERE;

		$this->assertEquals( $expectedWikiText, $wikiText );
	}

		public function testSimpleMixedParams() {
		$template = new \BlueSpice\WikiText\Template( 'TestTemplate', [
			'param1' => 'Value 1',
			'Value 2',
			'param3' => 'Value 3'
		] );

		$wikiText = $template->render();
		$expectedWikiText = <<<HERE
{{TestTemplate
|param1 = Value 1
|Value 2
|param3 = Value 3
}}
HERE;

		$this->assertEquals( $expectedWikiText, $wikiText );

		$template->setRenderFormatted( false );
		$wikiText = $template->render();
		$expectedWikiText = <<<HERE
{{TestTemplate|param1 = Value 1|Value 2|param3 = Value 3}}
HERE;

		$this->assertEquals( $expectedWikiText, $wikiText );
	}

	public function testComplexNamedParams() {
		$template = new \BlueSpice\WikiText\Template( 'TestTemplate', [
			'param1' => 'Value 1',
			'param2' => [
				"Some normal text with a ",
				new \BlueSpice\WikiText\Template( 'NestedTemplate', [
					'innerParam1' => 'Inner value 1'
				]),
				" and\n\nsome paragraph"
			],
			'param3' => 'Value 3'
		] );

		$wikiText = $template->render();
		$expectedWikiText = <<<HERE
{{TestTemplate
|param1 = Value 1
|param2 = Some normal text with a {{NestedTemplate
|innerParam1 = Inner value 1
}} and

some paragraph
|param3 = Value 3
}}
HERE;

		$this->assertEquals( $expectedWikiText, $wikiText );

		$template->setRenderFormatted( false );
		$wikiText = $template->render();
		$expectedWikiText = <<<HERE
{{TestTemplate|param1 = Value 1|param2 = Some normal text with a {{NestedTemplate
|innerParam1 = Inner value 1
}} and

some paragraph|param3 = Value 3}}
HERE;

		$this->assertEquals( $expectedWikiText, $wikiText );
	}

	public function testAutoLinebreakOnCertainWikiTextParamValues() {
		$template = new \BlueSpice\WikiText\Template( 'TestTemplate', [
			'param1' => [
				"*Some\n*wikitext\n*list"
			]
		] );

		$wikiText = $template->render();
		$expectedWikiText = <<<HERE
{{TestTemplate
|param1 =
*Some
*wikitext
*list
}}
HERE;

		$this->assertEquals( $expectedWikiText, $wikiText );
	}
}
