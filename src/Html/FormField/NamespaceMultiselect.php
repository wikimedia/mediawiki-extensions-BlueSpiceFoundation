<?php

namespace BlueSpice\Html\FormField;

class NamespaceMultiselect extends \HTMLFormField {
	const OPTION_HIDE_TALK = 'hide-talk';
	const OPTION_HIDE_PSEUDO = 'hide-pseudo';
	const OPTION_ONLY_CUSTOM = 'only-custom-namespaces';
	const OPTION_BLACKLIST = 'namespace-blacklist';

	public function getInputHTML( $value ) {
		$this->mParent->getOutput()->addModules( 'ext.bluespice.html.formfields' );

		$container = \Html::element(
			'div',
			[
				'class' => $this->getClassString(),
				'data-bs-store-data' => $this->getStoreData()
			]
		);

		$assocField = \Html::input(
			$this->mName,
			$value,
			'text',
			[
				'class' => 'bs-html-formfield-hidden'
			]
		);

		return $container.$assocField;
	}

	protected function getDefaultOptions() {
		return [
			static::OPTION_HIDE_TALK => false,
			static::OPTION_HIDE_PSEUDO => true,
			static::OPTION_ONLY_CUSTOM => false,
			static::OPTION_BLACKLIST => []
		];
	}

	protected function getClassString() {
		return "{$this->mClass} bs-html-formfield-namespacemultiselect";
	}

	protected function getStoreData() {
		$options = $this->getOptions();
		$availableNamespaces = [];
		$language = $this->mParent->getLanguage();

		$allNamespaces = $language->getNamespaceIds();
		foreach( $allNamespaces as $lcName => $namespaceId ) {
			//Create 'BS.model.Namespace' compatible datasets
			//TODO: Add serverside models that are synchron with the JS models
			;
			if( $namespaceId < 0 && $options[ static::OPTION_HIDE_PSEUDO ] ) {
				continue;
			}

			if( \MWNamespace::isTalk( $namespaceId ) && $options[ static::OPTION_HIDE_TALK ] ) {
				continue;
			}

			//TODO: Make threshold of '3000' configureable?
			if( $namespaceId < 3000 && $options[ static::OPTION_ONLY_CUSTOM ] ) {
				continue;
			}

			if( in_array( $namespaceId, $options[ static::OPTION_BLACKLIST] ) ) {
				continue;
			}

			$dataSet = [
				'namespaceId' => $namespaceId,
				'namespaceName' => $language->getNsText( $namespaceId ),
				'isNonincludable' => \MWNamespace::isNonincludable( $namespaceId ),
				'namespaceContentModel' => \MWNamespace::getNamespaceContentModel( $namespaceId )
			];

			if( $namespaceId === NS_MAIN ) {
				$dataSet['namespaceName'] = wfMessage( 'bs-ns_main' )->plain();
			}

			$availableNamespaces[] = $dataSet;
		}

		return \FormatJson::encode( $availableNamespaces );
	}

	public function getOptions() {
		$options = parent::getOptions();
		if( $options === null ) {
			$options = [];
		}

		$customOptions = [
			static::OPTION_BLACKLIST,
			static::OPTION_HIDE_PSEUDO,
			static::OPTION_HIDE_TALK,
			static::OPTION_ONLY_CUSTOM
		];

		foreach( $customOptions as $customOption ) {
			if( isset( $this->mParams[ $customOption ] ) ) {
				$options[$customOption] = $this->mParams[ $customOption ];
			}
		}

		return $options + $this->getDefaultOptions();
	}
}
