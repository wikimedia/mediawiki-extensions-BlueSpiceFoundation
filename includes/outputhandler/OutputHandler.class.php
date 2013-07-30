<?php
/**
 * This file is part of blue spice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (c) 2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Markus Glaser, Sebastian Ulbricht
 * @version 0.1.0 alpha
 *
 * $LastChangedDate: 2010-07-18 01:13:04 +0200 (So, 18 Jul 2010) $
 * $LastChangedBy: mglaser $
 * $Rev: 314 $
 * $Id: ViewManager.class.php 314 2010-07-17 23:13:04Z mglaser $
 */

// Last reviewed: MRG20100813

// TODO MRG20100813: Generell fehlen noch einige Kommentare
class BsOutputHandler {
	/**
	 * Port output replaces site dom element with its id.
	 */
	const PORT_MARKUP_REPLACE	= 1;
	/**
	 * Port output will be insert into site dom element with its id.
	 */
	const PORT_MARKUP_INSERT	= 2;
	/**
	 * If no dom element with ports id is present in site domtree,
	 * port output will be insert as new node under the body-node.
	 * This option have to be combined with PORT_MARKUP_REPLACE or _INSERT.
	 */
	const PORT_MARKUP_BUILD		= 4;

	protected static $_registeredViews = array();
	protected static $_registeredPorts = array();
	protected static $_supressCleanup  = false;

	/**
	 * Don´t call this method manually!
	 */
	// TODO MRG20100813: Müsste sie dann nicht private sein?
	// TODO MRG20100813: Wie wird sichergestellt, dass ob auch wieder stoppt?
	// TODO MRG20100813: Versteh ich richtig, dass es immer eine Error und eine Notice-Queue gibt?
	public static function init() {
		wfProfileIn( 'BS::'.__METHOD__ );
		//ob_start(array('BsOutputHandler', 'execute'));
		self::createPort('standardErrorMessage', 'ViewErrorMessage', 'siteNotice', self::PORT_MARKUP_INSERT);
		self::createPort('standardNoticeMessage', 'ViewNoticeMessage', 'siteNotice', self::PORT_MARKUP_INSERT);
		wfProfileOut( 'BS::'.__METHOD__ );
	}

	/**
	 * A wrapper method to add an standard error message to site output.
	 * @param String $msg the error message
	 */
	public static function error($msg) {
		self::getPortElement('standardErrorMessage')->addData(array($msg));
	}

	/**
	 * A wrapper method to add an standard notice message to site output.
	 * @param String $msg the notice message
	 */
	public static function notice($msg) {
		self::getPortElement('standardNoticeMessage')->addData(array($msg));
	}

	/**
	 * Register a Plugin which is not located in the standard plugin directory.
	 * @param String $name the class name of plugin to register (have to be unique)
	 * @param String $path the absolute path to the plugin file
	 * @return boolean returns false if an error occured and plugin has not been registered or true otherwise
	 */
	public static function registerView($name, $path) {
		$_name = strtolower($name);
		if(isset(self::$_registeredViews[$_name]) && self::$_registeredViews[$_name]) {
			// @todo Error Plugin already registered
			return false;
		}
		if(!is_file($path)) {
			// @todo Error File not found
			return false;
		}
		require_once($path);
		if(!class_exists($name, false)) {
			// @todo Error Class not found
			return false;
		}
		self::$_registeredViews[$_name] = $name;
		return true;
	}

	public static function loadViews() {
		wfProfileIn( 'BS::'.__METHOD__ );
		$views = glob(BSROOTDIR.DS.'includes'.DS.'outputhandler'.DS.'views'.DS.'view.*.php');
		foreach($views as $view) {
			$file = basename($view);
			$name = explode('.', $file);
			$name = $name[1];
			self::registerView('View'.$name, BSROOTDIR.DS.'includes'.DS.'outputhandler'.DS.'views'.DS.$file);
		}
		wfProfileOut( 'BS::'.__METHOD__ );
	}
	// TODO MRG20100813: Was bedeutet 6? kann man das durch eine Konstante ersetzen?
	public static function createPort($name, $type = 'ViewBaseElement', $htmlId = false, $options = 6) {
		wfProfileIn( 'BS::'.__METHOD__ );
		$_name = strtolower($name);
		self::$_registeredPorts[$_name] = array(
			'id'		=> $htmlId,
			'element'	=> self::createElement($type),
			'options'	=> $options
		);
		wfProfileOut( 'BS::'.__METHOD__ );
	}

	// TODO MRG20100813: Was bedeutet 6? Kann man das durch eine Konstante ersetzen?
	public static function bindPort($name, $element, $htmlId = false, $options = 6) {
		wfProfileIn( 'BS::'.__METHOD__ );
		if( !( $element instanceof ViewBaseElement ) ) {
			// @todo Error no valid Plugin
			wfProfileOut( 'BS::'.__METHOD__ );
			return false;
		}
		$_name = strtolower($name);
		self::$_registeredPorts[$_name] = array(
			'id'		=> $htmlId,
			'element'	=> $element,
			'options'	=> $options
		);
		wfProfileOut( 'BS::'.__METHOD__ );
	}

	public static function getPortElement($name) {
		$_name = strtolower($name);
		if(!isset(self::$_registeredPorts[$_name])) {
			return false;
		}
		return self::$_registeredPorts[$_name]['element'];
	}

	public static function createElement($name) {
		$_name = strtolower($name);
		if(!isset(self::$_registeredViews[$_name])) {
			// @todo Error Plugin not found
			return false;
		}
		$classname = self::$_registeredViews[$_name];
		return new $classname();
	}

	public static function processRawOutput() {
		self::$_supressCleanup = true;
	}

	/**
	 * Don´t call this method manually!
	 */
	// TODO MRG20100813: Muss es dann nicht private sein?
	public static function execute($buffer) {
		wfProfileIn( 'BS::'.__METHOD__ );
		
		if(self::$_supressCleanup || BsCore::getParam( 'feed', false, BsPARAM::REQUEST|BsPARAMTYPE::STRING ) || BsCore::getParam( 'action', 'view', BsPARAM::REQUEST|BsPARAMTYPE::STRING ) == 'ajax' || strpos($_SERVER['SCRIPT_NAME'], 'load.php') !== false) {
			return $buffer;
		}
		libxml_use_internal_errors( true ); //Supress warnings caused by MediaWiki malformed html
		$document = new DOMDocument();
		$document->loadHTML($buffer);

		if(count(self::$_registeredPorts)) {
			foreach(self::$_registeredPorts as $port) {
				$port_dom = new DOMDocument();
				$port_dom->loadHTML(
					'<html><head></head><body>'
					.$port['element']->execute().'</body></html>'
				);
				$port_content = $port_dom->getElementsByTagName('body')->item(0)->firstChild;
				if(!$port_content) {
					continue;
				}
				$port_content = $document->importNode($port_content, true);

				$port_holder = $document->getElementById($port['id']);
				if(!$port_holder && $port['options'] & self::PORT_MARKUP_BUILD) {
					$port_holder = $document->getElementsByTagName('body')->item(0);
					$port_holder->appendChild($port_content);
				}
				elseif($port['options'] & self::PORT_MARKUP_REPLACE) {
					$port_holder->parentNode->replaceChild($port_content, $port_holder);
				}
				elseif($port['options'] & self::PORT_MARKUP_INSERT) {
					$port_holder->appendChild($port_content);
				}
			}
		}

		// TODO MRG20100813: Hab ich hier nicht <html><head>... drin?
		wfProfileOut( 'BS::'.__METHOD__ );
		return $document->saveHTML();
	}
}
