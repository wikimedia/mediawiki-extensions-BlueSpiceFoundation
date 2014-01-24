<?php

class BsFileSystemHelper {

	/**
	 *
	 * @param string $sSubDirName
	 * @return Status 
	 */
	public static function ensureCacheDirectory($sSubDirName = '') {
		wfProfileIn(__METHOD__);
		if (self::hasTraversal($sSubDirName))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		if (!empty($sSubDirName) && !preg_match('#^[a-zA-Z/\\\]+#', $sSubDirName)) {
			wfProfileOut(__METHOD__);
			return Status::newFatal('Requested subdirectory of ' . BS_DATA_DIR . ' contains illegal chars');
		}
		if (!is_dir(BS_CACHE_DIR)) {
			if (!mkdir(BS_CACHE_DIR, 0777, true)) {
				wfProfileOut(__METHOD__);
				return Status::newFatal(BS_CACHE_DIR . ' is not accessible');
			}
		}
		if (empty($sSubDirName)) {
			wfProfileOut(__METHOD__);
			return Status::newGood(BS_CACHE_DIR);
		} elseif (is_dir(BS_CACHE_DIR . '/' . $sSubDirName)) {
			wfProfileOut(__METHOD__);
			return Status::newGood(BS_CACHE_DIR . '/' . $sSubDirName);
		}

		if (!mkdir(BS_CACHE_DIR . '/' . $sSubDirName, 0777, true)) {
			wfProfileOut(__METHOD__);
			return Status::newFatal(BS_CACHE_DIR . ' is not accessible');
		}
	}

	/**
	 *
	 * @param string $sSubDirName
	 * @return Status 
	 */
	public static function ensureDataDirectory($sSubDirName = '') {
		wfProfileIn(__METHOD__);
		if (self::hasTraversal($sSubDirName))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		if (!empty($sSubDirName) && !preg_match('#^[a-zA-Z/\\\]+#', $sSubDirName)) {
			wfProfileOut(__METHOD__);
			return Status::newFatal('Requested subdirectory of ' . BS_DATA_DIR . ' contains illegal chars');
		}
		if (!is_dir(BS_DATA_DIR)) {
			if (!mkdir(BS_DATA_DIR, 0777, true)) {
				wfProfileOut(__METHOD__);
				return Status::newFatal(BS_DATA_DIR . ' is not accessible');
			}
		}

		if (empty($sSubDirName)) {
			wfProfileOut(__METHOD__);
			return Status::newGood(BS_DATA_DIR);
		} elseif (is_dir(BS_DATA_DIR . '/' . $sSubDirName)) {
			wfProfileOut(__METHOD__);
			return Status::newGood(BS_DATA_DIR . '/' . $sSubDirName);
		}
		if (!mkdir(BS_DATA_DIR . '/' . $sSubDirName, 0777, true)) {
			wfProfileOut(__METHOD__);
			return Status::newFatal(BS_DATA_DIR . ' is not accessible');
		}

		wfProfileOut(__METHOD__);
		return Status::newGood(BS_DATA_DIR . '/' . $sSubDirName);
	}

	/**
	 *
	 * @param string $sSubDirName
	 * @param mixed $data
	 * @return Status 
	 */
	public static function saveToCacheDirectory($sFileName, $data, $sSubDirName = '') {
		wfProfileIn(__METHOD__);
		$oStatus = self::ensureCacheDirectory($sSubDirName);
		if (self::hasTraversal($sSubDirName . DS . $sFileName))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		if (!$oStatus->isGood()) {
			wfProfileOut(__METHOD__);
			return $oStatus;
		}

		if (!file_put_contents($oStatus->getValue() . DS . $sFileName, $data)) {
			wfProfileOut(__METHOD__);
			return Status::newFatal('could not save "' . $sFileName . '" to location: ' . $oStatus->getValue() . '/' . $sFileName);
		}

		wfProfileOut(__METHOD__);
		return $oStatus;
	}

	/**
	 *
	 * @param string $sSubDirName
	 * @param mixed $data
	 * @return Status 
	 */
	public static function saveToDataDirectory($sFileName, $data, $sSubDirName = '') {
		wfProfileIn(__METHOD__);
		$oStatus = self::ensureDataDirectory($sSubDirName);
		if (self::hasTraversal($sSubDirName . DS . $sFileName))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		if (!$oStatus->isGood()) {
			wfProfileOut(__METHOD__);
			return $oStatus;
		}

		//todo: via FileRepo
		if (!file_put_contents($oStatus->getValue() . DS . $sFileName, $data)) {
			wfProfileOut(__METHOD__);
			return Status::newFatal('could not save "' . $sFileName . '" to location: ' . $oStatus->getValue() . '/' . $sFileName);
		}

		wfProfileOut(__METHOD__);
		return $oStatus;
	}

	/**
	 *
	 * @param string $sSubDirName
	 * @return string Filepath 
	 */
	public static function getDataDirectory($sSubDirName = '') {
		$sDataDir = ( $sSubDirName ) ? BS_DATA_DIR . DS . $sSubDirName : BS_DATA_DIR;
		return $sDataDir;
	}

	/**
	 *
	 * @param string $sSubDirName
	 * @return string URL 
	 */
	public static function getDataPath($sSubDirName = '') {
		$sDataPath = ( $sSubDirName ) ? BS_DATA_PATH . DS . $sSubDirName : BS_DATA_PATH;
		return $sDataPath;
	}

	/**
	 *
	 * @param string $sSubDirName
	 * @return string Filepath 
	 */
	public static function getCacheDirectory($sSubDirName = '') {
		$sCacheDir = ( $sSubDirName ) ? BS_CACHE_DIR . '/' . $sSubDirName : BS_CACHE_DIR;
		return $sCacheDir;
	}

	/**
	 * HINT: http://fr2.php.net/manual/en/function.copy.php#91010
	 * @param string $sSource
	 * @param type $sDestination
	 */
	public static function copyRecursive($sSource, $sDestination) {
		if (self::hasTraversal($sSource) || self::hasTraversal($sDestination))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		$rDir = opendir($sSource);
		wfMkdirParents($sDestination);
		while (false !== ( $sFileName = readdir($rDir))) {
			if (( $sFileName != '.' ) && ( $sFileName != '..' )) {
				if (is_dir($sSource . '/' . $sFileName)) {
					self::copyRecursive($sSource . '/' . $sFileName, $sDestination . '/' . $sFileName);
				} else {
					copy($sSource . '/' . $sFileName, $sDestination . '/' . $sFileName);
				}
			}
		}
		closedir($rDir);
	}

	/**
	 * Get a file object from a repo by the name of the repo
	 * @param String $sFileName
	 * @param String $sRepoName
	 * @return boolean|\File
	 */
	public static function getFileFromRepoName($sFileName, $sRepoName) {
		$oFileRepo = RepoGroup::singleton()->getRepoByName($sRepoName);
		if (!$oFileRepo instanceof FileRepo)
			return false;
		$oFile = $oFileRepo->newFile($sFileName);
		if (!$oFile instanceof File)
			return false;
		return $oFile;
	}

	/**
	 * Get the content of a file
	 * @param String $sFileName
	 * @param String $sDir
	 * @return String The file's content.
	 */
	public static function getFileContent($sFileName, $sDir) {
		if (self::hasTraversal($sDir . DS . $sFileName))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		if (!is_dir(BS_DATA_DIR . DS . $sDir))
			return Status::newFatal(wfMessage("bs-filesystemhelper-no-directory", $sDir)->plain());
		if (!file_exists(BS_DATA_DIR . DS . $sDir . DS . $sFileName))
			return Status::newFatal(wfMessage("bs-filesystemhelper-file-not-exists", $sFileName)->plain());
		$sFile = file_get_contents(BS_DATA_DIR . DS . $sDir . DS . $sFileName);
		return Status::newGood($sFile);
	}

	/**
	 * Copies a file defined by name from a source to a destination folder
	 * @param String $sFileName
	 * @param String $sSource
	 * @param String $sDestination
	 * @return Status good on success, otherwise fatal with message
	 */
	public static function copyFile($sFileName, $sSource, $sDestination, $bOverwrite = true) {
		if (self::hasTraversal($sSource . DS . $sFileName) || self::hasTraversal($sDestination . DS . $sFileName))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		if (!is_dir(BS_DATA_DIR . DS . $sSource))
			return Status::newFatal(wfMessage("bs-filesystemhelper-no-directory", BS_DATA_DIR . DS . $sSource)->plain());
		if (!is_dir(BS_DATA_DIR . DS . $sDestination))
			return Status::newFatal(wfMessage("bs-filesystemhelper-no-directory", BS_DATA_DIR . DS . $sDestination)->plain());
		if (!file_exists(BS_DATA_DIR . DS . $sSource . DS . $sFileName))
			return Status::newFatal(wfMessage("bs-filesystemhelper-file-not-exists", $sFileName)->plain());
		if (file_exists(BS_DATA_DIR . DS . $sSource . DS . $sFileName) && !$bOverwrite)
			return Status::newFatal(wfMessage("bs-filesystemhelper-file-already-exists", $sFileName)->plain());
		$bStatus = copy(BS_DATA_DIR . DS . $sSource . DS . $sFileName, BS_DATA_DIR . DS . $sDestination . DS . $sFileName);
		if ($bStatus)
			return Status::newGood();
		else
			return Status::newFatal(wfMessage("bs-filesystemhelper-file-copy-error", $sFileName)->plain());
	}

	public static function copyFolder($sFolderName, $sSource, $sDestination, $bOverwrite = true) {
		if (self::hasTraversal($sSource . DS . $sFolderName) || self::hasTraversal($sDestination . DS . $sFolderName))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		if (!is_dir(BS_DATA_DIR . DS . $sSource))
			return Status::newFatal(wfMessage("bs-filesystemhelper-no-directory", BS_DATA_DIR . DS . $sSource)->plain());
		if (!is_dir(BS_DATA_DIR . DS . $sDestination))
			return Status::newFatal(wfMessage("bs-filesystemhelper-no-directory", BS_DATA_DIR . DS . $sDestination)->plain());
		if (!file_exists(BS_DATA_DIR . DS . $sSource . DS . $sFolderName))
			return Status::newFatal(wfMessage("bs-filesystemhelper-folder-not-exists", $sFolderName)->plain());
		if (file_exists(BS_DATA_DIR . DS . $sSource . DS . $sFolderName) && !$bOverwrite)
			return Status::newFatal(wfMessage("bs-filesystemhelper-folder-already-exists", $sFolderName)->plain());
		$it = new RecursiveDirectoryIterator(BS_DATA_DIR . DS . $sSource . DS . $sFolderName);
		BsFileSystemHelper::ensureDataDirectory($sDestination . DS . $sFolderName);
		$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($files as $file) {
			if ($file->getFilename() === '.' || $file->getFilename() === '..') {
				continue;
			}
			if ($file->isDir())
				mkdir($file->getRealPath(), 0777);
			if ($file->isFile()) {
				$bStatus = copy(BS_DATA_DIR . DS . $sSource . DS . $sFolderName . DS . $file->getFileName(), BS_DATA_DIR . DS . $sDestination . DS . $sFolderName . DS . $file->getFileName());
				if (!$bStatus)
					return Status::newFatal(wfMessage("bs-filesystemhelper-folder-copy-error", $file->getFileName())->plain());
			}
		}
		return Status::newGood();
	}

	/**
	 * Rename a folder
	 * @param String $sSource
	 * @param String $sDestination
	 * @param boolean $bOverwrite
	 * @return Status good on success, otherwise fatal with message
	 */
	public static function renameFolder($sSource, $sDestination, $bOverwrite = true) {
		if (self::hasTraversal($sSource) || self::hasTraversal($sDestination))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		if (!is_dir(BS_DATA_DIR . DS . $sSource))
			return Status::newFatal(wfMessage("bs-filesystemhelper-no-directory", BS_DATA_DIR . DS . $sSource)->plain());
		if (file_exists(BS_DATA_DIR . DS . $sDestination) && !$bOverwrite)
			return Status::newFatal(wfMessage("bs-filesystemhelper-folder-already-exists", $sDestination)->plain());
		$bStatus = rename(BS_DATA_DIR . DS . $sSource, BS_DATA_DIR . DS . $sDestination);
		if ($bStatus)
			return Status::newGood();
		else
			return Status::newFatal(wfMessage("bs-filesystemhelper-folder-rename-error", $sSource)->plain());
	}

	/**
	 * Deletes a file
	 * @param String $sFile
	 * @param String $sDir
	 * @return Status good on success, otherwise fatal with message
	 */
	public static function deleteFile($sFile, $sDir) {
		if (self::hasTraversal($sDir . DS . $sFile))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		if (!is_dir(BS_DATA_DIR . DS . $sDir))
			return Status::newFatal(wfMessage("bs-filesystemhelper-folder-not-exists", $sFile)->plain());
		$bStatus = unlink(BS_DATA_DIR . DS . $sDir . DS . $sFile);
		if ($bStatus)
			return Status::newGood();
		else
			return Status::newFatal(wfMessage("bs-filesystemhelper-file-delete-error", $sFile)->plain());
	}

	/**
	 * Deletes a folder with all its content
	 * @param String $sDir
	 * @return Status good on success, otherwise fatal with message
	 */
	public static function deleteFolder($sDir, $bIfExists = false) {
		if ($bIfExists && !file_exists(BS_DATA_DIR . DS . $sDir))
			return Status::newGood();
		if (self::hasTraversal($sDir))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		if (!file_exists(BS_DATA_DIR . DS . $sDir))
			return Status::newFatal(wfMessage("bs-filesystemhelper-folder-not-exists", $sDir)->plain());
		//hint: http://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
		$it = new RecursiveDirectoryIterator(BS_DATA_DIR . DS . $sDir);
		$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($files as $file) {
			if ($file->getFilename() === '.' || $file->getFilename() === '..') {
				continue;
			}
			if ($file->isDir()) {
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
		rmdir(BS_DATA_DIR . DS . $sDir);
		return Status::newGood();
	}

	/**
	 * 
	 * @global type $wgRequest
	 * @param type $sName
	 * @param type $sDir
	 * @param type $sFileName
	 * @param type $sRequiredExtension
	 * @return type
	 * @throws MWException
	 */
	public static function uploadFile($sName, $sDir, $sFileName = '', $sRequiredExtension = '') {
		global $wgRequest;
		$oWebRequest = new WebRequest();
		$oWebRequestUpload = $oWebRequest->getUpload($sName);
		$oUploadFromFile = new UploadFromFile();
		$oUploadFromFile->initialize($wgRequest->getVal('name'), $oWebRequestUpload);
		$aStatus = $oUploadFromFile->verifyUpload();
		if ($aStatus['status'] != 0) {
			return Status::newFatal(wfMessage('bs-filesystemhelper-upload-err-code', '{{int:' . UploadBase::getVerificationErrorCode($aStatus['status']) . '}}')->parse());
		}
		$sRemoteFileName = $oWebRequestUpload->getName();
		$sRemoteFileExt = pathinfo($sRemoteFileName, PATHINFO_EXTENSION);
		if ($sRequiredExtension && strtolower($sRemoteFileExt) != strtolower($sRequiredExtension)) {
			return Status::newFatal(wfMessage('bs-filesystemhelper-upload-wrong-ext', $sRequiredExtension));
		}
		$oStatus = self::ensureDataDirectory($sDir);
		if (!$oStatus->isGood())
			return $oStatus;

		$sTmpName = BS_DATA_DIR . DS . $sDir . DS;
		$sTmpName .= ($sFileName) ? $sFileName : $sRemoteFileName;
		if (self::hasTraversal($sTmpName, true))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		move_uploaded_file($oWebRequestUpload->getTempName(), $sTmpName);
		return Status::newGood($oWebRequestUpload->getName());
	}

	/**
	 * Converts uploaded image to PNG
	 * @global type $wgRequest
	 * @param type $sName
	 * @param type $sDir
	 * @param type $sFileName
	 * @return type
	 */
	public static function uploadAndConvertImage($sName, $sDir, $sFileName = '') {
		global $wgRequest;
		$oWebRequest = new WebRequest();
		$oWebRequestUpload = $oWebRequest->getUpload($sName);
		$oUploadFromFile = new UploadFromFile();
		$oUploadFromFile->initialize($wgRequest->getVal('name'), $oWebRequestUpload);
		$aStatus = $oUploadFromFile->verifyUpload();

		if ($aStatus['status'] != 0) {
			return Status::newFatal(wfMessage('bs-filesystemhelper-upload-err-code', '{{int:' . UploadBase::getVerificationErrorCode($aStatus['status']) . '}}')->parse());
		}

		$sRemoteFileName = $oWebRequestUpload->getName();
		/*
		  $sRemoteFileExt = pathinfo($sRemoteFileName, PATHINFO_EXTENSION);
		  if ($sRequiredExtension && strtolower($sRemoteFileExt) != strtolower($sRequiredExtension)) {
		  return Status::newFatal(wfMessage('bs-filesystemhelper-upload-wrong-ext', $sRequiredExtension));
		  }
		 */

		$oStatus = self::ensureDataDirectory($sDir);
		if (!$oStatus->isGood())
			return $oStatus;

		$sTmpName = BS_DATA_DIR . DS . $sDir . DS;
		$sTmpName .= ($sFileName) ? $sFileName : $sRemoteFileName;
		if (self::hasTraversal($sTmpName, true))
			return Status::newFatal(wfMessage("bs-filesystemhelper-has-path-traversal")->plain());
		$sUploadPath = $oWebRequestUpload->getTempName();
		list($iWidth, $iHeight, $iType) = getimagesize($sUploadPath);
		switch ($iType) {
			case IMAGETYPE_GIF:
				$rImage = imagecreatefromgif($sUploadPath);
				break;
			case IMAGETYPE_JPEG:
				$rImage = imagecreatefromjpeg($sUploadPath);
				break;
			case IMAGETYPE_PNG:
				$rImage = imagecreatefrompng($sUploadPath);
				break;
			default:
				return Status::newFatal(wfMessage('bs-filesystemhelper-upload-unsupported-type')->plain());
		}

		$iNewWidth = $iNewHeight = BsConfig::get('MW::Avatars::DefaultSize');
		$fRatio = $iWidth / $iHeight;
		if ($fRatio < 1) {
			$iNewWidth = $iNewHeight * $fRatio; # portrait
		} else {
			$iNewHeight = $iNewWidth / $fRatio; # landscape
		}
		$rNewImage = imagecreatetruecolor($iNewWidth, $iNewHeight);
		imagecopyresampled($rNewImage, $rImage, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iWidth, $iHeight);
		imagepng($rNewImage, $sTmpName);
		imagedestroy($rNewImage);

		#move_uploaded_file($oWebRequestUpload->getTempName(), $sTmpName);
		return Status::newGood($oWebRequestUpload->getName());
	}

	/**
	 * Do a proper traversal check if $sPath exists, a string check otherwise
	 * @param string $sPath Filepath
	 * @return bool
	 */
	public static function hasTraversal($sPath, $bIsAbsolute = false) {
		if (!$sPath)
			return true; // BS_DATA_DIR without trailing DS. Bail out.
		$sCheckPath = ($bIsAbsolute ? '' : BS_DATA_DIR . DS) . $sPath;
		if (file_exists($sCheckPath)) {
			return (strpos(realpath($sCheckPath), BS_DATA_DIR . DS) !== 0);
		} else {
			return (self::normalizePath($sCheckPath) === null);
		}
	}

	/**
	 * Taken from MW FileBackend::normalizeContainerPath()
	 * @param string $path
	 * @return null or sting
	 */
	public static function normalizePath($path) {
		// Normalize directory separators
		$path = strtr($path, '\\', '/');
		// Collapse any consecutive directory separators
		$path = preg_replace('![/]{2,}!', '/', $path);
		// Remove any leading directory separator
		$path = ltrim($path, '/');
		// Use the same traversal protection as Title::secureAndSplit()
		if (strpos($path, '.') !== false) {
			if (
					$path === '.' ||
					$path === '..' ||
					strpos($path, './') === 0 ||
					strpos($path, '../') === 0 ||
					strpos($path, '/./') !== false ||
					strpos($path, '/../') !== false
			) {
				return null;
			}
		}
		return $path;
	}

	public static function saveBase64ToTmp($sFileName, $sFileContent){
		$sFileName = wfTempDir() . DS . basename($sFileName);
		
		$sFileContent = preg_replace("#data:.*;base64,#", "", $sFileContent);
		$sFileContent = str_replace(' ', '+', $sFileContent);
		$sFileContent = base64_decode($sFileContent);
		$bFile = file_put_contents($sFileName, $sFileContent);
		if ($bFile === 0 || $bFile === false)
			return Status::newFatal(wfMessage('bs-filesystemhelper-save-base64-error')->plain());
		else
			return Status::newGood($sFileName);
	}
	
	public static function uploadLocalFile($sFilename, $bDeleteSrc = false, $sComment = "", $sPageText = "", $bWatch = false){
		global $wgLocalFileRepo, $wgUser;
		$oUploadStash = new UploadStash(new LocalRepo($wgLocalFileRepo));
		$oFile = $oUploadStash->stashFile($sFilename, "file");
		if ($oFile === false)
			return Status::newFailure(wfMessage('bs-filesystemhelper-upload-local-error-stash-file')->plain());
		$oUploadFromStash = new UploadFromStash($wgUser, $oUploadStash, $wgLocalFileRepo);
		$oUploadFromStash->initialize($oFile->getFileKey(), basename($sFilename));
		$aStatus = $oUploadFromStash->verifyUpload();
		if ($aStatus['status'] != UploadBase::OK) {
			return Status::newFatal(wfMessage('bs-filesystemhelper-upload-err-code', '{{int:' . UploadBase::getVerificationErrorCode($aStatus['status']) . '}}')->parse());
		}
		$status = $oUploadFromStash->performUpload($sComment, $sPageText, $bWatch, $wgUser);
		$oUploadFromStash->cleanupTempFile();
		
		if (file_exists($sFilename) && $bDeleteSrc)
			unlink($sFilename);
		$oFile = wfFindFile(basename($sFilename));
		if ($status->isGood() && $oFile !== false){
			if ( BsExtensionManager::isContextActive( 'MW::SecureFileStore::Active' ) ) {
				return Status::newGood(SecureFileStore::secureStuff($oFile->getUrl(), true));
			}
			else
				return Status::newGood($oFile->getUrl(), true);
		}
		else
			return Status::newFatal (wfMessage('bs-filesystemhelper-upload-local-error-create')->plain());
	}
	
}
