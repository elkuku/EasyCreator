<?php
##*HEADER*##

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<form method="post"
      action="<?php echo JRoute::_('index.php?option=ECR_COM_COM_NAME&layout=edit&id='.(int)$this->item->id); ?>"
      name="adminForm" id="ECR_LOWER_COM_NAME-form">

    <fieldset class="form-horizontal">
        <legend><?php echo JText::_('ECR_UPPER_COM_COM_NAME_ECR_UPPER_COM_NAME_DETAILS'); ?></legend>
		<?php foreach($this->form->getFieldset() as $field): ?>
        <div class="control-group">
            <div class="control-label"><?php echo $field->label; ?></div>
            <div class="controls"><?php echo $field->input; ?></div>
        </div>
		<?php endforeach; ?>
    </fieldset>

    <div>
        <input type="hidden" name="task" value="ECR_LOWER_COM_NAME.edit" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
