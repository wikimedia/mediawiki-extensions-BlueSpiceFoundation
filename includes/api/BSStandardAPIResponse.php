<?php

/**
 * This response should implement the ExtJS standard format for serverside
 * form validations:
 * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.action.Submit
 *
 * TODO: do a clean implemenation with gettes and setters
 */
class BSStandardAPIResponse {
	// ExtJS
	public $errors = array();
	// ExtJS
	public $success = false;

	// Custom fields
	public $message = '';
	public $payload = array();
	public $payload_count = 0;
}
