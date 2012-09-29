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

?>
<?php if(false == class_exists('g11n')) : ?>
<div class="g11nNotice">
    EasyCreator is in "English ONLY" mode ! If you want a localized version, please install the g11n library. -
    <a href="http://joomlacode.org/gf/project/elkuku/frs/?action=FrsReleaseBrowse&frs_package_id=5915">
        Download lib_g11n
    </a>
</div>
<?php endif; ?>

<?php

echo $this->loadTemplate($this->legacyTemplate);
