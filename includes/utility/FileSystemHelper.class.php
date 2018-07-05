<?php

class BsFileSystemHelper {

	/**
	 * Checks if given directory within BS_CACHE_DIR exists and creates it if not
	 * @param string $sSubDirName
	 * @return Status
	 */
	public static function ensureCacheDirectory($sSubDirName = '') {
		if ( self::hasTraversal( $sSubDirName ) ) {
			return Status::newFatal( wfMessage( "bs-filesystemhelper-has-path-traversal" ) );
		}
		if (!empty($sSubDirName) && !preg_match('#^[a-zA-Z/\\\]+#', $sSubDirName)) {
			return Status::newFatal('Requested subdirectory of ' . BS_CACHE_DIR . ' contains illegal chars');
		}
		if (!is_dir(BS_CACHE_DIR)) {
			if (!mkdir(BS_CACHE_DIR, 0777, true)) {
				return Status::newFatal(BS_CACHE_DIR . ' is not accessible');
			}
		}
		if (empty($sSubDirName)) {
			return Status::newGood(BS_CACHE_DIR);
		} elseif ( is_dir( BS_CACHE_DIR . "/$sSubDirName" ) ) {
			return Status::newGood( BS_CACHE_DIR . "/$sSubDirName" );
		}

		if (!mkdir(BS_CACHE_DIR . "/$sSubDirName", 0777, true)) {
			return Status::newFatal(BS_CACHE_DIR . ' is not accessible');
		}

		return Status::newGood( BS_CACHE_DIR . "/$sSubDirName" );
	}

	/**
	 * Checks if given directory within BS_DATA_DIR exists and creates it if not
	 * @param string $sSubDirName
	 * @return Status
	 */
	public static function ensureDataDirectory($sSubDirName = '') {
		if ( self::hasTraversal( $sSubDirName ) ) {
			return Status::newFatal( wfMessage( "bs-filesystemhelper-has-path-traversal" ) );
		}
		if (!empty($sSubDirName) && !preg_match('#^[a-zA-Z/\\\]+#', $sSubDirName)) {
			return Status::newFatal('Requested subdirectory of ' . BS_DATA_DIR . ' contains illegal chars');
		}
		if (!is_dir(BS_DATA_DIR)) {
			if (!mkdir(BS_DATA_DIR, 0777, true)) {
				return Status::newFatal(BS_DATA_DIR . ' is not accessible');
			}
		}

		$sFullPath = strpos( $sSubDirName, BS_DATA_DIR ) === 0 ? $sSubDirName : BS_DATA_DIR . "/$sSubDirName";
		if ( empty( $sFullPath ) ) {
			return Status::newGood(BS_DATA_DIR);
		} elseif ( is_dir( $sFullPath ) ) {
			return Status::newGood( $sFullPath );
		}
		if ( !mkdir( $sFullPath, 0777, true ) ) {
			return Status::newFatal(BS_DATA_DIR . ' is not accessible');
		}

		return Status::newGood( $sFullPath );
	}

	/**
	 * Saves a file to a subdirectory of BS_CACHE_DIR
	 * @param string $sSubDirName
	 * @param mixed $data
	 * @return Status
	 */
	public static function saveToCacheDirectory($sFileName, $data, $sSubDirName = '') {
		$oStatus = self::ensureCacheDirectory($sSubDirName);
		if ( self::hasTraversal( "$sSubDirName/$sFileName" ) ) {
			return Status::newFatal( wfMessage( "bs-filesystemhelper-has-path-traversal" ) );
		}
		if (!$oStatus->isGood()) {
			return $oStatus;
		}

		if (!file_put_contents( "{$oStatus->getValue()}/$sFileName", $data ) ) {
			return Status::newFatal(
				"could not save \"$sFileName\" to location: {$oStatus->getValue()}/$sFileName"
			);
		}

		return $oStatus;
	}

	/**
	 * Saves a file to a subdirectory of BS_DATA_DIR
	 * @param string $sSubDirName
	 * @param mixed $data
	 * @return Status
	 */
	public static function saveToDataDirectory($sFileName, $data, $sSubDirName = '') {
		$oStatus = self::ensureDataDirectory($sSubDirName);
		if ( self::hasTraversal( "$sSubDirName/$sFileName" ) ) {
			return Status::newFatal( wfMessage( "bs-filesystemhelper-has-path-traversal" ) );
		}

		if (!$oStatus->isGood()) {
			return $oStatus;
		}

		if (!file_put_contents( "{$oStatus->getValue()}/$sFileName", $data ) ) {
			return Status::newFatal(
				"could not save \"$sFileName\" to location: {$oStatus->getValue()}/$sFileName"
			);
		}

		$repo = \RepoGroup::singleton()->getRepoByName( $sSubDirName );

		// This is not usable! Used for invalidating cache.
		$repo->quickImport(
			"",
			""
		);

		return $oStatus::newGood( static::getFileFromRepoName( $sFileName, $sSubDirName ) );
	}

	/**
	 * Returns an absolute filesystem path to a subdirecotry of BS_DATA_DIR
	 * @param string $sSubDirName
	 * @return string Filepath
	 */
	public static function getDataDirectory($sSubDirName = '') {
		return empty( $sSubDirName )
			? BS_DATA_DIR
			: BS_DATA_DIR . "/$sSubDirName";
	}

	/**
	 * Returns a web path to a subdirecotry of BS_DATA_PATH
	 * @param string $sSubDirName
	 * @return string URL
	 */
	public static function getDataPath( $sSubDirName = '' ) {
		return empty( $sSubDirName ) ? BS_DATA_PATH : BS_DATA_PATH . '/' . $sSubDirName;
	}

	/**
	 * Returns an absolute filesystem path to a subdirecotry of BS_CACHE_DIR
	 * @param string $sSubDirName
	 * @return string Filepath
	 */
	public static function getCacheDirectory( $sSubDirName = '' ) {
		return empty( $sSubDirName )
			? BS_CACHE_DIR
			: BS_CACHE_DIR . "/$sSubDirName";
	}

	/**
	 * HINT: http://fr2.php.net/manual/en/function.copy.php#91010
	 * @param string $sSource
	 * @param string $sDestination
	 * @return Status
	 */
	public static function copyRecursive($sSource, $sDestination) {
		if (self::hasTraversal($sSource) || self::hasTraversal($sDestination))
			return Status::newFatal( wfMessage( "bs-filesystemhelper-has-path-traversal" ) );
		$rDir = opendir($sSource);
		wfMkdirParents($sDestination);
		while (false !== ( $sFileName = readdir($rDir))) {
			if (( $sFileName != '.' ) && ( $sFileName != '..' )) {
				if ( is_dir( "$sSource/$sFileName" ) ) {
					self::copyRecursive(
						"$sSource/$sFileName",
						"$sDestination/$sFileName"
					);
				} else {
					copy(
						"$sSource/$sFileName",
						"$sDestination/$sFileName"
					);
				}
			}
		}
		closedir($rDir);
		return Status::newGood();
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
	 * Get the content of a file in data directory
	 * @param String $sFileName
	 * @param String $sDir
	 * @return Status (->getValue() for the file's content).
	 */
	public static function getFileContent($sFileName, $sDir) {
		if ( self::hasTraversal( "$sDir/$sFileName" ) )
			return Status::newFatal( wfMessage( "bs-filesystemhelper-has-path-traversal" ) );
		if ( !is_dir( BS_DATA_DIR . "/$sDir" ) )
			return Status::newFatal( wfMessage( "bs-filesystemhelper-no-directory", $sDir ) );
		if ( !file_exists( BS_DATA_DIR . "/$sDir/$sFileName" ) )
			return Status::newFatal( wfMessage( "bs-filesystemhelper-file-not-exists", $sFileName ) );
		$sFile = file_get_contents( BS_DATA_DIR . "/$sDir/$sFileName" );
		return Status::newGood($sFile);
	}

	/**
	 * Get the content of a file in cache directory
	 * @param String $sFileName
	 * @param String $sDir
	 * @return Status (->getValue() for the file's content).
	 */
	public static function getCacheFileContent($sFileName, $sDir) {
		if ( self::hasTraversal( $sDir . "/$sFileName" ) )
			return Status::newFatal( wfMessage( "bs-filesystemhelper-has-path-traversal" ) );
		if ( !is_dir( BS_CACHE_DIR . "/$sDir" ) )
			return Status::newFatal( wfMessage( "bs-filesystemhelper-no-directory", $sDir ) );
		if ( !file_exists( BS_CACHE_DIR . "/$sDir/$sFileName" ) )
			return Status::newFatal( wfMessage( "bs-filesystemhelper-file-not-exists", $sFileName ) );
		$sFile = file_get_contents( BS_CACHE_DIR . "/$sDir/$sFileName" );
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

		if ( self::hasTraversal( "$sSource/$sFileName" ) || self::hasTraversal( "$sDestination/$sFileName" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-has-path-traversal"
			));
		}

		if ( !is_dir( BS_DATA_DIR . "/$sSource" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-no-directory",
				BS_DATA_DIR . "/$sSource"
			));
		}

		if ( !is_dir( BS_DATA_DIR . "/$sDestination" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-no-directory",
				BS_DATA_DIR . "/$sDestination"
			));
		}

		if ( !file_exists( BS_DATA_DIR . "/$sSource/$sFileName" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-file-not-exists",
				$sFileName
			));
		}

		if ( file_exists( BS_DATA_DIR . "/$sSource/$sFileName" ) && !$bOverwrite ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-file-already-exists",
				$sFileName
			));
		}

		$bStatus = copy(
			BS_DATA_DIR . "/$sSource/$sFileName",
			BS_DATA_DIR . "/$sDestination/$sFileName"
		);
		if ($bStatus)
			return Status::newGood();
		else
			return Status::newFatal( wfMessage( "bs-filesystemhelper-file-copy-error", $sFileName ) );
	}

	/**
	 * Copies a folder with its contents
	 * @param string $sFolderName
	 * @param string $sSource
	 * @param string $sDestination
	 * @param boolean $bOverwrite
	 * @return Status
	 */
	public static function copyFolder($sFolderName, $sSource, $sDestination, $bOverwrite = true) {
		if ( self::hasTraversal( "$sSource/$sFolderName" ) || self::hasTraversal( "$sDestination/$sFolderName" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-has-path-traversal"
			));
		}

		if ( !is_dir( BS_DATA_DIR . "/$sSource" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-no-directory",
				BS_DATA_DIR . "/$sSource"
			));
		}

		if ( !is_dir( BS_DATA_DIR . "/$sDestination" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-no-directory",
				BS_DATA_DIR . "/$sDestination"
			));
		}

		if ( !file_exists( BS_DATA_DIR . "/$sSource/$sFolderName" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-folder-not-exists",
				$sFolderName
			));
		}
		if ( file_exists( BS_DATA_DIR . "/$sSource/$sFolderName" ) && !$bOverwrite ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-folder-already-exists",
				$sFolderName
			));
		}
		$it = new RecursiveDirectoryIterator(
			BS_DATA_DIR . "/$sSource/$sFolderName"
		);
		BsFileSystemHelper::ensureDataDirectory( "$sDestination/$sFolderName" );
		$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($files as $file) {
			if ($file->getFilename() === '.' || $file->getFilename() === '..') {
				continue;
			}
			if ($file->isDir())
				mkdir($file->getRealPath(), 0777);
			if ($file->isFile()) {
				$bStatus = copy(
					BS_DATA_DIR . "/$sSource/$sFolderName/{$file->getFileName()}",
					BS_DATA_DIR . "/$sDestination/$sFolderName/{$file->getFileName()}"
				);
				if (!$bStatus)
					return Status::newFatal( wfMessage( "bs-filesystemhelper-folder-copy-error", $file->getFileName( ) ));
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
			return Status::newFatal( wfMessage( "bs-filesystemhelper-has-path-traversal" ) );

		if ( !is_dir( BS_DATA_DIR . "/$sSource" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-no-directory",
				BS_DATA_DIR . "/$sSource"
			));
		}

		if ( file_exists( BS_DATA_DIR . "/$sDestination" ) && !$bOverwrite ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-folder-already-exists",
				$sDestination
			));
		}
		$bStatus = rename(
			BS_DATA_DIR . "/$sSource",
			BS_DATA_DIR . "/$sDestination"
		);
		if ($bStatus)
			return Status::newGood();
		else
			return Status::newFatal( wfMessage( "bs-filesystemhelper-folder-rename-error", $sSource, $sDestination ) );
	}

	/**
	 * Deletes a file
	 * @param String $sFile
	 * @param String $sDir
	 * @return Status good on success, otherwise fatal with message
	 */
	public static function deleteFile($sFile, $sDir) {
		if ( self::hasTraversal( $sDir . "/$sFile" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-has-path-traversal"
			));
		}

		if ( !is_dir( BS_DATA_DIR . "/$sDir" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-folder-not-exists",
				$sFile
			));
		}

		$bStatus = unlink( BS_DATA_DIR . "/$sDir/$sFile" );
		if ($bStatus)
			return Status::newGood();
		else
			return Status::newFatal( wfMessage( "bs-filesystemhelper-file-delete-error", $sFile ) );
	}

	/**
	 * Deletes a folder with all its content
	 * @param String $sDir
	 * @return Status good on success, otherwise fatal with message
	 */
	public static function deleteFolder($sDir, $bIfExists = false) {
		if ( $bIfExists && !file_exists( BS_DATA_DIR . "/$sDir" ) ) {
			return Status::newGood();
		}

		if (self::hasTraversal($sDir))
			return Status::newFatal( wfMessage( "bs-filesystemhelper-has-path-traversal" ) );

		if ( !file_exists( BS_DATA_DIR . "/$sDir" ) ) {
			return Status::newFatal( wfMessage(
				"bs-filesystemhelper-folder-not-exists",
				$sDir
			));
		}
		//hint: http://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
		$it = new RecursiveDirectoryIterator( BS_DATA_DIR . "/$sDir" );
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
		rmdir( BS_DATA_DIR . "/$sDir" );
		return Status::newGood();
	}

	/**
	 * Uploads a file to the local file repository
	 * @param string $sName
	 * @param string $sDir
	 * @param string $sFileName
	 * @param string $sRequiredExtension
	 * @return Status
	 * @throws MWException
	 */
	public static function uploadFile( $sName, $sDir, $sFileName = '', $sRequiredExtension = '' ) {
		$oWebRequest = new WebRequest();
		$oWebRequestUpload = $oWebRequest->getUpload( $sName );
		$oUploadFromFile = new UploadFromFile();
		$oUploadFromFile->initialize( RequestContext::getMain()->getRequest()->getVal( 'name' ), $oWebRequestUpload );
		$aStatus = $oUploadFromFile->verifyUpload();
		if ( $aStatus['status'] != 0 ) {
			return Status::newFatal(
				wfMessage(
					'bs-filesystemhelper-upload-err-code',
					'{{int:' . UploadBase::getVerificationErrorCode( $aStatus['status'] ) . '}}'
				)->parse()
			);
		}

		$sRemoteFileName = $oWebRequestUpload->getName();
		$sRemoteFileExt = pathinfo( $sRemoteFileName, PATHINFO_EXTENSION );
		if ( $sRequiredExtension && ( strtolower( $sRemoteFileExt ) != strtolower( $sRequiredExtension ) ) ) {
			return Status::newFatal( wfMessage( 'bs-filesystemhelper-upload-wrong-ext', $sRequiredExtension ) );
		}

		$oStatus = self::ensureDataDirectory( $sDir );
		if ( !$oStatus->isGood() )
			return $oStatus;

		$sTmpName = BS_DATA_DIR . "/$sDir/";
		$sTmpName .= ( $sFileName ) ? $sFileName : $sRemoteFileName;
		if ( self::hasTraversal( $sTmpName, true ) )
			return Status::newFatal( wfMessage( "bs-filesystemhelper-has-path-traversal" ) );

		move_uploaded_file( $oWebRequestUpload->getTempName(), $sTmpName );
		return Status::newGood( $oWebRequestUpload->getName() );
	}

	/**
	 * Converts uploaded image to PNG
	 * @global WebRequest $wgRequest
	 * @param string $sName
	 * @param string $sDir
	 * @param string $sFileName
	 * @return Status
	 */
	public static function uploadAndConvertImage($sName, $sDir, $sFileName = '') {
		global $wgRequest;
		$oWebRequest = new WebRequest();
		$oWebRequestUpload = $oWebRequest->getUpload($sName);
		$oUploadFromFile = new UploadFromFile();
		$oUploadFromFile->initialize($wgRequest->getVal('name'), $oWebRequestUpload);
		$aStatus = $oUploadFromFile->verifyUpload();

		if ($aStatus['status'] != 0) {
			return Status::newFatal( wfMessage( 'bs-filesystemhelper-upload-err-code', '{{int:' . UploadBase::getVerificationErrorCode( $aStatus['status'] ) . '}}')->parse( ) );
		}

		$sRemoteFileName = $oWebRequestUpload->getName();
		/*
		  $sRemoteFileExt = pathinfo($sRemoteFileName, PATHINFO_EXTENSION);
		  if ($sRequiredExtension && strtolower($sRemoteFileExt) != strtolower($sRequiredExtension)) {
		  return Status::newFatal( wfMessage( 'bs-filesystemhelper-upload-wrong-ext', $sRequiredExtension ) );
		  }
		 */

		$oStatus = self::ensureDataDirectory($sDir);
		if (!$oStatus->isGood())
			return $oStatus;

		$sTmpName = BS_DATA_DIR . "/$sDir/";
		$sTmpName .= ($sFileName) ? $sFileName : $sRemoteFileName;
		if (self::hasTraversal($sTmpName, true))
			return Status::newFatal( wfMessage( "bs-filesystemhelper-has-path-traversal" ) );
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
				return Status::newFatal( wfMessage( 'bs-filesystemhelper-upload-unsupported-type' ) );
		}

		$iNewWidth = $iNewHeight = 1024;
		$fRatio = $iWidth / $iHeight;
		if ($fRatio < 1) {
			$iNewWidth = $iNewHeight * $fRatio; # portrait
		} else {
			$iNewHeight = $iNewWidth / $fRatio; # landscape
		}
		$rNewImage = imagecreatetruecolor($iNewWidth, $iNewHeight);
		imagealphablending($rNewImage, false);
		imagesavealpha($rNewImage,true);
		$iTransparent = imagecolorallocatealpha($rNewImage, 255, 255, 255, 127);
		imagefilledrectangle($rNewImage, 0, 0, $iNewWidth, $iNewHeight, $iTransparent);
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
		$sCheckDataPath = ( $bIsAbsolute ? '' : BS_DATA_DIR . "/") . $sPath;
		$sCheckCachePath = ( $bIsAbsolute ? '' : BS_CACHE_DIR . "/" ) . $sPath;
		if (file_exists($sCheckDataPath)) {
			return strpos(
					realpath( $sCheckDataPath ),
					realpath( BS_DATA_DIR . "/" )
				) !== 0;
		} elseif (file_exists($sCheckCachePath)) {
			return strpos(
					realpath( $sCheckCachePath ),
					realpath( BS_CACHE_DIR . "/" )
				) !== 0;
		} else {
			$sPath = self::normalizePath($sCheckDataPath);
			if( $sPath  === null ) {
				$sPath = self::normalizePath($sCheckCachePath);
			}
			return ($sPath === null);
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

	/**
	 * Maps invalid FS chars to placeholders
	 * @var array
	 */
	public static $aFSCharMap = array(
		':' => '_COLON_' //MediaWiki NSFileRepo makes it possible to have filenames with colons. Unfortunately we cannot have a colon in a filesystem path
	);

	protected static function makeTmpFileName( $sFileName ) {
		$sTmpFileName = $sFileName;
		foreach( self::$aFSCharMap as $search => $replace ) {
			$sTmpFileName = str_replace($search, $replace, $sTmpFileName);
		}
		return $sTmpFileName;
	}

	protected static function restoreFileName( $sTmpFileName ) {
		$sFileName = $sTmpFileName;
		foreach( self::$aFSCharMap as $replace => $search ) {
			$sFileName = str_replace($search, $replace, $sFileName);
		}
		return $sFileName;
	}

	/**
	 * Takes base64 encoded file content and saves it to a temporary location
	 * @param string $sFileName
	 * @param string $sFileContent
	 * @return Status
	 */
	public static function saveBase64ToTmp($sFileName, $sFileContent){
		$sFileName = self::makeTmpFileName($sFileName);
		$sFileName = wfTempDir() . "/" . basename( $sFileName );

		$sFileContent = preg_replace("#^data:.*?;base64,#", "", $sFileContent);
		$sFileContent = str_replace(' ', '+', $sFileContent);
		$sFileContent = base64_decode($sFileContent);

		$bFile = file_put_contents($sFileName, $sFileContent);
		if ($bFile === 0 || $bFile === false) {
			return Status::newFatal( wfMessage( 'bs-filesystemhelper-save-base64-error' ) );
		}
		else {
			return Status::newGood($sFileName);
		}
	}

	/**
	 * Saves a file from the server filesystem to the local file repo
	 * @global FileRepo $wgLocalFileRepo
	 * @global User $wgUser
	 * @param string $sFilename
	 * @param boolean $bDeleteSrc
	 * @param string $sComment
	 * @param string $sPageText
	 * @param boolean $bWatch
	 * @param boolean $bIgnoreWarnings
	 * @return Status
	 */
	public static function uploadLocalFile( $sFilename, $bDeleteSrc = false, $sComment = "", $sPageText = "", $bWatch = false, $bIgnoreWarnings = true ){
		global $wgLocalFileRepo, $wgUser;
		$oUploadStash = new UploadStash( new LocalRepo( $wgLocalFileRepo ), $wgUser );
		$oUploadFile = $oUploadStash->stashFile( $sFilename, "file" );
		$sTargetFileName = basename( self::restoreFileName( $sFilename ) );

		if ($oUploadFile === false) {
			return Status::newFailure( wfMessage('bs-filesystemhelper-upload-local-error-stash-file' )->plain());
		}

		$oUploadFromStash = new UploadFromStash( $wgUser, $oUploadStash, $wgLocalFileRepo );
		$oUploadFromStash->initialize( $oUploadFile->getFileKey(), $sTargetFileName );
		$aStatus = $oUploadFromStash->verifyUpload();

		if( $bIgnoreWarnings === false ) {
			$aWarnings = $oUploadFromStash->checkWarnings();
			if( !empty( $aWarnings ) ) {
				$oStatus = new Status();
				foreach( $aWarnings as $sKey => $vValue ) {
					$oStatus->warning( $sKey, $vValue );
				}
				return $oStatus;
			}
		}

		if ( $aStatus['status'] != UploadBase::OK ) {
			return Status::newFatal(
				wfMessage( 'bs-filesystemhelper-upload-err-code', '{{int:' . UploadBase::getVerificationErrorCode($aStatus['status']) . '}}' )->parse()
			);
		}
		$status = $oUploadFromStash->performUpload( $sComment, $sPageText, $bWatch, $wgUser );
		$oUploadFromStash->cleanupTempFile();

		if ( file_exists( $sFilename ) && $bDeleteSrc ) {
			unlink( $sFilename );
		}

		$oRepoFile = RepoGroup::singleton()->getLocalRepo()->newFile( $sTargetFileName );
		if ( $status->isGood() ){
			if( !$oRepoFile ) {
				return Status::newGood();
			}

			$oPage = WikiPage::factory( $oRepoFile->getTitle() );
			$oPage->doEditContent( new WikitextContent( $sPageText ), '' );

			return Status::newGood( $oRepoFile->getUrl(), true );
		}
		else{
			return Status::newFatal( wfMessage( 'bs-filesystemhelper-upload-local-error-create' ) );
		}
	}
}
