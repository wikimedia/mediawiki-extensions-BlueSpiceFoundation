<?php

abstract class ApiExtJSBase extends ApiBase {

	public function getAllowedParams() {
		return array(
			'sort' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			),
			'page' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false
			),
			'limit' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false
			),
			'start' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false
			),
			'_dc' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false
			),
			'format' => array(
				ApiBase::PARAM_DFLT => 'json',
				ApiBase::PARAM_TYPE => array( 'json', 'jsonfm' ),
			)
		);
	}

	public function getParamDescription() {
		return array(
			'sort' => 'JSON string with sorting info',
			'page' => 'Allows server side calculation of start/limit',
			'limit' => 'Number of results to return',
			'start' => 'The offset to start the result list from',
			'_dc' => '"Disable cache" flag',
			'format' => 'The format of the output'
		);
	}

	public function getResultProperties() {
		return array(
			'' => array(
				'success' => 'boolean',
				'results' => array(
					ApiBase::PROP_TYPE => 'integer',
					ApiBase::PROP_NULLABLE => false
				),
				'rows' => array(
					ApiBase::PROP_TYPE => 'array',
					ApiBase::PROP_NULLABLE => false
				)
			)
		);
	}

	protected function getParameterFromSettings($paramName, $paramSettings, $parseLimit) {
		$value = parent::getParameterFromSettings($paramName, $paramSettings, $parseLimit);
		//Unfortunately there is no way to register custom types for parameters
		if( $paramName === 'sort' ) {
			$value = FormatJson::decode($value);
		}
		return $value;
	}
}