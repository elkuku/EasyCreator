<?php
/**
 * @version SVN: $Id$
 * @package    LanguageChecker
 * @subpackage Base
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://easy-joomla.org}
 * @author     Created on 12-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

//-- Include dependancies
jimport('joomla.application.component.controller');

$controller	= JController::getInstance('LanguageChecker');

$controller->execute(JRequest::getCmd('task'));

$controller->redirect();
