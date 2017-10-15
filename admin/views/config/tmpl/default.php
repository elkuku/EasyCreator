<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 12-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

ecrLoadMedia('config');

JFactory::getApplication()->JComponentTitle = sprintf(jgettext('%s Configuration'), 'EasyCreator');

echo $this->loadTemplate($this->legacyTemplate);
