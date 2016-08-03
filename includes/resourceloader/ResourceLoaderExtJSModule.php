<?php

class ResourceLoaderExtJSModule extends ResourceLoaderModule {

	protected $targets = array( 'desktop', 'mobile' );

	public function getScript(\ResourceLoaderContext $context) {
		$aContents = array();
		$aContents[] = file_get_contents( BSROOTDIR .'/resources/extjs/ext-all-debug.js' );

		$sLangFile = $this->getLanguageFile( $context );
		if( !empty( $sLangFile ) ) {
			$aContents[] = file_get_contents( BSROOTDIR .'/resources/extjs/locale/' . $sLangFile );
		}

		//Since ExtJS gets loaded by RL it may bot be in global scope.
		//So we make ExtJS global available.
		$aContents[] = 'window.Ext = Ext;';

		return implode( ";\n", $aContents );
	}

	public function getScriptURLsForDebug( \ResourceLoaderContext $context ) {
		$extAssetsPath = $this->getConfig()->get( 'ExtensionAssetsPath' );
		$aUrls = array(
			"$extAssetsPath/BlueSpiceFoundation/resources/extjs/ext-all-debug.js"
		);

		$sLangFile = $this->getLanguageFile( $context );
		if( !empty( $sLangFile ) ) {
			$aUrls[] = "$extAssetsPath/BlueSpiceFoundation/resources/extjs/locale/$sLangFile";
		}

		return $aUrls;
	}

	/**
	 *
	 * @param ResourceLoaderContext $context
	 * @return string
	 */
	protected function getLanguageFile( $context ) {
		//Use ExtJS's built-in i18n. This may fail for some languages...
		$aLangCodeMap = $this->makeLanguageCodeMap();
		$sLangCode = $context->getLanguage() === null ? 'en' : $context->getLanguage();

		if( isset( $aLangCodeMap[$sLangCode] ) ) {
			return $aLangCodeMap[$sLangCode];
		}

		return '';
	}

	/**
	 *
	 * @param ResourceLoaderContext $context
	 * @return array
	 */
	public function getDependencies( ResourceLoaderContext $context = null ) {
		return array(
			'ext.bluespice'
		);
	}

	public function getGroup() {
		return 'bsextjs';
	}

	/**
	 * https://www.mediawiki.org/wiki/Manual:Language#/media/File:MediaWiki_fallback_chains.svg
	 * @return array
	 */
	protected function makeLanguageCodeMap() {
		return array(
			'af' => 'ext-lang-af.js',
			'bg' => 'ext-lang-bg.js',
			'ca' => 'ext-lang-ca.js',
			'cs' => 'ext-lang-cs.js',
			'da' => 'ext-lang-da.js',
			'de' => 'ext-lang-de.js',
			'de-at' => 'ext-lang-de.js',
			'de-ch' => 'ext-lang-de.js',
			'de-formal' => 'ext-lang-de.js',
			'el' => 'ext-lang-el_GR.js',
			'en' => 'ext-lang-en.js',
			//'en' => 'ext-lang-en_AU.js',
			'en-gb' => 'ext-lang-en_GB.js',
			'es' => 'ext-lang-es.js',
			'et' => 'ext-lang-et.js',
			'fa' => 'ext-lang-fa.js',
			'fi' => 'ext-lang-fi.js',
			'fr' => 'ext-lang-fr.js',
			'frp' => 'ext-lang-fr.js',
			'frc' => 'ext-lang-fr_CA.js',
			'gr' => 'ext-lang-gr.js',
			'he' => 'ext-lang-he.js',
			'hr' => 'ext-lang-hr.js',
			'hu' => 'ext-lang-hu.js',
			'id' => 'ext-lang-id.js',
			'it' => 'ext-lang-it.js',
			'ja' => 'ext-lang-ja.js',
			'ko' => 'ext-lang-ko.js',
			'lt' => 'ext-lang-lt.js',
			'lv' => 'ext-lang-lv.js',
			'mk' => 'ext-lang-mk.js',
			'nl' => 'ext-lang-nl.js',
			'nb' => 'ext-lang-no_NB.js',
			//'??' => 'ext-lang-no_NN.js',
			'pl' => 'ext-lang-pl.js',
			'pt' => 'ext-lang-pt.js',
			'pt-br' => 'ext-lang-pt_BR.js',
			'pt' => 'ext-lang-pt_PT.js',
			'ro' => 'ext-lang-ro.js',
			'ru' => 'ext-lang-ru.js',
			'sk' => 'ext-lang-sk.js',
			'sl' => 'ext-lang-sl.js',
			'sr' => 'ext-lang-sr.js',
			//'sr-el' => 'ext-lang-sr_RS.js',
			//'sr-ec' => 'ext-lang-sv_SE.js',
			'th' => 'ext-lang-th.js',
			'tr' => 'ext-lang-tr.js',
			'ur' => 'ext-lang-ukr.js',
			//'??' => 'ext-lang-vn.js',
			'zh' => 'ext-lang-zh_CN.js',
			'zh-hans' => 'ext-lang-zh_CN.js',
			'zh-hant' => 'ext-lang-zh_CN.js',
			'zh-sg' => 'ext-lang-zh_CN.js',
			'zh-tw' => 'ext-lang-zh_TW.js'
		);
	}
}