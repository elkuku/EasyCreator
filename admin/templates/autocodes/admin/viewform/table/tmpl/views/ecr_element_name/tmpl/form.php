<?php
##*HEADER*##

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_('Details'); ?></legend>

        <table class="admintable">
<!--viewform.table.ECR_COM_TBL_NAME.admin.row-->
	</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="ECR_COM_COM_NAME" />
<input type="hidden" name="id" value="<?php echo $this->ECR_COM_NAME->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="ECR_COM_TBL_NAME" />
</form>
