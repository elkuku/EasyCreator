<?php
##*HEADER*##

?>
<tr>
	<th width="5"><?php echo JText::_('_ECR_COM_COM_NAME__ECR_COM_NAME__ID'); ?></th>
	<th width="20">
		<input type="checkbox" name="toggle" value=""
		onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>
	<th><?php echo JText::_('_ECR_COM_COM_NAME__ECR_COM_NAME_GREETING'); ?></th>
</tr>
