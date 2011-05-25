<?php
// Das Tooltip Behavior wird geladen
JHtml::_('behavior.tooltip');

// Der Link für das Formular
$actionLink = JRoute::_('index.php?option=_ECR_COM_COM_NAME_&layout=edit&id='.(int)$this->item->id);

?>
<form action="<?php echo $actionLink; ?>" method="post" name="adminForm" id="_ECR_LOWER_COM_NAME_-form">
	<fieldset class="adminform">
        <legend><?php echo JText::_('_ECR_UPPER_COM_COM_NAME___ECR_UPPER_COM_NAME__DETAILS'); ?></legend>

        <ul class="adminformlist">
            <?php foreach($this->form->getFieldset() as $field): ?>
                <li><?php echo $field->label;echo $field->input;?></li>
            <?php endforeach; ?>
        </ul>
    </fieldset>
    <div>
        <input type="hidden" name="task" value="_ECR_LOWER_COM_NAME_.edit" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>