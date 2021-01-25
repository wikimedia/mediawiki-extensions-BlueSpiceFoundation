<?php

namespace BlueSpice\Hook\ChameleonSkinTemplateOutputPageBeforeExec;

use BlueSpice\Hook\ChameleonSkinTemplateOutputPageBeforeExec;
use BlueSpice\SkinData;
use MediaWiki\MediaWikiServices;

class AddExportDownloadFile extends ChameleonSkinTemplateOutputPageBeforeExec {

	protected function skipProcessing() {
		if ( $this->skin->getTitle()->getNamespace() != NS_FILE ) {
			return true;
		}

		$file = MediaWikiServices::getInstance()->getRepoGroup()->getLocalRepo()
			->newFile( $this->skin->getTitle() );
		if ( !$file ) {
			return true;
		}

		if ( $file->getHandler() instanceof \BitmapHandler ) {
			return true;
		}

		return false;
	}

	protected function doProcess() {
		$file = MediaWikiServices::getInstance()->getRepoGroup()->getLocalRepo()
			->newFile( $this->skin->getTitle() );
		$this->mergeSkinDataArray( SkinData::EXPORT_MENU, [
			60 => [
				'id' => 'bs-em-filedownload',
				'href' => $file->getFullUrl(),
				'title' => $file->getName(),
				'text' => wfMessage( 'bs-imagepage-download-text' )->plain(),
				'class' => 'bs-ue-export-link',
				'iconClass' => 'icon-download'
			]
		] );
		return true;
	}

}
