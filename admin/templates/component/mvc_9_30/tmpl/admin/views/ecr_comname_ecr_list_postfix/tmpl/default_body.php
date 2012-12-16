<?php
##*HEADER*##

?>
<?php foreach($this->items as $i => $row): ?>
<?php $link = JRoute::_('index.php?option=ECR_COM_COM_NAME&view=ECR_COM_TBL_NAME&layout=edit&id='
        .$row->id); ?>
<tr class="row<?php echo $i % 2; ?>">
    <td>
        <a href="<?php echo $link; ?>">
            <?php echo $row->id; ?>
        </a>
    </td>
    <td>
        <?php echo JHtml::_('grid.id', $i, $row->id); ?>
    </td>
<!--admin.viewlist.table.ECR_COM_TBL_NAME.cell-->
</tr>
<?php endforeach;
