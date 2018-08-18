<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * Template Guide
 *
 * A template is a string and can be saved in a plain text file or an html
 * file. It contains some placeholders as minimum requirement.
 * The template typically contains comprehensive markup code which is reusable.
 * There are two kinds of placeholders:
 *    databound placeholders
 *       There form is {name_of_key} for associative datasets or {0}, {1}
 *       and so on for non-associative datasets.
 *    itembound placeholders
 *       If items are added to this element with an explicite key, placeholders
 *       in the form of ###name_of_item### are replaced with item's output.
 *
 * @abstract
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Markus Glaser, Sebastian Ulbricht
 *
 * $LastChangedDate: 2010-07-18 01:13:04 +0200 (So, 18 Jul 2010) $
 * $LastChangedBy: mglaser $
 * $Rev: 314 $
 */
// Last review MRG20100816

// TODO MRG20100816: Changelog

// TODO MRG20100816: Kommentare
/**
 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
 * \BlueSpice\TemplateRenderer instead
 */
class ViewBaseElement {
	protected static $_prAutoId = 0;

	/**
	 * Return an autoincremented number to use as element id.
	 * @return int
	 */
	protected static function getAutoId() {
		return ++self::$_prAutoId;
	}

	// TODO MRG20100816: Bitte kurzen Kommentar zum Zweck der Variablen
	protected $_mAutoElement    = 'div';
	protected $_mData           = array();
	protected $_mId             = null;
	protected $_mItems          = array();
	protected $_mTemplate       = '';
	protected $_mAutoWrap       = false;
	protected $_mPresentDataset = null;
	protected $mI18N            = null;
	protected $mOptions         = array();

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
	 * \BlueSpice\TemplateRenderer instead
	 * Build a new element instance and bind an unique id to it.
	 */
	public function __construct() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$this->_mId = 'bs-element-'.self::getAutoId();
		$this->config = \MediaWiki\MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' );
	}

	public function __toString(){
		return $this->execute();
	}

	/**
	 * Set up the element type (HTML-Tag) for this element.
	 * The AutoElement is used to enclose the datasets if no template is defined
	 * or the whole element, if AutoWrapping is not configured or just enabled.
	 * @param String $elm typically an HTML-Tag
	 * @return ViewBaseElement Returns an reference to itself to enable method chaining.
	 */
	public function setAutoElement($elm = 'div') {
		$this->_mAutoElement = $elm;
		return $this;
	}

	/**
	 * Set the autowrapping for this element. Valid options are true, false or
	 * a string with html code to wrap arround this element.
	 * An autowrap string have to contain an placeholder ###CONTENT###.
	 * If the option is true, the AutoElement is used to wrap the element.
	 * @param Mixed $wrap a string or a boolean value
	 * @return ViewBaseElement Returns an reference to itself to enable method chaining.
	 */
	public function setAutoWrap($wrap) {
		$this->_mAutoWrap = $wrap;
		return $this;
	}

	/**
	 * Set the id of the element
	 * @param String $id The id should be unique for this http request because it´s used as html element id.
	 * @return ViewBaseElement Returns an reference to itself to enable method chaining.
	 */
	public function setId($id) {
		$this->_mId = $id;
		return $this;
	}

	/**
	 * Returns the id of this element.
	 * @return Mixed
	 */
	public function getId() {
		return $this->_mId;
	}


	/**
	 * Set a string as template for this element.
	 * @param String $template
	 * @return ViewBaseElement Returns an reference to itself to enable method chaining.
	 */
	public function setTemplate( $template ) {
		$this->_mTemplate = $template;
		return $this;
	}

	/**
	 * Set the content of given file as template for this element.
	 * @param String $path the absolute path to the template file
	 * @return ViewBaseElement Returns an reference to itself to enable method chaining.
	 * @throws Exception
	 */
	public function setTemplateFile( $path ) {
		if ( !is_file( $path ) ) {
			throw new Exception('template file' . $path . ' not found');
		}

		$this->_mTemplate = file_get_contents( $path );
		if($this->_mTemplate === false) {
			throw new Exception('Failed reading template file ' . $path);
		}

		return $this;
	}

	/**
	 * Returns the template content of this element.
	 * @return String
	 */
	public function getTemplate() {
		return $this->_mTemplate;
	}

	// TODO MRG20100831: comment
	public function setOption( $key, $value ) {
		$this->mOptions[$key] = $value;
		return $this;
	}

	/**
	 * Set multiple options to protected array 'mOptions'
	 * @param array $aOptions Associative array with new keys and values to be stored in 'mOptions'
	 */
	public function setOptions( $aOptions ) {
		if ( !is_array( $aOptions ) ) {
			error_log( 'Parameter not an array or array empty, '.__METHOD__.' in '.__FILE__.' line '.__LINE__ );
			return $this;
		}
		$this->mOptions = array_merge( $this->mOptions, $aOptions );
		return $this;
	}

	// TODO MRG20100831: comment
	public function getOption( $key ) {
		if ( isset( $this->mOptions[$key] ) )
			return $this->mOptions[$key];
		return false;
	}

	/**
	 * Binds an array of values to this element.
	 * Each call of this method adds an new dataset to the internal store of this
	 * element.
	 * If the dataset is an assoziative array, the values of the array are replacing
	 * the matching placeholders in an template, which is used.
	 * If you want to use templates with nonassociative arrays, you have to define
	 * placeholders like {0}, {1} and so on.
	 * @param array $dataSet
	 * @return ViewBaseElement Returns an reference to itself to enable method chaining.
	 */
	public function addData( array $dataSet ) {
		$this->_mData[] = $dataSet;
		return $this;
	}

	public function addCompleteDataset( array $dataSet ) {
		$this->_mData = $dataSet;
		return $this;
	}

	/**
	 * Adds an child element to this element.
	 * As parameter "item" a string with the element type or an element instance
	 * can be given. The parameter "key" is optional and for future use.
	 * @param String/Object $item
	 * @param String $key
	 * @return Object Returns the added item
	 */
	public function addItem( $item, $key = false ) {
		if ( !( $item instanceof ViewBaseElement ) ) {
			return false;
		}
		// better: if( empty( $item ) ), see second note on http://www.php.net/manual/en/types.comparisons.php
		if ( !$item ) {
			return false;
		}
		if ( $key && ( is_numeric( $key ) || is_string( $key ) ) ) {
			$this->_mItems[$key] = $item;
		}
		else {
			$this->_mItems[] = $item;
		}
		return $item;
	}

	public function hasItems() {
		return count( $this->_mItems );
	}

	// TODO MRG20100816: Kennzeichnen, dass diese Funktion rekursiv aufgerufen wird.
	// TODO MRG20100816: Genauer kommentieren
	/**
	 *
	 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
     * \BlueSpice\TemplateRenderer instead
	 * @return String Returns the output of this element.
	 */
	public function execute( $params = false ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$output = '';
		// TODO MRG20100816: Eine Mischung aus data und items geht nicht?
		if ( count( $this->_mData ) ) {
			foreach( $this->_mData as $dataSet ) {
				$output .= $this->processData( $dataSet );
			}
		}
		elseif ( count( $this->_mItems ) ) {
			if ( $this->_mTemplate != '' ) {
				$output = $this->_mTemplate;
				$output = preg_replace_callback(
					'/###([-_|A-Za-z0-9]*?)###/',
					array( $this, 'processItem' ),
					$output
				);
			}
			else {
				$output .= $this->getAutoElementOpener();
				foreach ( $this->_mItems as $item ) {
					$output .= $item->execute();
				}
				$output .= $this->getAutoElementCloser();
			}
		}
		else {
			return '';
		}
		if ( $this->_mAutoWrap && is_string( $this->_mAutoWrap ) ) {
			$output = preg_replace( '/###CONTENT###/', $output, $this->_mAutoWrap );
		}
		return $output;
	}

	protected function processData( $dataSet ) {
		$this->_mPresentDataset = $dataSet;
		if($this->_mTemplate != '') {
			$output = $this->_mTemplate;
			$output = preg_replace_callback(
				'/###([-_|A-Za-z0-9]*?)###/',
				array( $this, 'processItem' ),
				$output
			);
			foreach( $dataSet as $key => $value ) {
				$output = str_replace('{'.$key.'}', $value, $output);
			}
		}
		else {
			foreach ( $dataSet as $key => $value ) {
				$output = $this->getAutoElementOpener();
				$output .= $value;
				$output .= $this->getAutoElementCloser();
			}
		}

		$this->_mPresentDataset = null;
		return $output;
	}

	protected function processItem( $matches ) {
		$request = $matches[1];

		// TODO MRG20100816: Ist diese Token-Syntax irgendwo beschrieben? Ausserdem müssen wir sicherstellen, dass
		// | nicht anderweitig verwendet wird.
		$tokens = explode( '|', $request );
		$item = array_shift( $tokens );
		if( !isset( $this->_mItems[$item] ) ) {
			return '';
		}
		if ( count( $tokens ) ) {
			$params = array();
			foreach ( $tokens as $token ) {
				if ( isset( $this->_mPresentDataset[$token] )
				) {
					$params[$token] = $this->_mPresentDataset[$token];
				}
				else {
					$params[$token] = null;
				}
			}
			return $this->_mItems[$item]->execute( $params );
		}
		return $this->_mItems[$item]->execute();
	}

	// TODO MRG20100816: Kommentar: Das sollte in der Regel überschrieben werden.
	protected function getAutoElementOpener() {
		if(!$this->_mAutoElement) {
			return '';
		}

		// TODO MRG20100816: Wahrscheinlich brauchen wir noch eine autoElementClass.
		return '<'.$this->_mAutoElement.' id="'.$this->_mId.'">';
	}

	protected function getAutoElementCloser() {
		if ( !$this->_mAutoElement ) {
			return '';
		}
		return '</'.$this->_mAutoElement.'>';
	}

	// TODO MRG20100831: comment
	protected function renderLink( $options = array(), $content ) {
		$glue = '';
		$href = isset( $options['href'] )
			? $options['href']
			: '';
		if ( isset( $options['query'] ) ) {
			$glue = (strpos( $href, '?' ) === false)
				? '?'
				: '&';
			$href = $href.$glue.$options['query'];
		}
		$out = '<a '
			.'href="'.$href.'" ';
		if ( isset( $options['title'] ) ) {
			$out .= 'title="'.$options['title'].'" ';
		}
		if ( isset( $options['class'] ) ) {
			$out .= 'class="'.$options['class'].'" ';
		}
		if (isset($options['openInNewWindow'])) {
			$out .= 'target="_blank" ';
		}
		$out .= '>';
		$out .= $content;
		$out .= '</a>';
		return $out;
	}

}
