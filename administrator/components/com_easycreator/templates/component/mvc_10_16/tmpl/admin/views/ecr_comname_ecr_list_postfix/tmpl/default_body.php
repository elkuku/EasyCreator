<?php
##*HEADER*##

?>
<?php foreach($this->items as $i => $item): ?>
<tr class="row<?php echo $i % 2; ?>">
	<td><?php echo $item->id; ?></td>
	<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
	<td>
		<a href="<?php echo JRoute::_('index.php?option=_ECR_COM_COM_NAME_&task=_ECR_COM_NAME_.edit&cid[]='.$item->id); ?>">
			<?php echo $item->greeting; ?>
		</a>
	</td>
</tr>
<?php endforeach;
