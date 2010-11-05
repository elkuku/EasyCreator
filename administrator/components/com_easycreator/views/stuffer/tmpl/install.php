<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 06.-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
?>
<h2>Install - Uninstall - Update</h2>
<?php

echo $this->loadTemplate('php');
echo $this->loadTemplate('sql');
echo $this->loadTemplate('update');

?>

<div style="clear: both; height: 1em;"></div>

<h2>@TODOs</h2>
<ul>
	<li>create install/uninstall php files</li>
	<li>create install/uninstall sql files - create the queries</li>
	<li>
		<ul>
			<li>J! 1.6 brings new features..</li>
		</ul>
	</li>
</ul>


<input type="hidden" name="old_task" value="install" />
<?php
//#var_dump($this->installFiles);
