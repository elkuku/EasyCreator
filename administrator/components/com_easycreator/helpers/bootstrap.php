<?php
/**
 *
 * This file is used to run unit tests.
 * It must be placed in the Joomla! root directory.
 *
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 04-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

define('_JEXEC', 1);

define('JPATH_BASE', dirname(__FILE__));

define('DS', DIRECTORY_SEPARATOR);

require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';
