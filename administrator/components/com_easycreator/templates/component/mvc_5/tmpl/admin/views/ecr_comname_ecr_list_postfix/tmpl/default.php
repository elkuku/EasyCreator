<?php
##*HEADER*##

?>
<form action="index.php" method="post" name="adminForm">
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_('ID'); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
			</th>
<!--admin.viewlist.table.ECR_COM_TBL_NAME.header-->
		</tr>
	</thead>
	<?php
    $k = 0;
    for($i = 0, $n = count($this->items); $i < $n; $i++):
        $row = &$this->items[$i];
        $checked = JHTML::_('grid.id', $i, $row->id);
        $link = JRoute::_('index.php?option=ECR_COM_COM_NAME&controller=ECR_COM_TBL_NAME&task=edit&cid[]='.$row->id);

        ?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<a href="<?php echo $link; ?>">
					<?php echo $row->id; ?>
				</a>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
<!--admin.viewlist.table.ECR_COM_TBL_NAME.cell-->
		</tr>
		<?php
        $k = 1 - $k;
    endfor;
    ?>
	 <tfoot>
    <tr>
      <td colspan="#_ECR_ADMIN_LIST_COLSPAN_#">
      	<?php echo $this->pagination->getListFooter(); ?>
      </td>
    </tr>
  </tfoot>
	</table>
</div>

<input type="hidden" name="option" value="ECR_COM_COM_NAME" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="ECR_COM_TBL_NAME" />
</form>
