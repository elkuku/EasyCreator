<?php
##*HEADER*##

?>
<?php foreach($this->items as $i => $item): ?>
    <?php $link = JRoute::_('index.php?option=_ECR_COM_COM_NAME_&controller=_ECR_COM_TBL_NAME_&task=edit&cid[]='.$item->id); ?>
    <tr class="row<?php echo $i % 2; ?>">
		<td>
			<a href="<?php echo $link; ?>">
				<?php echo $row->id; ?>
			</a>
		</td>
		<td>
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
<!--admin.viewlist.table._ECR_COM_TBL_NAME_.cell-->
	</tr>
<?php endforeach; ?>
