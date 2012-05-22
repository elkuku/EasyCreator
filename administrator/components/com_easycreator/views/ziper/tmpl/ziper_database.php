<?php
/**
 * User: elkuku
 * Date: 25.03.12
 * Time: 13:18
 */
?>

<div class="infoHeader img icon24-database"><?php echo jgettext('Database support') ?></div>
<?php
echo EcrHtmlOptions::database($this->project);
