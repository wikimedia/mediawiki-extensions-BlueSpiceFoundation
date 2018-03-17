<?php
require_once( 'BSMaintenance.php' );

class BSPimpUnitTestResultsForJenkins extends BSMaintenance {
	public function __construct() {
		$this->addOption('source', 'The path to the source file', true, true);
		$this->addOption('target', 'The path to the source file', true, true);
		$this->addOption('prefix', 'A prefix for the whole test run', true, true);

		parent::__construct();
	}

	public function execute() {
		$originalReportFile = $this->getOption( 'source' );
		if ( !file_exists( $originalReportFile ) ) {
			die( "Cannot read input file" );
		}

		$prefix = $this->getOption( 'prefix' );

		$reportDom = new DOMDocument();
		$reportDom->load( $originalReportFile );

		$root = $reportDom->documentElement;
		$xpath = new DOMXPath( $reportDom );

		$testcases = $root->getElementsByTagName( 'testcase' );

		foreach ( $testcases as $testcase ) {
			// Get suite name
			// suitename can be like
			// * BSApiTitleQueryStoreTest without DataProvider
			// * BSApiTitleQueryStoreTest::testSingleFilter with DataProvider
			$suitename = explode( "::", $testcase->parentNode->getAttribute( 'name' ) );
			$suitename = $suitename[0];

			// Get file name
			// filename is absolute path or nothing if test has DataProvider. In this
			// case, we need to go up one level further
			$parentSuiteNode = $testcase->parentNode;
			if ( !$parentSuiteNode->hasAttribute( 'file' ) ) {
				$parentSuiteNode = $parentSuiteNode->parentNode;
			}
			$filename = preg_replace( "/^.*?extensions.(.*).tests.*$/", "$1", $parentSuiteNode->getAttribute( 'file' ) );
			$filename = str_replace( ["/", "\\"], "::", $filename );

			// Compile new class name for jenkins
			$jenkinsClassName = $prefix . "." . $filename . "." . $suitename;

			$testcase->setAttribute( "classname", $jenkinsClassName );
		}

		$reportDom->save( $this->getOption( 'target' ) );
	}
}

$maintClass = 'BSPimpUnitTestResultsForJenkins';
require_once RUN_MAINTENANCE_IF_MAIN;
