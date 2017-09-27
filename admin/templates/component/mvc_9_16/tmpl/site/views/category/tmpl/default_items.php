<?php
##*HEADER*##

?>
<script language="javascript" type="text/javascript">
function tableOrdering(order, dir, task)
{
    var form = document.adminForm;

    form.filter_order.value = order;
    form.filter_order_Dir.value = dir;

    document.adminForm.submit(task);
}
</script>

<form action="<?php echo JFilterOutput::ampReplace($this->action); ?>" method="post" name="adminForm">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="right" colspan="4">
        <?php
            echo JText::_('Display Num').'&nbsp;';
            echo $this->pagination->getLimitBox();
        ?>
        </td>
    </tr>
<?php if ($this->params->def('show_headings', 1)) : ?>
    <tr>
        <td width="10" style="text-align:right;"
            class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
            <?php echo JText::_('Num'); ?>
        </td>
        <td width="10" style="text-align:right;"
            class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
            <?php echo JText::_('Id'); ?>
        </td>
<!--site.viewcategory.table.ECR_COM_TBL_NAME.header-->
    </tr>
<?php endif; ?>

<?php foreach($this->items as $item) : ?>
    <tr class="sectiontableentry<?php echo $item->odd + 1; ?>">
        <td align="right">
            <?php echo $this->pagination->getRowOffset($item->count); ?>
        </td>
        <td height="20">
            <?php echo $item->link; ?>
        </td>
<!--site.viewcategory.table.ECR_COM_TBL_NAME.cell-->
    </tr>
<?php endforeach; ?>

    <tr>
        <td align="center" colspan="4"
            class="sectiontablefooter<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
        <?php echo $this->pagination->getPagesLinks(); ?>
        </td>
    </tr>
    <tr>
        <td colspan="4" align="right" class="pagecounter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </td>
    </tr>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>
