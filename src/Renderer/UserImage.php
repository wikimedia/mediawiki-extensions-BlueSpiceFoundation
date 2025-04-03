<?php
namespace BlueSpice\Renderer;

use BlueSpice\Utility\CacheHelper;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Context\RequestContext;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\User\User;

class UserImage extends \BlueSpice\TemplateRenderer {
	public const PARAM_WIDTH = 'width';
	public const PARAM_HEIGHT = 'height';
	public const PARAM_USER = 'user';
	public const PARAM_IMAGE_ALT = 'imagealt';

	/**
	 *
	 * @var User
	 */
	protected $user = null;

	/**
	 *
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name
	 * @param CacheHelper|null $cacheHelper
	 */
	public function __construct( Config $config, Params $params,
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

		$this->user = $params->get(
			static::PARAM_USER,
			RequestContext::getMain()->getUser()
		);
		if ( !$this->user instanceof User ) {
			$this->user = RequestContext::getMain()->getUser();
		}
		$this->args[static::PARAM_WIDTH] = $params->get(
			static::PARAM_WIDTH,
			50
		);
		$this->args[static::PARAM_HEIGHT] = $params->get(
			static::PARAM_HEIGHT,
			50
		);
		if ( empty( $this->args[static::PARAM_CLASS] ) ) {
			$this->args[static::PARAM_CLASS] = '';
		}
		$this->args[static::PARAM_CLASS] .= ' bs-userminiprofile';

		$this->args['imagesrc'] = $this->getUser()->getName();
		$this->args['username'] = $this->getUser()->getName();

		$userHelper = $this->services->getService( 'BSUtilityFactory' )
			->getUserHelper( $this->getUser() );

		$this->args['imagetitle'] = $userHelper->getDisplayName();

		$this->args[static::PARAM_IMAGE_ALT] = $params->get(
			static::PARAM_IMAGE_ALT,
			''
		);
		$this->args['anchorhref']
			= $this->getUser()->getUserPage()->getLocalURL();
	}

	/**
	 *
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * Returns the template's name
	 * @return string
	 */
	public function getTemplateName() {
		return "BlueSpiceFoundation.UserImage";
	}

	/**
	 *
	 * @param mixed $val
	 * @return string
	 */
	protected function render_imagesrc( $val ) {
		return $this->services->getService( 'MWStake.DynamicFileDispatcher.Factory' )->getUrl(
			'userprofileimage',
			[
				'username' => $val,
				'width' => (int)$this->args[static::PARAM_WIDTH] * 1.4,
				'height' => (int)$this->args[static::PARAM_HEIGHT] * 1.4
			]
		);
	}

	/**
	 *
	 * @return string
	 */
	protected function getCacheKey() {
		return $this->getCacheHelper()->getCacheKey(
			'BSFoundation',
			'TemplateRenderer',
			'UserImage',
			$this->getUser()->getName(),
			$this->args[static::PARAM_WIDTH],
			$this->args[static::PARAM_HEIGHT]
		);
	}
}
