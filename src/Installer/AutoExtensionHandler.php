<?php

namespace BlueSpice\Installer;

class AutoExtensionHandler {
	/**
	 * Wiki install path
	 *
	 * @var string
	 */
	private $path = '';

	/**
	 * settings.d files
	 *
	 * @var array
	 */
	private $files = [];

	/**
	 * Enabled extensions
	 *
	 * @var array
	 */
	private $extensions = [];

	/**
	 * RegEx for wfLoadExtension or wfLoadSkin
	 *
	 * @var string
	 */
	private $functionCallRegex = "#(wfLoadSkin|wfLoadExtension)\(\s*['|\"](.*?)['|\"]\s*\)#";

	/**
	 * RegEx for require_once
	 *
	 * @var string
	 */
	// phpcs:ignore Generic.Files.LineLength.TooLong
	private $requireSetupFileRegex = "#require_once\(*\s*.*?['|\"].*?\/(extensions|skins)\/(.*?)\/(.*?)\.php['|\"]\s*\)*#";

	/**
	 * RegEx for SemainicMediaWiki
	 * SemanticMediaWiki is enabled by the GolbalFunction enableSemantics
	 *
	 * @var string
	 */
	private $enableSemanticsRegex = "#(enableSemantics)\(\s*['|\"](.*?)['|\"]\s*\)#";

	/**
	 *
	 * @param string $installPath
	 */
	public function __construct( $installPath ) {
		$this->path = $installPath . '/settings.d';
	}

	/**
	 * Process path and get activated extensions
	 *
	 * @return array
	 */
	public function getExtensions(): array {
		$this->findFiles();
		$this->processFiles();

		return $this->extensions;
	}

	/**
	 * Find files in settings.d
	 *
	 * @return void
	 */
	private function findFiles() {
		$dir = dir( $this->path );

		// phpcs:ignore MediaWiki.ControlStructures.AssignmentInControlStructures.AssignmentInControlStructures
		while ( ( $entry = $dir->read() ) !== false ) {
			if ( $entry === '.' || $entry === '..' ) {
				continue;
			}

			$filePath = $this->path . "/$entry";
			if ( is_dir( $filePath ) || !file_exists( $filePath ) ) {
				continue;
			}

			$maybeLocalCopy = preg_replace( '#\.php$#', '.local.php', $entry );
			if ( file_exists( $maybeLocalCopy ) ) {
				continue;
			}
			$fileName = $entry;

			if ( !array_key_exists( $fileName, $this->files ) ) {
				$this->files[$fileName] = $filePath;
			}
		}

		ksort( $this->files, SORT_NATURAL );

		$dir->close();
	}

	/**
	 * Process setings.d files
	 *
	 * @return void
	 */
	private function processFiles() {
		foreach ( $this->files as $file ) {
			$fileContent = file_get_contents( $file );
			$this->extensions = array_merge(
				$this->extensions,
				$this->processFileContent( $fileContent )
			);

		}
	}

	/**
	 * Process file line by line and get activated extensions
	 *
	 * @param string $fileContent
	 * @return array
	 */
	private function processFileContent( $fileContent ): array {
		$extensions = [];

		$lines = explode( "\n", $fileContent );
		foreach ( $lines as $line ) {
			$line = trim( $line );

			if ( empty( $line ) ) {
				continue;
			}

			if ( $line === 'return;' ) {
				return $extensions;
			}

			if ( strpos( $line, '<?php' ) === 0 ) {
				continue;
			}

			if ( strpos( $line, '#' ) === 0 ) {
				continue;
			}

			if ( strpos( $line, '/' ) === 0 ) {
				continue;
			}

			if ( strpos( $line, '*' ) === 0 ) {
				continue;
			}

			$matches = [];
			if ( preg_match( $this->functionCallRegex, $line, $matches ) ) {
				$extensions["ext-$matches[2]"] = $matches[2];
			} elseif ( preg_match( $this->requireSetupFileRegex, $line, $matches ) ) {
				if ( !empty( $matches ) && ( count( $matches ) === 4 ) && ( $matches[2] === $matches[3] ) ) {
					$extensions["ext-$matches[3]"] = $matches[3];
				}
			} elseif ( preg_match( $this->enableSemanticsRegex, $line, $matches ) ) {
				/*
				* Hack for SemanticMediaWiki
				* SemanticMediaWiki is enabled by the GlobalFunction enableSemantic()
				*/
				if ( !empty( $matches ) && ( $matches[1] === 'enableSemantics' ) ) {
					$extensions["ext-SemanticMediaWiki"] = 'SemanticMediaWiki';
				}
			}
		}

		return $extensions;
	}
}
