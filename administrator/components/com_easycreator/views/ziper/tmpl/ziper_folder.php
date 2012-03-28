<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 09-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$buildPath = $this->project->getZipPath();
?>
<div class="infoHeader img icon-16-installfolder"><?php echo jgettext('Build folder') ?></div>
<?php
if(2 == ECR_HELP):
    echo JHTML::tooltip(jgettext('Build folder').'::'
    .jgettext('The folder where your final package ends up. The folders extension_name and version will be added automatically.'
    .'<br />'
    .'If left blank the default folder will be used.'));
    echo '<br/><br/>';
    endif; ?>

<div class="path"><?php echo $buildPath.DS.$this->project->version; ?></div>
<?php
if(! JFolder::exists($buildPath.DS.$this->project->version)) :
    EcrHtml::displayMessage(jgettext('The folder does not exist'), 'warning');
endif;
