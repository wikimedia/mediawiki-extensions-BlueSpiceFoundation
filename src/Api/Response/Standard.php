<?php

namespace BlueSpice\Api\Response;

/**
 * This response should implement the ExtJS standard format for serverside
 * form validations:
 * http://docs.sencha.com/extjs/4.2.2/#!/api/Ext.form.action.Submit
 *
 * TODO: do a clean implemenation with gettes and setters
 */
class Standard {
	public const ERRORS = 'errors';
	public const SUCCESS = 'success';
	public const MESSAGE = 'message';
	public const PAYLOAD = 'payload';
	public const PAYLOAD_COUNT = 'payload_count';

	/** @var array */
	public $errors = [];
	/** @var bool */
	public $success = false;

	// Custom fields
	/** @var string */
	public $message = '';
	/** @var array */
	public $payload = [];
	/** @var int */
	public $payload_count = 0;
}
