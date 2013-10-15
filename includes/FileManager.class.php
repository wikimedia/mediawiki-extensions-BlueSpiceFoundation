<?php
/**
 * This file is part of blue spice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (c) 2007-2009, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Sebastian Ulbricht
 * @version 0.1.0 beta
 *
 * $LastChangedDate: 2010-02-26 11:10:57 +0100 (Fr, 26 Feb 2010) $
 * $LastChangedBy: mscheer $
 * $Rev: 131 $

 */

// Last Review: MRG20100813

class BsFileManager {
	const NO_MINIFICATION = 256;
	const ORDER_IF_LOADED = 512;
	const LOAD_ON_DEMAND  = 1024;

	protected $aRegisteredFiles  = array();
	protected $aRegisteredGroups = array();
	protected $aFileRegister     = array();

	public function __construct() {
		
	}

	public function add($sGroup, $sFile, $iOptions) {
		wfProfileIn( 'BS::'.__METHOD__ );
		$sGroup = strtolower($sGroup);
		$iIndex = array_search($sGroup, $this->aFileRegister);
		if($iIndex) {
			if(!preg_match('%[/\\\\.]%', $sFile)) {

			}
			else {
				array_splice($this->aFileRegister, ($iIndex - 1), 0, array($sFile));
				$this->aRegisteredFiles[$sFile] = array($sGroup, $iOptions);
			}
		}
		else {
			if(!preg_match('%[/\\\\.]%', $sFile)) {
				$sFile = strtolower($sFile);
				if(isset($this->aRegisteredGroups[$sFile])) {
					$this->putBeforeGroup($sGroup, $sFile);
				}
				else {
					$this->aFileRegister[] = $sFile;
				}
			}
			else {
				if(isset($this->aRegisteredFiles[$sFile])) {
					$this->putBeforeFile($sGroup, $sFile);
				}
				else {
					$this->aFileRegister[] = $sFile;
				}

				$this->aRegisteredFiles[$sFile] = array($sGroup, $iOptions);
				$this->aRegisteredGroups[$sGroup][] = $sFile;
			}
		}
		wfProfileOut( 'BS::'.__METHOD__ );
	}

	public function getFileRegister() {
		wfProfileIn( 'BS::'.__METHOD__ );
		$aReturn = array();
		foreach($this->aFileRegister as $sFile) {
			if(!preg_match('%[/\\\\.]%', $sFile)) {
				continue;
			}
			$aReturn[$sFile] = $this->aRegisteredFiles[$sFile][1] + 0;
		}
		wfProfileOut( 'BS::'.__METHOD__ );
		return $aReturn;
	}

	protected function putBeforeFile($sGroup, $sFile) {
		$iIndex = array_search($sFile, $this->aFileRegister);
		array_splice($this->aFileRegister, ($iIndex - 1), 0, $this->aRegisteredGroups[$sGroup]);
		$this->aFileRegister = array_slice($this->aFileRegister, 0, (count($this->aRegisteredGroups[$sGroup]) * -1), true);
	}
	
	protected function putBeforeGroup($sGroup, $sTargetGroup) {
		$sFile = $this->aRegisteredGroups[$sTargetGroup][0];
		$this->putBeforeFile($sGroup, $sFile);
	}
}

/*
class BsFileManager {
	const NO_MINIFICATION = 256;
	// TODO MRG20100813: Was macht diese Konstante genau? Ich lese: es wird nur sortiert, wenn die Datei geladen wird
	const ORDER_IF_LOADED = 512;
	const LOAD_ON_DEMAND  = 1024;

	// TODO MRG20100813: $NULL ist doch allgemeingültig. Warum wird das abstrahiert?
	protected static $NULL		= NULL;
	protected static $prCounter	= 0;
	protected static $prFiles	= array();

	public static function add($group, $file, $options = 0) {
		// TODO MRG20100813: normalerweise normieren wir mit strtolower
		$group = strtoupper($group);
		$tmp = new self(self::$prCounter++, $group, $file, $options);
		self::$prFiles['register'][]		= $tmp;
		self::$prFiles['groups'][$group][]	= $tmp;
		if(!($options & self::ORDER_IF_LOADED)) {
			self::$prFiles['search'][$file] = $tmp;
		}
	}

	// TODO MRG20100813: wozu diese Kapselung? Ist geplant, dass hier noch Funktionalität hinzukommt?
	public static function getOutput() {
		return self::buildOutput();
	}

	protected static function buildOutput() {
		self::prepare();
		if(!count(self::$prFiles['register'])) {
			return self::$NULL;
		}
		//self::test();
		$node = self::sort();
		//self::test();
		// TODO MRG20100813: Hier fehlen kurze Kommentare
		$node = $node->getFirstNode();
		$node->output();
	}

	// TODO MRG20100813: Ist das & notwendig?
	public static function &sort() {
		$node = self::$prFiles['register'][0]->getFirstNode();
		$counter = 0;
		// TODO MRG20100813: Wieso Counter < 10? Wenn das eine Sicherheitsschranke ist, dann mindestens auf 100 setzen.
		// Allerdings sollten wir diesen counter nie brauchen, daher bin ich mir auch nicht sicher, ob wir ihn hier
		// verwenden sollten.
		while(!$node->isLastNode() && $counter <= 10) {
			$_node = $node->haveToLoadBefore($node);
			if($_node != NULL) {
				$node->moveBeforeThis($_node);
				$node = $_node->getFirstSibling();
			}
			else {
				if(!$node->isLastNode()) {
					$node = $node->next();
				}
			}
			$counter++;
		}
		return $node;
	}

	protected static function prepare() {
		foreach(self::$prFiles['register'] as $key => $file) {
			$file->mPrevNode = isset(self::$prFiles['register'][$file->mId-1]) ? self::$prFiles['register'][$file->mId-1] : NULL;
			$file->mNextNode = isset(self::$prFiles['register'][$file->mId+1]) ? self::$prFiles['register'][$file->mId+1] : NULL;
		}
		foreach(self::$prFiles['groups'] as $name => $group) {
			foreach($group as $key => $file) {
				$file->mPrevSibling = isset($group[$key-1]) ? $group[$key-1] : NULL;
				$file->mNextSibling = isset($group[$key+1]) ? $group[$key+1] : NULL;
				if($file->mNextSibling) {
					if(self::isFilePath($file->mNextSibling->mFile)) {
						$file->setLoadBefore($file->mNextSibling);
					}
					else {
						$file->setLoadBefore(self::getFirstOfGroup($file->mNextSibling->mFile));
					}
				}
				if(!self::isFilePath($file->mFile)) {
					if($file->mNextSibling) {
						$last = self::getLastOfGroup($file->mFile);
						if($last) {
							$last->setLoadBefore($file);
						}
					}
					if($file->mPrevSibling) {
						$file->mPrevSibling->setLoadBefore(self::getFirstOfGroup($file->mFile));
					}
				}
			}
		}
	}

	// TODO MRG20100813: Brauchen wir das & ?
	protected static function &getFirstOfGroup($group) {
		if(self::$prFiles['groups'][strtoupper($group)][0]) {
			return self::$prFiles['groups'][strtoupper($group)][0]->getFirstSibling();
		}
		return self::$NULL;
	}

	// TODO MRG20100813: Brauchen wir das & ?
	protected static function &getLastOfGroup($group) {
		if(self::$prFiles['groups'][strtoupper($group)][0]) {
			return self::$prFiles['groups'][strtoupper($group)][0]->getLastSibling();
		}
		return self::$NULL;
	}

	// TODO MRG20100813: Diese Funktion scheint mir sehr einfach zu sein :) 
	// Könnte auch in die Blue spice Klasse als allgemeine Hilfsfunktion
	protected static function isFilePath($file) {
		return strpos($file, '.');
	}

	protected static function test() {
		foreach(self::$prFiles['groups'] as $name => $group) {
			echo "Group: $name<br>";
			foreach($group as $key => $file) {
				echo "<pre>File: ";
				echo $file->mFile."\n";
				echo "PrevNode: ";
				if($file->mPrevNode) {
					echo $file->mPrevNode->mFile;
				}
				echo "\n";
				echo "NextNode: ";
				if($file->mNextNode) {
					echo $file->mNextNode->mFile;
				}
				echo "\n";
				echo "PrevSibling: ";
				if($file->mPrevSibling) {
					echo $file->mPrevSibling->mFile;
				}
				echo "\n";
				echo "NextSibling: ";
				if($file->mNextSibling) {
					echo $file->mNextSibling->mFile;
				}
				echo "\n";
				echo "LoadBefore:\n";
				if($file->mLoadBefore) {
					foreach($file->mLoadBefore as $_file) {
						if($_file) {
							echo "- {$_file->mFile}\n";
						}
						else {
							echo "- NULL\n";
						}
					}
				}
				echo "</pre>";
			}
			echo "<hr>";
		}
	}

	protected $mId			= NULL;
	protected $mGroup		= NULL;
	protected $mFile		= NULL;
	protected $mOptions		= NULL;
	protected $mPrevNode	= NULL;
	protected $mNextNode	= NULL;
	protected $mPrevSibling = NULL;
	protected $mNextSibling	= NULL;
	protected $mLoadBefore	= NULL;
	protected $mIsExtension = false;

	protected function __construct($id, $group, $file, $options) {
		$this->mId		= $id;
		$this->mGroup	= $group;
		$this->mFile	= $file;
		$this->mOptions	= $options;
	}

	protected function output() {
		if($this->mNextNode) {
			$this->mNextNode->output();
		}
	}

	public function setLoadBefore(&$obj) {
		$this->mLoadBefore[] = $obj;
	}

	// TODO MRG20100813: Brauchen wir das & ?
	public function &haveToLoadBefore(BsFileManager &$obj) {
		if($this->mLoadBefore != NULL) {
			foreach($this->mLoadBefore as $key => $file) {
				if(!$file) {
					continue;
				}
				if($file->mFile == $obj->mFile) {
					$this->mLoadBefore[$key] = NULL;
					return $this;
				}
			}
		}
		if($this->mNextSibling) {
			return $this->mNextSibling->haveToLoadBefore($obj);
		}
		if($this->mNextNode) {
			return $this->mNextNode->haveToLoadBefore($obj);
		}
		return self::$NULL;
	}

	public function moveBeforeThis(BsFileManager $obj) {
		$first = $obj->getFirstSibling();
		$last  = $obj->getLastSibling();
		$first->mPrevNode->mNextNode = $last->mNextNode;
		$last->mNextNode->mPrevNode = $first->mPrevNode;
		$first->mPrevNode = $this->mPrevNode;
		$last->mNextNode = $this;
		$this->mPrevNode = $last;
	}

	// TODO MRG20100813: Brauchen wir das & ?
	public function &getFirstSibling() {
		if($this->mPrevSibling) {
			return $this->mPrevSibling->getFirstSibling();
		}
		return $this;
	}

	// TODO MRG20100813: Brauchen wir das & ?
	public function &getLastSibling() {
		if($this->mNextSibling) {
			return $this->mNextSibling->getLastSibling();
		}
		return $this;
	}

	// TODO MRG20100813: Brauchen wir das & ?
	public function &getFirstNode() {
		if($this->mPrevNode) {
			return $this->mPrevNode->getFirstNode();
		}
		return $this;
	}

	// TODO MRG20100813: Brauchen wir das & ?
	public function &getLastNode() {
		if($this->mNextNode) {
			return $this->mNextNode->getLastNode();
		}
		return $this;
	}

	public function isFirstNode() {
		return ($this->mPrevNode == NULL);
	}

	public function isLastNode() {
		return ($this->mNextNode == NULL);
	}

	// TODO MRG20100813: Brauchen wir das & ?
	public function &next() {
		if($this->mNextNode) {
			return $this->mNextNode;
		}
		return self::$NULL;
	}
}
*/