<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Maz_Amf
 * @subpackage Parse
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Maz/Amf/Value/Messaging/AcknowledgeMessage.php';
require_once 'Maz/Amf/Value/Messaging/AsyncMessage.php';
require_once 'Maz/Amf/Value/Messaging/CommandMessage.php';
require_once 'Maz/Amf/Value/Messaging/ErrorMessage.php';
require_once 'Maz/Amf/Value/Messaging/RemotingMessage.php';

/**
 * Loads a local class and executes the instantiation of that class.
 *
 * @todo       PHP 5.3 can drastically change this class w/ namespace and the new call_user_func w/ namespace
 * @package    Maz_Amf
 * @subpackage Parse
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
final class Maz_Amf_Parse_TypeLoader
{
    /**
     * @var string callback class
     */
    public static $callbackClass;

    /**
     * @var array AMF class map
     */
    public static $classMap = array (
        'flex.messaging.messages.AcknowledgeMessage' => 'Maz_Amf_Value_Messaging_AcknowledgeMessage',
        'flex.messaging.messages.ErrorMessage'       => 'Maz_Amf_Value_Messaging_AsyncMessage',
        'flex.messaging.messages.CommandMessage'     => 'Maz_Amf_Value_Messaging_CommandMessage',
        'flex.messaging.messages.ErrorMessage'       => 'Maz_Amf_Value_Messaging_ErrorMessage',
        'flex.messaging.messages.RemotingMessage'    => 'Maz_Amf_Value_Messaging_RemotingMessage',
    );

    /**
     * @var array Default class map
     */
    protected static $_defaultClassMap = array(
        'flex.messaging.messages.AcknowledgeMessage' => 'Maz_Amf_Value_Messaging_AcknowledgeMessage',
        'flex.messaging.messages.ErrorMessage'       => 'Maz_Amf_Value_Messaging_AsyncMessage',
        'flex.messaging.messages.CommandMessage'     => 'Maz_Amf_Value_Messaging_CommandMessage',
        'flex.messaging.messages.ErrorMessage'       => 'Maz_Amf_Value_Messaging_ErrorMessage',
        'flex.messaging.messages.RemotingMessage'    => 'Maz_Amf_Value_Messaging_RemotingMessage',
    );

    public static $moreMappedFields = array();

    /**
     * Load the mapped class type into a callback.
     *
     * @param  string $className
     * @return object|false
     */
    public static function loadType($className)
    {
        $class    = false;
        $callBack = false;
        $class    = self::getMappedClassName($className);
        if (!class_exists($class)) {
            require_once 'Maz/Amf/Exception.php';
            throw new Maz_Amf_Exception($className .' mapped class '. $class . ' is not defined');
        }

        return $class;
    }

    /**
     * Looks up the supplied call name to its mapped class name
     *
     * @param  string $className
     * @return string
     */
    public static function getMappedClassName($className)
    {
        $mappedName = array_search($className, self::$classMap);

        if ($mappedName) {
            return $mappedName;
        }

        $mappedName = array_search($className, array_flip(self::$classMap));

        if ($mappedName) {
            return $mappedName;
        }

        return $className;
    }

    public static function getMoreMappedFields($className)
    {
        $mappedFields = isset(self::$moreMappedFields[$className])?self::$moreMappedFields[$className]:array();

        if ($mappedFields) {
            return $mappedFields;
        }

        return array();
    }

    /**
     * Map PHP class names to ActionScript class names
     *
     * Allows users to map the class names of there action script classes
     * to the equivelent php class name. Used in deserialization to load a class
     * and serialiation to set the class name of the returned object.
     *
     * @param  string $asClassName
     * @param  string $phpClassName
     * @return void
     */
    public static function setMapping($asClassName, $phpClassName, $moreMappedFields)
    {
    	if(!is_null($moreMappedFields))
    		self::$moreMappedFields[$phpClassName] = $moreMappedFields;
        self::$classMap[$asClassName] = $phpClassName;
    }

    /**
     * Reset type map
     *
     * @return void
     */
    public static function resetMap()
    {
        self::$classMap = self::$_defaultClassMap;
    }
}
