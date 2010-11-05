<?php
##*HEADER*##

JHTML::_('behavior.tooltip');

##ECR_MAT_ORDERING_VIEW0##
?>
<form action="index.php" method="post" name="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_('Filter'); ?>:
			<input type="text" name="search" id="search" value="
			<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />

			<button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
			<button onclick="document.getElementById('search').value='';
				this.form.getElementById('filter_item').value='';this.form.submit();">
				<?php echo JText::_('Reset'); ?></button>
		</td>
		<td nowrap="nowrap">
			<?php echo $this->lists['type']; ?>
		</td>
	</tr>
</table>
<div id="tablecell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_('NUM'); ?>
			</th>
			<th width="5%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->elements); ?>);" />
			</th>
			<th class="title">
				<?php echo JHTML::_('grid.sort', 'Title', 'title', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			##ECR_MAT_DESCRIPTION_VIEW1##
			##ECR_MAT_PUBLISHED_VIEW1##
			##ECR_MAT_ORDERING_VIEW1##
			<th width="1%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort', 'id', 'id', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
		</tr>
	</thead>

	<tbody>
	<?php
    $k = 0;

    for($i = 0, $n = count($this->items); $i < $n; $i++)
    {
        $row = &$this->items[$i];
        $link 		= JRoute::_('index.php?option=com__ECR_COM_NAME_&controller=_ECR_ELEMENT_NAME_&task=edit&cid[]='.$row->id);

        $checked = JHTML::_('grid.id', $i, $row->id);
        ##ECR_MAT_PUBLISHED_VIEW11##
    ?>
		<tr class="<?php echo "row$k"; ?>">
			<td width="5%">
				<?php echo $this->pagination->getRowOffset($i); ?>
			</td>
			<td width="5%">
				<?php echo $checked; ?>
			</td>
			<td>
				<span class="editlinktip hasTip"
					title="<?php echo JText::_('Edit _ECR_ELEMENT_NAME_');?>::<?php echo $row->title; ?>">
					<a href="<?php echo $link  ?>">
					    <?php echo $row->title; ?>
					</a>
				</span>
			</td>
			##ECR_MAT_DESCRIPTION_VIEW2##
			##ECR_MAT_PUBLISHED_VIEW2##
			##ECR_MAT_ORDERING_VIEW2##
			<td align="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
            $k = 1 - $k;
        }//for
        ?>
	</tbody>
    <tfoot>
    <tr>
      <td colspan="13"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  </tfoot>
	</table>
</div>
	<input type="hidden" name="option" value="com__ECR_COM_NAME_" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<input type="hidden" name="controller" value="_ECR_ELEMENT_NAME_" />
	<input type="hidden" name="view" value="_ECR_ELEMENT_NAME_s" />
	<?php echo JHTML::_('form.token'); ?>
</form>
