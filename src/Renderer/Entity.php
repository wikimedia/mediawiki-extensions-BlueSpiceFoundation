<?php
namespace BlueSpice\Renderer;

use BlueSpice\Utility\CacheHelper;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Json\FormatJson;
use MediaWiki\Linker\LinkRenderer;
use MWException;

class Entity extends \BlueSpice\TemplateRenderer implements \JsonSerializable {
	public const PARAM_ENTITY = 'entity';

	/**
	 *
	 * @var \BlueSpice\Entity
	 */
	protected $entity = null;

	/**
	 * Constructor
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name
	 * @param CacheHelper|null $cacheHelper
	 */
	protected function __construct( Config $config, Params $params,
		?LinkRenderer $linkRenderer = null, ?IContextSource $context = null,
		$name = '', ?CacheHelper $cacheHelper = null ) {
		parent::__construct(
			$config,
			$params,
			$linkRenderer,
			$context,
			$name,
			$cacheHelper
		);
		$this->context = $params->get(
			static::PARAM_CONTEXT,
			null
		);
		if ( !$this->context instanceof IContextSource ) {
			throw new MWException(
				'"IContextSource" must be given by ' . static::PARAM_CONTEXT . ' param'
			);
		}
		$this->entity = $params->get(
			static::PARAM_ENTITY,
			null
		);
		if ( !$this->entity instanceof \BlueSpice\Entity ) {
			throw new MWException(
				'"\BlueSpice\Entity" must be given by ' . static::PARAM_ENTITY . ' param'
			);
		}

		$this->args[static::PARAM_CLASS] .= ' bs-entity';
		$this->args[static::PARAM_TAG] = 'div';
	}

	/**
	 *
	 * @param mixed $val
	 * @return string
	 */
	protected function render_content( $val ) {
		return FormatJson::encode( $this->getEntity(), true );
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
	 * @return array
	 */
	public function jsonSerialize(): array {
		return $this->args;
	}

}
