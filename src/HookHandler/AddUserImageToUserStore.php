<?php

namespace BlueSpice\HookHandler;

use BlueSpice\Renderer\Params;
use BlueSpice\RendererFactory;
use MediaWiki\User\User;
use MediaWiki\User\UserFactory;
use MWStake\MediaWiki\Component\CommonWebAPIs\Hook\MWStakeCommonWebAPIsQueryStoreResultHook;
use MWStake\MediaWiki\Component\CommonWebAPIs\Rest\UserQueryStore;
use MWStake\MediaWiki\Component\DataStore\ResultSet;

class AddUserImageToUserStore implements MWStakeCommonWebAPIsQueryStoreResultHook {
	/** @var UserFactory */
	private $userFactory;
	/** @var RendererFactory */
	private $rendererFactory;

	/**
	 * @param UserFactory $userFactory
	 * @param RendererFactory $rendererFactory
	 */
	public function __construct( UserFactory $userFactory, RendererFactory $rendererFactory ) {
		$this->userFactory = $userFactory;
		$this->rendererFactory = $rendererFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonWebAPIsQueryStoreResult( $store, &$result ) {
		if ( !( $store instanceof UserQueryStore ) ) {
			return;
		}
		$data = $result->getRecords();
		foreach ( $data as $record ) {
			$thumbParams = [ 'width' => '32', 'height' => '32' ];

			$user = $this->userFactory->newFromId( $record->get( 'user_id' ) );
			if ( $user instanceof User === false ) {
				continue;
			}

			$image = $this->rendererFactory->get( 'userimage', new Params( [
					'user' => $user
				] + $thumbParams ) );

			$record->set( 'user_image', $image->render() );
		}

		$result = new ResultSet( $data, $result->getTotal() );
	}
}
