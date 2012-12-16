<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 25-Mar-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

?>

<div class="infoHeader img icon24-database"><?php echo jgettext('Database support') ?></div>
<?php
echo EcrHtmlOptions::database($this->project);
