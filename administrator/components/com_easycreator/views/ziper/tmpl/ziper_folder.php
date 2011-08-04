<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 09-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$buildPath = $this->project->getZipPath();
?>
<strong class="img icon-16-installfolder"><?php echo jgettext('Build folder'); ?></strong>
<?php
echo JHTML::tooltip(jgettext('Build folder').'::'
.jgettext('The folder where your final package ends up. The folders extension_name and version will be added automatically.')
.jgettext('<br />If left blank the default folder will be used.'));

echo '<br /><br />';
echo $buildPath;
echo DS.$this->project->version;

if( ! JFolder::exists($buildPath.DS.$this->project->version)) :
    ecrHTML::displayMessage(jgettext('The folder does not exist'), 'warning');
endif;
