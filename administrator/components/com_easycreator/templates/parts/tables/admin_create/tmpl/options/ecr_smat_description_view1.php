<fieldset class="adminform"><legend><?php echo JText::_('Description'); ?></legend>
<table class="admintable">
	<tr>
		<td valign="top" colspan="3"><?php
        echo $this->editor->display('description'
        , $this->item->description, '550', '300', '60', '20', array('pagebreak', 'readmore')) ;
        ?></td>
	</tr>
</table>
</fieldset>
