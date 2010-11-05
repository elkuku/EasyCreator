<?php
##*HEADER*##

//-- Include joomla.javascript.js
$document =& JFactory::getDocument();
$document->addScript(JURI::root(true).'/includes/js/joomla.javascript.js');

?>
<script language="javascript" type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	<?php
        $editor =& JFactory::getEditor();
        echo $editor->save('content');
    ?>

	submitform(pressbutton);
}
</script>

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
						<input class="text_area" type="text" name="greeting"
						 id="greeting" size="25" maxlength="25" value="<?php echo $this->data->greeting; ?>" />
					</td>
				</tr>
				<tr>
					<td width="100" align="right" class="key">
						<label for="content">
							<?php echo JText::_('Content'); ?>:
						</label>
					</td>
					<td>
						<?php
                        $editor =& JFactory::getEditor();
                        echo $editor->display('content', $this->data->content, '550', '400', '60', '20', false);
                        ?>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="_ECR_COM_COM_NAME_" />
	<input type="hidden" name="id" value="<?php echo $this->data->id; ?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="" />

	<?php echo JHTML::_('form.token'); ?>
	<button type="button" onclick="submitbutton('save')"><?php echo JText::_('Save'); ?></button>
	<button type="button" onclick="submitbutton('cancel')"><?php echo JText::_('Cancel'); ?></button>
</form>