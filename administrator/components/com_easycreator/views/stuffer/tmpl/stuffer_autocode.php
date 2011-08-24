<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 21-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
?>

<div class="ecr_floatbox">
    <div class="infoHeader img icon-24-easycreator">AutoCode</div>
    <strong><?php echo jgettext('List postfix'); ?></strong>
    <?php echo $this->project->listPostfix; ?>
</div>
