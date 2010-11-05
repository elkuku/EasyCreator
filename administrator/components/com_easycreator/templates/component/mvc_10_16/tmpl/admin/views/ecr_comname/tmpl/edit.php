<?php
##*HEADER*##

JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
?>

<form action="<?php echo JRoute::_('index.php?option=_ECR_COM_COM_NAME_'); ?>" method="post"
name="adminForm" id="adminForm" class="form-validate">

<div class="width-60 fltlft">
    <fieldset class="adminform">
        <legend><?php echo JText::_('Details'); ?></legend>
        <?php foreach($this->form->getGroup('options') as $field): ?>
            <?php if( ! $field->hidden): ?>
                <?php echo $field->label; ?>
            <?php endif; ?>
            <?php echo $field->input; ?>
        <?php endforeach; ?>
    </fieldset>
    </div>
    <div class="width-40 fltrt">
        <fieldset class="adminform">
            <legend><?php echo JText::_('Options'); ?></legend>
            <?php foreach($this->form->getGroup('params') as $field): ?>
                <?php if( ! $field->hidden): ?>
                    <?php echo $field->label; ?>
                <?php endif; ?>
                <?php echo $field->input; ?>
            <?php endforeach; ?>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="_ECR_COM_NAME_.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>
