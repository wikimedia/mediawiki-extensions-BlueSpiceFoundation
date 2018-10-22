<?php
namespace BlueSpice\Renderer;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Renderer\Params;
use BlueSpice\DynamicFileDispatcher\Params as DFDParams;
use BlueSpice\DynamicFileDispatcher\UserProfileImage;
use BlueSpice\Services;

class UserImage extends \BlueSpice\TemplateRenderer {
	const PARAM_WIDTH = 'width';
	const PARAM_HEIGHT = 'height';
	const PARAM_USER = 'user';

	/**
	 *
	 * @var User
	 */
	protected $user = null;

	/**
	 * Constructor
	 */
	public function __construct( \Config $config, Params $params, LinkRenderer $linkRenderer = null ) {
		parent::__construct( $config, $params, $linkRenderer );
		$this->user = $params->get(
			static::PARAM_USER,
			\RequestContext::getMain()->getUser()
		);
		if( !$this->user instanceof \User ) {
			$this->user = \RequestContext::getMain()->getUser();
		}
		$this->args[static::PARAM_WIDTH] = $params->get(
			static::PARAM_WIDTH,
			50
		);
		$this->args[static::PARAM_HEIGHT] = $params->get(
			static::PARAM_HEIGHT,
			50
		);
		if( empty( $this->args[static::PARAM_CLASS] ) ) {
			$this->args[static::PARAM_CLASS] = '';
		}
		$this->args[static::PARAM_CLASS] .= ' bs-userminiprofile';

		$this->args['imagesrc'] = $this->getUser()->getName();
		$this->args['username'] = $this->getUser()->getName();

		$userHelper = Services::getInstance()->getBSUtilityFactory()
			->getUserHelper( $this->getUser() );

		$this->args['imagetitle'] = $userHelper->getDisplayName();
		$this->args['imagealt'] = $userHelper->getDisplayName();
		$this->args['anchorhref']
			= $this->getUser()->getUserPage()->getLocalURL();
	}

	/**
	 *
	 * @return \User
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

	protected function render_imagesrc( $val ) {
		$params = [
			DFDParams::MODULE => UserProfileImage::MODULE_NAME,
			UserProfileImage::USERNAME => $val,
			UserProfileImage::WIDTH => (int)$this->args[static::PARAM_WIDTH] * 1.4,
			UserProfileImage::HEIGHT => (int)$this->args[static::PARAM_HEIGHT] * 1.4,
		];

		$dfdUrlBuilder = Services::getInstance()
			->getBSDynamicFileDispatcherUrlBuilder();
		return $dfdUrlBuilder->build( new DFDParams( $params ) );
	}

	protected function getCacheKey() {
		return \BsCacheHelper::getCacheKey(
			'BSFoundation',
			'TemplateRenderer',
			'UserImage',
			$this->getUser()->getID(),
			$this->args[static::PARAM_WIDTH],
			$this->args[static::PARAM_HEIGHT]
		);
	}
}
