<?php
##*HEADER*##

//-- Load the tooltip behavior
JHtml::_('behavior.tooltip');

?>
<form action="<?php echo JRoute::_('index.php?option=_ECR_COM_COM_NAME_'); ?>" method="post" name="adminForm">
    <table class="adminlist">
        <thead><?php echo $this->loadTemplate('header');?></thead>
        <tfoot><?php echo $this->loadTemplate('footer');?></tfoot>
        <tbody><?php echo $this->loadTemplate('body');?></tbody>
    </table>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</form>
