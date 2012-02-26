<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 26.02.12
 * Time: 00:39
 * To change this template use File | Settings | File Templates.
 */

ecrStylesheet('php_file_tree');
ecrStylesheet()

?>

<h2>Run</h2>

<ul class="php-file-tree">

    <li class="pft-file ext-php">

<label for="cliargs"><?php echo $this->project->comName.'.php'; ?></label>

<input type="text" size="50" id="cliargs" class="cliargs"/>

<a href="javascript:;" class="ecr_button"
   onclick="runCli('<?php echo $this->project->comName; ?>');">
    <?php echo jgettext('Execute'); ?>
</a>
    </li>
</ul>

<div id="ecr_codeeye_output" style="padding-top: 0.2em;"><h2><?php echo jgettext('Output')?></h2></div>
<pre id="ecr_codeeye_console"><?php echo jgettext('Console'); ?></pre>
