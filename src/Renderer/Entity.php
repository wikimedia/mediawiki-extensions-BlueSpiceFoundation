<?php
namespace BlueSpice\Renderer;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Renderer\Params;

class Entity extends \BlueSpice\TemplateRenderer implements \JsonSerializable {
	const PARAM_ENTITY = 'entity';
	const PARAM_CONTEXT = 'context';

	/**
	 *
	 * @var \BlueSpice\Entity
	 */
	protected $entity = null;

	/**
	 *
	 * @var \IContextSource
	 */
	protected $context = null;

	/**
	 * Constructor
	 */
	public function __construct( \Config $config, Params $params, LinkRenderer $linkRenderer = null ) {
		parent::__construct( $config, $params, $linkRenderer );
		$this->context = $params->get(
			static::PARAM_CONTEXT,
			null
		);
		if( !$this->context instanceof \IContextSource ) {
			throw new \MWException(
				'"\IContextSource" must be given by ' . static::PARAM_CONTEXT. ' param'
			);
		}
		$this->entity = $params->get(
			static::PARAM_ENTITY,
			null
		);
		if( !$this->entity instanceof \BlueSpice\Entity ) {
			throw new \MWException(
				'"\BlueSpice\Entity" must be given by ' . static::PARAM_ENTITY. ' param'
			);
		}

		$this->args[static::PARAM_CLASS] .= ' bs-entity';
		$this->args[static::PARAM_TAG] = 'div';
	}

	protected function render_content( $val ) {
		return \FormatJson::encode( $this->getEntity(), true );
	}

	/**
	 * Returns the template's name
	 * @return string
	 */
	public function getTemplateName() {
		return $this->getEntity()->getConfig()->get( 'EntityRenderTemplate' );
	}

	/**
	 *
	 * @return \BlueSpice\Entity
	 */
	public function getEntity() {
		return $this->entity;
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
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->args;
	}

}
