<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 25.03.12
 * Time: 13:18
 * To change this template use File | Settings | File Templates.
 */
?>

<div class="infoHeader img icon-24-database"><?php echo jgettext('Database support') ?></div>
<?php echo EcrHtml::drawDbOptions($this->project); ?>
