<?php

namespace BlueSpice\Html\FormField;

use MediaWiki\Html\Html;
use MediaWiki\HTMLForm\HTMLFormField;
use MediaWiki\Json\FormatJson;
use MediaWiki\MediaWikiServices;

class NamespaceMultiselect extends HTMLFormField {
	public const OPTION_HIDE_TALK = 'hide-talk';
	public const OPTION_HIDE_PSEUDO = 'hide-pseudo';
	public const OPTION_ONLY_CUSTOM = 'only-custom-namespaces';
	public const OPTION_BLACKLIST = 'namespace-blacklist';

	/**
	 *
	 * @param string $value
	 * @return string
	 */
	public function getInputHTML( $value ) {
		$this->mParent->getOutput()->addModules( 'ext.bluespice.html.formfields' );

		$container = Html::element(
			'div',
			[
				'class' => $this->getClassString(),
				'data-bs-store-data' => $this->getStoreData()
			]
		);

		$assocField = Html::input(
			$this->mName,
			$value,
			'text',
			[
				'class' => 'bs-html-formfield-hidden'
			]
		);

		return $container . $assocField;
	}

	/**
	 *
	 * @return array
	 */
	protected function getDefaultOptions() {
		return [
			static::OPTION_HIDE_TALK => false,
			static::OPTION_HIDE_PSEUDO => true,
			static::OPTION_ONLY_CUSTOM => false,
			static::OPTION_BLACKLIST => []
		];
	}

	/**
	 *
	 * @return string
	 */
	protected function getClassString() {
		return "{$this->mClass} bs-html-formfield-namespacemultiselect";
	}

	/**
	 *
	 * @return string
	 */
	protected function getStoreData() {
		$options = $this->getOptions();
		$availableNamespaces = [];
		$language = $this->mParent->getLanguage();

		// $language->getNamespaceIds() has namespace name as index.
		// This will resulte in double or tripple entries for one namespace.
		// For e.g. namespace id 6 there will be 'File' and 'Images' in the list.
		$allNamespaces = $language->getNamespaceIds();

		$namespacesInList = [];
		foreach ( $allNamespaces as $lcName => $namespaceId ) {
			if ( in_array( $namespaceId, $namespacesInList ) ) {
				continue;
			}
			$namespacesInList = array_merge(
				$namespacesInList,
				[ $namespaceId ]
			);

			// Create 'BS.model.Namespace' compatible datasets
			// TODO: Add serverside models that are synchron with the JS models
			if ( $namespaceId < 0 && $options[ static::OPTION_HIDE_PSEUDO ] ) {
				continue;
			}

			$namespaceInfo = MediaWikiServices::getInstance()->getNamespaceInfo();
			if ( $namespaceInfo->isTalk( $namespaceId ) && $options[ static::OPTION_HIDE_TALK ] ) {
				continue;
			}

			// TODO: Make threshold of '3000' configureable?
			if ( $namespaceId < 3000 && $options[ static::OPTION_ONLY_CUSTOM ] ) {
				continue;
			}

			if ( in_array( $namespaceId, $options[ static::OPTION_BLACKLIST] ) ) {
				continue;
			}

			$dataSet = [
				'namespaceId' => $namespaceId,
				'namespaceName' => $language->getNsText( $namespaceId ),
				'isNonincludable' => $namespaceInfo->isNonincludable( $namespaceId ),
				'namespaceContentModel' => $namespaceInfo->getNamespaceContentModel( $namespaceId )
			];

			if ( $namespaceId === NS_MAIN ) {
				$dataSet['namespaceName'] = wfMessage( 'bs-ns_main' )->plain();
			}

			$availableNamespaces[] = $dataSet;
		}

		return FormatJson::encode( $availableNamespaces );
	}

	/**
	 *
	 * @return array
	 */
	public function getOptions() {
		$options = parent::getOptions();
		if ( $options === null ) {
			$options = [];
		}

		$customOptions = [
			static::OPTION_BLACKLIST,
			static::OPTION_HIDE_PSEUDO,
			static::OPTION_HIDE_TALK,
			static::OPTION_ONLY_CUSTOM
		];

		foreach ( $customOptions as $customOption ) {
			if ( isset( $this->mParams[ $customOption ] ) ) {
				$options[$customOption] = $this->mParams[ $customOption ];
			}
		}

		return $options + $this->getDefaultOptions();
	}
}
