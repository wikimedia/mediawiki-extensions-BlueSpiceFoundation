<?php

namespace BlueSpice;

use DeferredUpdates;
use Status;
use Title;
use WikiPage;

class SecondaryDataUpdater {
	/**
	 * @var ExtensionAttributeBasedRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @param ExtensionAttributeBasedRegistry $registry
	 */
	public function __construct( ExtensionAttributeBasedRegistry $registry ) {
		$this->registry = $registry;
	}

	/**
	 * @param Title $title
	 * @return Status
	 */
	public function run( Title $title ) {
		$status = Status::newGood();
		if ( !$title->exists() ) {
			$status->warning( "Title must exist" );
		}
		if ( $title->getNamespace() < NS_MAIN ) {
			$status->warning( "Namespace must be greater or equal to 0" );
		}
		if ( !$status->isGood() ) {
			return $status;
		}
		foreach ( $this->registry->getAllKeys() as $key ) {
			$callback = $this->registry->getValue(
				$key
			);
			if ( !is_callable( $callback ) ) {
				$status->warning( "invalid callback for \"$key\"" );
				continue;
			}
			$instance = call_user_func_array(
				$callback,
				[]
			);
			if ( !$instance instanceof ISecondaryDataUpdate ) {
				$status->warning(
					'callback must return instance of "' . ISecondaryDataUpdate::class . '"'
				);
				continue;
			}
			$instanceStatus = $instance->run( $title );
			if ( !$instanceStatus->isOK() ) {
				$status->warning( $instanceStatus->getMessage() );
				continue;
			}
			$status->merge( $instanceStatus );
		}
		try {
			$wikiPage = WikiPage::factory( $title );
			$content = $wikiPage->getContent();
			if ( !$content ) {
				$status->warning( 'WikiPage does not have a content' );
				return $status;
			}
			$updates = $content->getSecondaryDataUpdates( $title );
			foreach ( $updates as $update ) {
				DeferredUpdates::addUpdate( $update, DeferredUpdates::POSTSEND );
			}
		} catch ( Exception $e ) {
			$status->waring( $e->getMessage() );
		}
		return $status;
	}
}
