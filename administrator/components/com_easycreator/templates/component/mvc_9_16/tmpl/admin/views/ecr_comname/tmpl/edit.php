<?php
// Das Tooltip Behavior wird geladen
JHtml::_('behavior.tooltip');

// Der Link für das Formular
$actionLink = JRoute::_('index.php?option=ECR_COM_COM_NAME&layout=edit&id='.(int)$this->item->id);

?>
<form action="<?php echo $actionLink; ?>" method="post" name="adminForm" id="ECR_LOWER_COM_NAME-form">
	<fieldset class="adminform">
        <legend><?php echo JText::_('ECR_UPPER_COM_COM_NAME_ECR_UPPER_COM_NAME_DETAILS'); ?></legend>

        <ul class="adminformlist">
            <?php foreach($this->form->getFieldset() as $field): ?>
                <li><?php echo $field->label;echo $field->input;?></li>
            <?php endforeach; ?>
        </ul>
    </fieldset>
    <div>
        <input type="hidden" name="task" value="ECR_LOWER_COM_NAME.edit" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
