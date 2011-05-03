<?php
// Das Tooltip Behavior wird geladen
JHtml::_('behavior.tooltip');

// Der Link für das Formular
$actionLink = JRoute::_('index.php?option=com_hallowelt&layout=edit&id='.(int) $this->item->id);

?>
<form action="<?php echo $actionLink; ?>" method="post" name="adminForm" id="helloworld-form">
	<fieldset class="adminform">
        <legend><?php echo JText::_('COM_HALLOWELT_HALLOWELT_DETAILS'); ?></legend>

        <ul class="adminformlist">
            <?php foreach($this->form->getFieldset() as $field): ?>
                <li><?php echo $field->label;echo $field->input;?></li>
            <?php endforeach; ?>
        </ul>
    </fieldset>
    <div>
        <input type="hidden" name="task" value="hallowelt.edit" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>