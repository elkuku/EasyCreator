<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 06.-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
?>
<h2><?php echo jgettext('Install - Uninstall - Update'); ?></h2>
<?php

echo $this->loadTemplate('php');

echo $this->loadTemplate('sql');

echo $this->loadTemplate('update');

?>

<input type="hidden" name="old_task" value="install" />
<?php
