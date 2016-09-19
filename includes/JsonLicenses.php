<?php

/**
 * JsonLicenses
 *
 * Extends the mediawiki licenses class to put the available licenses out in a json format for ExtJS combobox.
 *
 * @author Sebastian Ulbricht
 */
class JsonLicenses extends Licenses {

	protected $json;

	public function __construct() {
		parent::__construct( array( 'fieldname' => 'JsonLicenses' ) );
	}

	public function getJsonOutput() {
		$this->json[] = $this->outputJsonOption( wfMessage( 'nolicense' )->text(), '' );
		$this->makeJson( $this->getLicenses() );
		return json_encode(array('items' => $this->json));
	}

	protected function outputJsonOption( $text, $value, $depth = 0 ) {
		return array(
			'text' => $text,
			'value' => "\n\n==".  wfMessage( 'license-header' )->inContentLanguage()->text()."==\n{{".$value."}}",
			'indent'=> $depth
		);
	}

	protected function makeJson( $tagset, $depth = 0 ) {
		foreach ( $tagset as $key => $val ) {
			if ( is_array( $val ) ) {
				$this->json[] = $this->outputJsonOption( $key, '', $depth );
				$this->makeJson( $val, $depth + 1 );
			}
			else {
				$this->json[] = $this->outputJsonOption( $val->text, $val->template, $depth );
			}
		}
	}

}