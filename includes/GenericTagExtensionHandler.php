<?php
/**
 * Provides a generic tag extension mechanism for BlueSpice.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */

/**
 * BsGenericTagExtensionHandler class in BlueSpice
 * @package BlueSpice_Foundation
 */
class BsGenericTagExtensionHandler {

	/**
	 * The registered element name that is used within the WikiText
	 * @var string
	 */
	protected $sTagName = '';

	/**
	 * An array that contains basic settings fot the tag and includes a
	 * ParamProcessor\ParamDefinition compatible list of parameter settings
	 * @var array
	 */
	protected $aTagDef = array();

	public function __construct( $sTagName, $aTagDef ) {
		$this->sTagName = $sTagName;
		$this->aTagDef = $this->extendTagDefinition( $aTagDef );
	}

	/**
	 * @param BsExtensionMW[] $aExtensions
	 * @param Parser $parser
	 */
	public static function setupHandlers( $aExtensions, $parser ) {
		foreach( $aExtensions as $oExtension ) {
			if( !$oExtension instanceof BlueSpice\ITagExtensionDefinitionProvider ) {
				continue;
			}
			$aExtensionTags = $oExtension->makeTagExtensionDefinitions();
			foreach( $aExtensionTags as $sTagName => $aTagDef ) {
				$oTagHandler = new self( $sTagName, $aTagDef );
				$parser->setHook( $sTagName, array( $oTagHandler, 'handle' ) );
			}
		}
	}

	/**
	 * Does the actual tag handling and outputs the HTML
	 * @param string $input
	 * @param array $args
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @return string
	 */
	public function handle( $input, array $args, Parser $parser, PPFrame $frame ) {
		if( $this->aTagDef['disableParserCache'] === true ) {
			$parser->disableCache();
		}

		if( $this->aTagDef['parseInput'] === true ) {
			$input = $parser->recursiveTagParse( $input );
		}

		if( $this->aTagDef['parseParams'] === true ) {
			foreach( $args as $iKey => $sValue ) {
				$args[$iKey] = $parser->recursiveTagParse( $sValue );
			}
		}

		$aElementClasses = $this->aTagDef['classes'];
		$aElementClasses[] = 'bs-tag';
		$aElementClasses[] = $this->makeElementClassName();

		$aAttributes = array();
		if( $this->aTagDef['titleMsg'] !== null ) {
			$aAttributes[ 'title' ] = wfMessage( $this->aTagDef['titleMsg'] )->plain();
		}

		$sContent = '';
		try {
			$aProcessedInput = $this->processInput( $input );
			$aProcessedArgs = $this->processArgs( $args );

			$aAttributes['params'] = FormatJson::encode( $aProcessedArgs );
			$parser->getOutput()->setProperty(
				'bs-tag-'.$this->sTagName, $aAttributes['params']
			);

			if( is_callable( $this->aTagDef['callback'] ) ) {
				$sContent = call_user_func_array(
					$this->aTagDef['callback'],
					array(
						$aProcessedInput, $aProcessedArgs, $parser, $frame
					)
				);
			}
		} catch ( Exception $ex ) {
			$sContent = $this->makeExceptionOutput( $ex );
			$aElementClasses[] = 'bs-tag-error';
		}

		$aAttributes['class'] = implode( ' ', $aElementClasses );

		return Html::rawElement(
			$this->aTagDef['element'],
			$aAttributes,
			$sContent
		);
	}

	/**
	 * Checks if a type/validation definition for the input content of an
	 * element exists and processes it accordingly
	 * @param string $input
	 * @return mixed
	 * @throws Exception
	 */
	protected function processInput( $input ) {
		if( $this->aTagDef['input'] === null ) {
			return $input;
		}

		$aInputParams = array(
			'input' => $input
		);

		$oProcessor = ParamProcessor\Processor::newDefault();
		$oProcessor->setParameters(
			$aInputParams,
			array(
				array(
					'name' => 'input',
					'message' => wfMessage( 'bs-tag-input-desc' )->plain()
				)
				+ $this->aTagDef['input']
			)
		);

		$oResult = $oProcessor->processParameters();
		$this->checkForProcessingErrors( $oResult );
		$aProcessedParams = $oResult->getParameters();

		return $aProcessedParams['input']->getValue();
	}

	/**
	 * Checks if a type/validation definition for the arguments of an
	 * element exists and processes them accordingly
	 * @param array $args
	 * @return array
	 * @throws Exception
	 */
	protected function processArgs( $args ) {
		$oProcessor = ParamProcessor\Processor::newDefault();
		$oProcessor->setParameters(
			$args,
			$this->makeParamDefinitions()
		);

		$oResult = $oProcessor->processParameters();
		$this->checkForProcessingErrors( $oResult );
		$aProcessedParams = $oResult->getParameters();

		$aProcessedValues = array();
		foreach( $aProcessedParams as $oProcessedParam ) {
			$aProcessedValues[$oProcessedParam->getName()] = $oProcessedParam->getValue();
		}

		return $aProcessedValues;
	}

	/**
	 * Adds default values for missing pieces of the tag definition
	 * @param array $aTagDef
	 * @return array
	 */
	protected function extendTagDefinition( $aTagDef ) {
		return array_merge(
			$this->makeDefaultTagDefinition(),
			$aTagDef
		);
	}

	/**
	 * All default values that may be needed to process a tag definition
	 * @return array
	 */
	protected function makeDefaultTagDefinition() {
		return array(
			'input' => null, //Param definition for the contents of a tag
			'params' => array(), //Param definition for the arguments of a tag
			'disableParserCache' => false, //Whether or not to disable the parser cache for the page the tag is used on
			'classes' => array(), //Additional CSS classes that should be added to the container element
			'titleMsg' => null, //A message key for the title attribute of the container element
			'element' => 'div', //The element name of the container element. Allows for inline tags.
			'parseInput' => true, //Whether ot not to do a recursiveTagParse on the input before further processing. Allowes for variables, templates, parserfunctions in tag input
			'parseParams' => true, //Whether ot not to do a recursiveTagParse on the arguments before further processing. Allowes for variables, templates, parserfunctions in tag arguments
			'callback' => null, //A optional Callable that generates the actual content within the container element
			'modules' => array(), //ResourceLoader modules to be added to the ParserOutput
			'moduleStyles' => array() //ResourceLoader modules (only CSS) to be added to the ParserOutput
		);
	}

	/**
	 * Generates a standard CSS class for the container element
	 * @return string
	 */
	protected function makeElementClassName() {
		$sPrefix = 'bs-';
		if( strpos( $this->sTagName, 'bs:' ) === 0 ) {
			$sPrefix = '';
		}
		return str_replace( ':', '-', $sPrefix.$this->sTagName );
	}


	/**
	 * Generates the output in case of an exception
	 * @param Exception $ex
	 * @return string
	 */
	public function makeExceptionOutput( $ex ) {
		if( $ex instanceof BSInvalidParamException ) {
			return $this->makeInvalidParamExceptionOutput( $ex );
		}

		return Html::element(
			$this->sTagName,
			array(
				'class' => 'bs-error bs-tag'
			),
			$ex->getMessage()
		);
	}

	/**
	 * Creates ParamProcessor\ParamDefinition compatible definitions based on
	 * the tag definition
	 * @return array
	 */
	public function makeParamDefinitions() {
		$aParamDefs = array();
		foreach( $this->aTagDef['params'] as $sParamName => $aParamDef ) {
			$aParamDefs[] = array(
				'name' => $sParamName,
				'message' => wfMessage( 'bs-tag-param-desc', $sParamName )->plain()
			) + $aParamDef;
		}

		return $aParamDefs;
	}

	/**
	 * Checks if ParamProcessrs has found any errors on the parameters that
	 * did not result in an exception already and throw an exception to display
	 * the errors to the user.
	 *
	 * TODO: This is somewhat bad behavior. Unfortunately at the moment
	 * ParamProcessor does not provide a better mechanism for this, or I am not
	 * aware of it.
	 * @param \ParamProcessor\ProcessingResult $oResult
	 * @throws BSInvalidParamException
	 */
	public function checkForProcessingErrors( $oResult ) {
		$aErrors = $oResult->getErrors();
		if( !empty( $aErrors ) ) {
			$ex = new BSInvalidParamException();
			$ex->setErrors( $aErrors );
			throw $ex;
		}
	}

	/**
	 * Generates the HTML output that tells the user what went wrong
	 * @param BSInvalidParamException $ex
	 * @return string
	 */
	public function makeInvalidParamExceptionOutput( $ex ) {
		$sHtml = '';
		foreach( $ex->getErrors() as $oProcessingError ) {
			$sHtml .= Html::element(
				'div',
				array(),
				$oProcessingError->getMessage()
			);
		}
		return Html::rawElement(
			'div',
			array(
				'class' => 'bs-error bs-tag'
			),
			$sHtml
		);
	}
}
