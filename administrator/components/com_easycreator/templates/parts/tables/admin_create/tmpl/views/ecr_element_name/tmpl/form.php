<?php
##*HEADER*##

JHTML::_('behavior.tooltip');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
	<legend><?php echo JText::_('Details'); ?></legend>
	<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_('Title'); ?>:
				</label>
			</td>
			<td colspan="2">
				<input class="text_area" type="text" name="title" id="title" size="32"
				maxlength="250" value="<?php echo $this->item->title;?>" />
			</td>
		</tr>
		##ECR_SMAT_PUBLISHED_VIEW1##
	</table>
	</fieldset>

	##ECR_SMAT_DESCRIPTION_VIEW1##

	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="option" value="com__ECR_COM_NAME_" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="_ECR_ELEMENT_NAME_" />
</form>
