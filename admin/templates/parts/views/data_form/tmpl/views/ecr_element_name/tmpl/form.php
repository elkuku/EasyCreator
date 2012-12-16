<?php
##*HEADER*##

JHTML::_('behavior.tooltip');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
	<legend><?php echo JText::_('Details'); ?></legend>
	<table class="admintable">
	<!--
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
	 -->
##ECR_OPTIONS##
	</table>
	</fieldset>

		<?php ##ECR_VIEW2_TMPL1_OPTION3## ?>

	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="option" value="com_ECR_COM_NAME" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="_ECR_LOWER_ELEMENT_NAME_" />
</form>
