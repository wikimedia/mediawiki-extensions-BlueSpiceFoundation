<?php

/**
 * This file contains the BsConfig class.
 *
 * The BsConfig class manages all settings for the BlueSpice framework and any
 * in the framework used adaptersettings.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice for MediaWiki
 * For further information visit http://www.blue-spice.org
 *
 * @author     Sebastian Ulbricht <sebastian.ulbricht@dragon-design.hk>
 * @version    0.1.0
 * @version    $Id: Config.class.php 9719 2013-06-13 08:32:52Z rvogel $
 * @package    Bluespice_Core
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */
// TODO SU (27.06.11 14:46): Core contaminations entfernen (evtl. hookähnliches methoden in jeweiligen adapter implementieren)

/**
 * the BsConfig class
 * @package BlueSpice_Core
 * @subpackage Core
 */
class BsConfig {
    /**
     * configuration level = hardcodet or file
     */
    const LEVEL_PRIVATE = 1;
    /**
     * configuration level = systemwide (configurable and saved in the database)
     */
    const LEVEL_PUBLIC = 2;
    /**
     * configuration level = userspecific (configurable and saved in the database)
     */
    const LEVEL_USER = 4;
    /**
     * configuration level = managed by the adapter and the specific system
     */
    const LEVEL_ADAPTER = 8; // implemented by adapter
    /**
     * the variable should be rendered as a javascript variable
     */
    const RENDER_AS_JAVASCRIPT = 16;
	/*
	 *  configuration for LEVEL_USER if only user specified without default value
	 */
	const NO_DEFAULT = 262144;
    /**
     * the variable will be set write protected and cannot be changed in this instance
     */
    const SET_WRITE_PROTECTED = 32768;

    // TODO MRG20100810: Diese Typen sollten mit denen aus BsCommon vereinheitlicht werden.
    const TYPE_BOOL = 32;
    const TYPE_INT = 64;
    const TYPE_FLOAT = 128;
    const TYPE_STRING = 256;
    const TYPE_TEXT = 512;
    const TYPE_OBJECT = 1024;
    const TYPE_ARRAY_INT = 2048;
    const TYPE_ARRAY_STRING = 4096;
    const TYPE_ARRAY_MIXED = 8192;
    const TYPE_ARRAY_BOOL = 16384;
    const TYPE_JSON = 65536;

    /**
     * the variable has an own preference plugin, which should be executed when a preference form is rendered
     */
    const USE_PLUGIN_FOR_PREFS = 131072;

    /**
     * holds all settings instances as an asociative array
     * @var array
     */
    protected static $prSettings = array();
    protected static $prRegisterAdapter = array();
    protected static $prRegisterExtension = array();
    protected static $prGetUsersSettings = true;

    /**
     * holds all settings instances which should be rendered as javascript variables
     * @var array
     */
    protected static $prRegisterJavascript = array();

    // TODO MRG20100810: was ist mit path gemeint? bitte im kommentar erklÃ¤ren und ggf. besser benennen.
    /**
     * Use this method to register a configurable variable.
     *
     * @param string $path The unique identifier the variable should be accessibly by. I.e. 'Adapter::Extension::MyVar'.
     * @param mixed $default The default value of the variable if not set by user.
     * @param int $options
     * @param string $i18n string for proper labeling in the webinterface
     * @param string $sFormFieldMappingName The type for the input rendering. I.e.
     */
    public static function registerVar($path, $default = NULL, $options = 0, $i18n = '', $sFormFieldMappingName = 'text') {
        $var = self::getSettingObject($path);
        $var->setDefault($default);
        if ($options) {
            $var->setOptions($options);
        }
        if ($i18n) {
            $var->setI18N($i18n);
        }
        if ($sFormFieldMappingName) {
            $var->setFieldMapping($sFormFieldMappingName);
        }
    }

    /**
     * This method switch the config class to deliver the bluespice basesettings or the users own settings
     * @param bool $bFlag 
     */
    public static function deliverUsersSettings($bFlag) {
        $tmp = self::$prGetUsersSettings;
        self::$prGetUsersSettings = $bFlag;
        return $tmp;
    }

    /**
     * sets the value of the variable, which is specified by the path
     *
     * @param string $path The unique identifier the variable should be accessibly by. I.e. 'Adapter::Extension::MyVar'.
     * @param mixed $value the value
     * @param bool $bUserValue true to set the value as a user specific setting
     * @return bool true if the action was successful
     */
    public static function set($path, $value, $bUserValue = false) {
        $oSetting = self::getSettingObject($path);
        if ($bUserValue) {
            return $oSetting->_setUserValue($value);
        }
        return $oSetting->_set($value);
    }

    /**
     * gets the value of the variable, which is specified by the path
     * @param string $path The unique identifier the variable should be accessibly by. I.e. 'Adapter::Extension::MyVar'.
     * @return mixed the value
     */
    public static function get($path) {
        wfProfileIn('BS::Core::ConfigGet');

        if (function_exists('wfRunHooks')){
            $bChanged = false;
            wfRunHooks("BSCoreConfigGet", array(&$path, &$bChanged));
			if ( $bChanged === true ) {
				return $path;
			}

        }
        $oSetting = self::getSettingObject($path);
        
        wfProfileOut('BS::Core::ConfigGet');

        if (is_object($oSetting)) {
            return $oSetting->_get();
        }
        else
            return null;
    }

    /**
     * adds an value to the variable, which is specified by the path
     * @param string $path The unique identifier the variable should be accessibly by. I.e. 'Adapter::Extension::MyVar'.
     * @param mixed $value the value to add
     * @return mixed the new value after the addition
     * @see _add()
     */
    public static function add($path, $value) {
        return self::getSettingObject($path)->_add($value);
    }

    /**
     * returns all settings instances, which should be rendered as javascript variables
     * @return array
     */
    public static function getScriptSettings() {
        return self::$prRegisterJavascript;
    }

    /**
     * returns the BsConfig instance for a variable, specified by the path
     * @param string $path The unique identifier the variable should be accessibly by. I.e. 'Adapter::Extension::MyVar'.
     * @return BsConfig
     */
    protected static function getSettingObject($path) {
        if (!is_string($path)) {
            //@todo Fehlermeldung Falsches Pfad-Format (ADAPTER::[EXTENSION::]VARIABLE)
            return false;
        }
        $key = strtolower($path);
        if (isset(self::$prSettings[$key])) {
            return self::$prSettings[$key];
        }
        $_path = explode('::', $path);
        $adapter = NULL;
        $extension = NULL;
        $varname = NULL;
        $len = count($_path);

        if ($len < 2 || $len > 3) {
            //@todo Fehlermeldung Falsches Pfad-Format (ADAPTER::[EXTENSION::]VARIABLE)
            return false;
        }

        $adapter = array_shift($_path);
        $len--;
        if ($len == 2) {
            $extension = array_shift($_path);
        }
        $varname = array_shift($_path);

        $tmp = new BsConfig();
        $tmp->setKey($path);
        $tmp->setAdapter($adapter);
        $tmp->setExtension($extension);
        $tmp->setName($varname);
        self::$prSettings[$key] = $tmp;
        return $tmp;
    }

    /**
     * loads all settings from the database and saves the instances for every variable internal.
     */
    public static function loadSettings() {
        $db = BsDatabase::getInstance('CORE');
        $res = $db->query("SELECT {$db->getFieldNames(array('key', 'value'))} FROM #__bs_settings");
        while ($row = $res->fetchRow()) {
            self::set($row[0], unserialize($row[1]));
        }
    }

    /**
     * save all settings to the database
     * @return bool false if an error occurs
     */
    public static function saveSettings() {
        $db = BsDatabase::getInstance('CORE');
        // TODO TL (10.08.2011 15:00): replace global wgDBtype
        $db->query("DELETE FROM #__bs_settings");

        global $wgDBtype;

        if ($wgDBtype == 'oracle') {
            $sql = "INSERT ALL ";
            foreach (self::$prSettings as $setting) {
                // TODO MRG20100810: versteh ich das richtig? Private settings werden nicht in der
                // DB gespeichert? Wo dann? Wie kann ich die setzen?
                // TODO SU (27.06.11 14:35): @MRG Private settings werden hardgecoded
                if (!($setting->getOptions() & (self::LEVEL_PUBLIC | self::LEVEL_USER))) {
                    continue;
                }
				if ( $setting->getOptions() & BsConfig::NO_DEFAULT ) continue;

                if ($setting->getOptions() & self::TYPE_BOOL) {
                    $value = (bool) $setting->getValue();
                } else {
                    $value = $setting->getValue();
                }
                $value = serialize($value);
                $sqltoken[] = "INTO #__bs_settings ({$db->getFieldNames(array('key', 'value'))}) VALUES ('{$setting->getKey()}', '{$db->escape($value)}')";
            }

            $sql .= join(' ', $sqltoken);
            $sql .= " SELECT * FROM DUAL";
        } else {
            $sql = "INSERT INTO #__bs_settings ({$db->getFieldNames(array('key', 'value'))}) VALUES ";

            foreach (self::$prSettings as $setting) {
                // TODO MRG20100810: versteh ich das richtig? Private settings werden nicht in der
                // DB gespeichert? Wo dann? Wie kann ich die setzen?
                // TODO SU (27.06.11 14:35): @MRG Private settings werden hardgecoded
                if (!($setting->getOptions() & (self::LEVEL_PUBLIC | self::LEVEL_USER))) {
                    continue;
                }
				if ( $setting->getOptions() & BsConfig::NO_DEFAULT ) continue;

                if ($setting->getOptions() & self::TYPE_BOOL) {
                    $value = (bool) $setting->getValue();
                } else {
                    $value = $setting->getValue();
                }
                $value = serialize($value);
                $sqltoken[] = "('{$setting->getKey()}', '{$db->escape($value)}')";
            }

            $sql .= join(',', $sqltoken);
        }

        return($db->query($sql));
    }

    // TODO RBV (02.06.11 16:06): Core-Kontamination! Keine MediaWiki Funktionen im Core!
    public static function getVarForUser($sKey, $mUser) {
        $oSettingsObject = self::getSettingObject($sKey);
        if (is_object($mUser)) {
            $oUser = $mUser;
        } else {
            $oUser = User::newFromName($mUser);
        }
        $bOrigDeliverFlag = BsConfig::deliverUsersSettings(false);
        // This is needed in MW 1.17. For some strange reason, array keys are rendered with _, not : . 
        // Beware, in getUsersForVar, the value is being read manually, so no replacement is neccessary.
        // Todo: either store all values with underscore or read all values manually to get consistent behaviour.
        //$sKey = str_replace( ':', '_', $sKey );
        if (is_object($oUser)) {
            $mRawOption = $oUser->getOption($sKey);
            if ($mRawOption) {
                $mReturn = $mRawOption;
            } else {
                $mReturn = $oSettingsObject->getValue();
            }
        } else {
            $mReturn = $oSettingsObject->getValue();
        }

        BsConfig::deliverUsersSettings($bOrigDeliverFlag);
        return $mReturn;
    }

    // TODO RBV (02.06.11 16:06): Core-Kontamination! Keine MediaWiki Funktionen im Core!
    public static function getUsersForVar($sKey, $vValue) {
        global $wgDBtype;
        $oDb = wfGetDB(DB_SLAVE);
        $aUsers = array();

        if ($wgDBtype == 'oracle') {
            $rRes = $oDb->select(
                    'user_properties', 
                    '*',
                    array(
                        'up_property' => $sKey,
                        'up_value like \'' . serialize($vValue) . '\'' //TODO WP: HACKY oracle patch - find a better way!
                    )
                );
        } else {
            $rRes = $oDb->select(
                    'user_properties', 
                    '*',
                    array(
                        'up_property' => $sKey,
                        'up_value' => serialize($vValue)
                    )
                );
            }

            while ($oRow = $rRes->fetchObject()) {
                $aUsers[] = User::newFromId($oRow->up_user);
            }
        return $aUsers;
    }

    // TODO SU (27.06.11 14:48): Core-Kontamination! Keine MediaWiki Funktionen im Core!
    public static function loadUserSettings($user) {
        if (!is_object($user)) {
            $user = User::newFromName($user);
            if (!is_object($user)) {
                return false;
            }
        }

        $vars = BsConfig::getRegisteredVars();
        foreach ($vars as $var) {
            $iOptions = $var->getOptions();
            if (!( $iOptions & ( BsConfig::LEVEL_USER ) )) {
                continue;
            }
            $options = $var->getOptions();
            if (!($options & (BsConfig::LEVEL_PUBLIC | BsConfig::LEVEL_USER))) {
                continue;
            }
            $mValue = $user->getOption($var->_mKey, null);
            if (!is_null($mValue) && $mValue != '') {
                $var->_setUserValue($mValue);
            }
        }
    }

    /**
     * saves all userspecific settings for the given user to the database
     * @param string|User $user the username or a instance of the mediawiki user class
     * @return bool returns always true since we save the settings with mediawiki methods
     */
    public static function saveUserSettings($user) {
        if (!is_object($user)) {
            $user = User::newFromName($user);
        }

        $orig_deliver = self::deliverUsersSettings(true);

        foreach (self::$prSettings as $setting) {
            if (!($setting->getOptions() & (self::LEVEL_USER))) {
                continue;
            }
            if ($setting->getOptions() & self::TYPE_BOOL) {
                $user->setOption($setting->getKey(), (int) $setting->getValue());
            } else {
                $user->setOption($setting->getKey(), $setting->getValue());
            }
        }
        $user->saveSettings();
        self::deliverUsersSettings($orig_deliver);
        return true;
    }

    /**
     * returns an array of instaces which holds all registered variables.
     * @return array
     */
    public static function getRegisteredVars() {
        return self::$prSettings;
    }

    /**
     * the path/key of the variable (normally ADAPTER::EXTENSION::NAME or ADAPTER::NAME)
     * @var string
     */
    protected $_mKey = NULL;

    /**
     * the name of the variables adapter
     * @var string
     */
    protected $_mAdapter = NULL;

    /**
     * the name of the variables extension
     * @var string
     */
    protected $_mExtension = NULL;

    /**
     * the name of the variable
     * @var string
     */
    protected $_mName = NULL;

    /**
     * a bitmask which represents the variables options
     * @var int
     */
    protected $_mOptions = NULL;

    /**
     * the default value of the variable
     * @var mixed
     */
    protected $_mDefault = NULL;

    /**
     * the set value of the variable
     * @var mixed
     */
    protected $_mValue = NULL;

    /**
     * the set user value of the variable
     * @var mixed
     */
    protected $_mUserValue = NULL;

    /**
     * the i18n key for this variable
     */
    protected $_mI18n = NULL;

    /**
     * a mapping descriptor which represents a class of a form element for the preference forms
     * i.e. mapping descriptor 'toogle' represents an checkbox in the preference forms
     * @var string
     */
    protected $_mFieldMapping = NULL;

    /**
     * the constructor
     */
    protected function __construct() {
        $this->_mOptions = self::LEVEL_PRIVATE;
    }

    /**
     * sets the key for this variable
     * @param string $key (normally ADAPTER::EXTENSION::NAME or ADAPTER::NAME)
     */
    protected function setKey($key) {
        $this->_mKey = $key;
    }

    /**
     * sets the adapter name for this variable
     * @param string $adapter
     */
    protected function setAdapter($adapter) {
        $this->_mAdapter = $adapter;
        self::$prRegisterAdapter[strtolower($adapter)] = $this;
    }

    /**
     * sets the extension name for this variable
     * @param string $extension
     */
    protected function setExtension($extension) {
        $this->_mExtension = $extension;
        self::$prRegisterExtension[strtolower($extension)] = $this;
    }

    /**
     * sets the name for this variable
     * @param string $name
     */
    protected function setName($name) {
        $this->_mName = $name;
    }

    /**
     * sets the i18n instance for this variable
     * @param String $i18n
     */
    protected function setI18N($i18n) {
        $this->_mI18n = $i18n;
    }

    /**
     * sets the option bitmask for this variable
     *
     * If the option BsConfig::LEVEL_ADAPTER is set, this method force the variables adapter
     * to set the value of the adapters variable to the same value.
     *
     * @param int $options
     */
    protected function setOptions($options) {
        $this->_mOptions = $options;
        if ($options & self::RENDER_AS_JAVASCRIPT) {
            self::$prRegisterJavascript[strtolower($this->_mKey)] = $this;
        }
        if ($options & self::LEVEL_ADAPTER) {
            if (!is_null($this->_mValue)) {
                BsCore::getInstance($this->_mAdapter)->getAdapter()->set($this->_mName, $this->_mValue);
            }
            $this->_mValue = NULL;
        }
    }

    /**
     * sets the mapping descriptor for this variable
     * @see $_mFieldMapping
     * @param string $sFormFieldMappingName
     */
    protected function setFieldMapping($sFormFieldMappingName) {
        $this->_mFieldMapping = $sFormFieldMappingName;
    }

    /**
     * sets the default value for this variable
     * @param mixed $default
     */
    protected function setDefault($default) {
        $this->_mDefault = $default;
    }

    /**
     * returns the default value for this variable
     * @return mixed
     */
    public function getDefault() {
        return $this->_mDefault;
    }

    /**
     * sets the value of this variable
     * @param mixed $value
     */
    protected function _set($value) {
        if ($this->_mOptions & self::LEVEL_ADAPTER) {
            BsCore::getInstance($this->_mAdapter)->getAdapter()->set($this->_mName, $value);
            return;
        }
        $this->_mValue = $value;
    }

    protected function _setUserValue($value) {
        $this->_mUserValue = $value;
    }

    /**
     * This method try to add a given value to the variables actual value.
     *
     * Actually just the types INT, STRING and BOOL are supported.
     * If the type of the variable is int, the return of this method is an addition of the given value and the set value.
     * If the type is string, the given value will extend the set value.
     * If the type is bool, the result is the same as you would set the value.
     *
     * @param mixed $value
     * @return mixed the value after the addition
     */
    protected function _add($value) {
        if ($this->_mOptions & self::TYPE_OBJECT) {
            //@todo Fehlermeldung Typ unterstÃ¼tzt kein ADD
            return;
        }
        if ($this->_mOptions & self::LEVEL_ADAPTER) {
            return BsCore::getInstance($this->_mAdapter)->getAdapter()->add($this->_mName, $value);
        }
        if ($this->_mOptions & self::TYPE_INT || $this->_mOptions & self::TYPE_FLOAT) {
            $this->_mValue += $value;
        } elseif ($this->_mOptions & self::TYPE_STRING || $this->_mOptions & self::TYPE_TEXT) {
            $this->_mValue .= $value;
        } elseif ($this->_mOptions & self::TYPE_BOOL && $value) {
            $this->_mValue = $value;
        } else {
            $this->_mValue = array_merge_recursive($this->_mValue, $value);
        }
        return $this->_mValue;
    }

    /**
     * returns the value of this variable
     * @return mixed
     */
    protected function _get() {
        if ($this->_mOptions & self::LEVEL_ADAPTER) {
            $tmp = BsCore::getInstance($this->_mAdapter)->getAdapter()->get($this->_mName);
            if (!is_null($tmp)) {
                return $tmp;
            }
        }
        if (self::$prGetUsersSettings) {
			if (!is_null($this->_mUserValue)) {
				return $this->_mUserValue;
			}
        }
        if (!is_null($this->_mValue)) {
            return $this->_mValue;
        }
        return $this->_mDefault;
    }

    /**
     * returns the value of this variable
     * @see _get()
     * @return mixed
     */
    public function getValue() {
        return $this->_get();
    }

    /**
     * returns the key of this variable
     * @return string
     */
    public function getKey() {
        return $this->_mKey;
    }

    /**
     * returns the adapter name of this variable
     * @return string
     */
    public function getAdapter() {
        return $this->_mAdapter;
    }

    /**
     * returns the extension name of this variable
     * @return string
     */
    public function getExtension() {
        return $this->_mExtension;
    }

    /**
     * returns the i18n instance of this variable
     * @return String i18n
     */
    public function getI18nExtension() {
        return $this->_mExtension;
    }

    /**
     * returns the name of this variable
     * @return string
     */
    public function getName() {
        return $this->_mName;
    }

    /**
     * returns the i18n translated name of this variable
     * @return string
     */
	public function getI18nName() {
		if ( is_string( $this->_mI18n ) )
			return $this->_mI18n;
		else
			return $this->_mName;
	}

    /**
     * returns the option bitmap of this variable
     * @return int
     */
    public function getOptions() {
        return $this->_mOptions;
    }

    /**
     * returns the mapping descriptor of this variable
     * @return string
     */
    public function getFieldMapping() {
        return $this->_mFieldMapping;
    }

    /**
     * returns an id for this variable, which can be used in html
     * @return string
     */
    public function generateFieldId() {
        return $this->getAdapter() . '_' . $this->getExtension() . '_' . $this->getName();
    }

}
