<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 21-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
?>

<?php echo ecrHTML::floatBoxStart(); ?>
<div class="infoHeader img icon-16-easy-joomla">AutoCode</div>
<strong><?php echo jgettext('List postfix'); ?></strong>
<?php echo $this->project->listPostfix; ?>
<?php echo ecrHTML::floatBoxEnd();
