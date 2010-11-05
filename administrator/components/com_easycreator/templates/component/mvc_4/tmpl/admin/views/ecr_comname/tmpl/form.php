<?php
##*HEADER*##

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_('Details'); ?></legend>

		<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="greeting">
					<?php echo JText::_('Greeting'); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="greeting" id="greeting" size="32"
				maxlength="250" value="<?php echo $this->_ECR_COM_NAME_->greeting;?>" />
			</td>
		</tr>
	</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="_ECR_COM_COM_NAME_" />
<input type="hidden" name="id" value="<?php echo $this->_ECR_COM_NAME_->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="_ECR_COM_TBL_NAME_" />
</form>