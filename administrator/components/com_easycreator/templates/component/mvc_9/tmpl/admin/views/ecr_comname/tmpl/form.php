<?php
##*HEADER*##

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_('Details'); ?></legend>

		<table class="admintable">
<!--admin.viewform.table._ECR_COM_TBL_NAME_.row-->
		</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="_ECR_COM_COM_NAME_" />
<input type="hidden" name="id" value="<?php echo $this->_ECR_COM_NAME_->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="_ECR_COM_TBL_NAME_" />
</form>