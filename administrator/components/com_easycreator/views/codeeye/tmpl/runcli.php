<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 30-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

ecrStylesheet('php_file_tree');
ecrStylesheet()

?>

<h2><?php echo jgettext('Run a CLI application'); ?></h2>

<ul class="php-file-tree">

    <li class="pft-file ext-php">

        <label class="inline" for="cliargs"><?php echo $this->project->comName.'.php'; ?></label>
        <input type="text" size="50" id="cliargs" class="cliargs"/>

        <a href="javascript:;" class="btn"
           onclick="runCli('<?php echo $this->project->getFilename(); ?>');">
            <?php echo jgettext('Execute'); ?>
        </a>
    </li>
</ul>

<div id="ecr_codeeye_output" style="padding-top: 0.2em;"><h2><?php echo jgettext('Output')?></h2></div>
<pre id="ecr_codeeye_console"><?php echo jgettext('Console'); ?></pre>
