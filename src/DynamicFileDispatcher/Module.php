<?php

namespace BlueSpice\DynamicFileDispatcher;

abstract class Module {

	/**
	 *
	 * @var \IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @var string
	 */
	protected $module = '';

	/**
	 *
	 * @var string
	 */
	protected $titleText = '';

	/**
	 *
	 * @var array
	 */
	protected $params = [];

	/**
	 *
	 * @param Params $params
	 * @param \Config $config
	 * @param \IContextSource $context
	 * @param boolean $secure - set to false when internal use, to improve
	 * performance
	 * @return DynamicFileDispatcher
	 * @throws \MWException
	 */
	public function __construct( $params, $config, $context, $secure = true ) {
		$this->context = $context;
		$this->config = $config;
		$this->extractParams( $params );
		if( $secure ) {
			$this->checkPermissions( $params );
		}
	}

	/**
	 *
	 * @return array
	 */
	public function getParamDefinition() {
		return [
			Params::MODULE => [
				Params::PARAM_TYPE => Params::TYPE_STRING,
				Params::PARAM_DEFAULT => '',
			]
		];
	}

	/**
	 *
	 * @param Params $params
	 */
	protected function extractParams( $params ) {
		foreach( $this->getParamDefinition() as $paramName => $definition ) {
			if( $paramName == Params::MODULE ) {
				$this->module = $params->get( $paramName, $definition );
				continue;
			}
			$this->params[$paramName] = $params->get( $paramName, $definition );
		}
	}

	/**
	 *
	 * @return \IContextSource
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 *
	 * @return \Config
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 *
	 * @return string
	 */
	public function getModuleName() {
		return $this->module;
	}

	/**
	 *
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * @param Params $params
	 * @return null
	 * @throws \MWException
	 */
	protected function checkPermissions( $params ) {
		$user = $this->getContext()->getUser();
		if( !$this->isTitleRequired() ) {
			if( !$user->isAllowed( $this->getPermissionKey() ) ) {
				throw new \MWException( 'permission denied' );
			}
			return;
		}
		$title = \Title::newFromText( $this->titleText );
		if( !$title instanceof \Title ) {
			throw new \MWException( 'title required' );
		}
		if( $this->mustRequiredTitleExist() && !$title->exists() ) {
			throw new \MWException( 'title must exist' );
		}
		if( !$title->userCan( $this->getPermissionKey(), $user ) ) {
			throw new \MWException( 'permission denied' );
		}
	}

	protected function isTitleRequired() {
		return false;
	}

	protected function mustRequiredTitleExist() {
		return false;
	}

	protected function getPermissionKey() {
		return 'read';
	}

	/**
	 * @return \File
	 */
	abstract public function getFile();

}
