<?php
##*HEADER*##

JHtml::_('behavior.tooltip');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <thead><?php echo $this->loadTemplate('head');?></thead>
        <tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
        <tbody><?php echo $this->loadTemplate('body');?></tbody>
    </table>

    <div>
        <input type="hidden" name="option" value="ECR_COM_COM_NAME" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="controller" value="ECR_COM_TBL_NAME" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
