<?php

namespace BlueSpice\Tests\Utility;

use BlueSpice\Utility\WikiTextTemplateTreeParser;

class WikiTextTemplateTreeParserTest extends \PHPUnit\Framework\TestCase {

	/**
	 *
	 * @param type $wikiText
	 * @param type $expectedArray
	 * @dataProvider provideGetArrayData
	 */
	public function testGetArray( $wikiText, $expectedArray ) {
		$parser = new WikiTextTemplateTreeParser( $wikiText );
		$this->assertEquals( $expectedArray , $parser->getArray() );
	}

	public function provideGetArrayData() {
		return [
			'simple-flat' => [
<<<HERE
{{TemplateX
|typist=User:WikiSysop
|topic=Some topic
|date=2018-05-11
}}
{{TemplateX/Item
|date=2018/05/11
|title=Some title
|done=yes
|desc={{TemplateX/Item/Desc|title=Test A|date=2018/04/17|type=A|assignee=ABC}}
}}
{{TemplateX/Item
|date=2017/03/30
|title=ABCDE
|done=no
|desc={{TemplateX/Item/Desc|title=Test 1|date=2018/04/17|type=A|assignee=ABC}}
{{TemplateX/Item/Desc|title=Test 2|date=2018/04/18|type=I|assignee=DEF}}
{{TemplateX/Item/Desc|title=Test 3|date=2018/04/18}}
{{TemplateX/Item/Desc}}
}}

Some arbitrary text
HERE
				,
[
	[
		'name' => 'TemplateX',
		'params' => [
			'typist' => 'User:WikiSysop',
			'topic' => 'Some topic',
			'date' => '2018-05-11'
		]
	],
	[
		'name' => 'TemplateX/Item',
		'params' => [
			'date' => '2018/05/11',
			'title' => 'Some title',
			'done' => 'yes',
			'desc' => [
				[
					'name' => 'TemplateX/Item/Desc',
					'params'=> [
						'title' => 'Test A',
						'date' => '2018/04/17',
						'type' => 'A',
						'assignee' => 'ABC'
					]
				]
			]
		]
	],
	[
		'name' => 'TemplateX/Item',
		'params' => [
			'date' => '2017/03/30',
			'title' => 'ABCDE',
			'done' => 'no',
			'desc' => [
				[
					'name' => 'TemplateX/Item/Desc',
					'params'=> [
						'title' => 'Test 1',
						'date' => '2018/04/17',
						'type' => 'A',
						'assignee' => 'ABC'
					]
				],
				[
					'name' => 'TemplateX/Item/Desc',
					'params'=> [
						'title' => 'Test 2',
						'date' => '2018/04/18',
						'type' => 'I',
						'assignee' => 'DEF'
					]
				],
				[
					'name' => 'TemplateX/Item/Desc',
					'params'=> [
						'title' => 'Test 3',
						'date' => '2018/04/18'
					]
				],
				[
					'name' => 'TemplateX/Item/Desc',
					'params'=> []
				]
			]
		]
	]
]
			]
		];
	}

}
