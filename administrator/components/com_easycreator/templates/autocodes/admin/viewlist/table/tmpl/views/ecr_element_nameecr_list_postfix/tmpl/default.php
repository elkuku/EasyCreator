<?php
##*HEADER*##

JHTML::_('behavior.tooltip');
?>
<form action="index.php" method="post" name="adminForm">
    <table>
        <tr>
            <td align="left" width="100%">
                <?php echo JText::_('Filter'); ?>:
                <input type="text" name="search" id="search"
                 value="<?php echo $this->lists['search'];?>"
                 class="text_area" onchange="document.adminForm.submit();" />
                <button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
                <button onclick="document.getElementById('search').value='';
                    this.form.getElementById('filter_item').value='';this.form.submit();">
                <?php echo JText::_('Reset'); ?></button>
            </td>
            <td nowrap="nowrap">
                <?php //#echo $this->lists['type']; ?>
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
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(
                        <?php echo count($this->items); ?>);" />
                    </th>
                    <?php $coloumnCount = 2; ?>
                    ##ECR_VIEW1_TMPL1_THS##
                </tr>
            </thead>

            <tbody>
            <?php
            $k = 0;

            for($i = 0, $n = count($this->items); $i < $n; $i++)
            {
                $row = &$this->items[$i];
                $link = JRoute::_('index.php?option=com_ECR_COM_NAME'
                .'&controller=_ECR_LOWER_ELEMENT_NAME_&task=edit&cid[]='.$row->id);

                $checked = JHTML::_('grid.id', $i, $row->id);
            ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td width="5%">
                        <span class="editlinktip hasTip" title="<?php echo JText::_('Edit'); ?>">
                            <a href="<?php echo $link; ?>">
                                <?php echo $this->pagination->getRowOffset($i); ?>
                            </a>
                        </span>
                    </td>
                    <td width="5%">
                        <?php echo $checked; ?>
                    </td>
                    ##ECR_VIEW1_TMPL1_TDS##
                </tr>
                <?php
                    $k = 1 - $k;
                }//for
                ?>
            </tbody>
            <tfoot>
                <tr>
                  <td colspan="<?php echo $coloumnCount + 1; ?>">
                      <?php echo $this->pagination->getListFooter(); ?>
                  </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <input type="hidden" name="option" value="com_ECR_COM_NAME" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
    <input type="hidden" name="controller" value="ECR_ELEMENT_NAME" />
    <input type="hidden" name="view" value="ECR_ELEMENT_NAMEs" />
    <?php echo JHTML::_('form.token'); ?>
</form>
