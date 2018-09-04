<?php

namespace BlueSpice\Hook\SkinTemplateOutputPageBeforeExec;

use BlueSpice\SkinData;

class AddExportDownloadFile extends \BlueSpice\Hook\SkinTemplateOutputPageBeforeExec {

	protected function skipProcessing() {
		if( $this->skin->getTitle()->getNamespace() != NS_FILE ) {
			return true;
		}

		if( !$file = wfFindFile( $this->skin->getTitle() ) ) {
			return true;
		}

		if( $file->getHandler() instanceof \BitmapHandler ) {
			return true;
		}

		return false;
	}

	protected function doProcess() {
		$file = wfFindFile( $this->skin->getTitle() );
		$this->mergeSkinDataArray( SkinData::EXPORT_MENU, [
			60 => [
				'id' => 'bs-em-filedownload',
				'href' => $file->getFullUrl(),
				'title' => $file->getName(),
				'text' => wfMessage( 'bs-imagepage-download-text' )->plain(),
				'class' => 'bs-ue-export-link',
				'iconClass' => 'icon-download'
			]
		]);
		return true;
	}

}
