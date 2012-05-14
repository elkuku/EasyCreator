<?php
##*HEADER*##

?>

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
                <input class="text_area" type="text" name="greeting" id="greeting" size="32"
                maxlength="250" value="<?php echo $this->ECR_COM_NAME->greeting;?>" />
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
                echo $editor->display('content', $this->ECR_COM_NAME->content, '550', '400', '60', '20', false);
                ?>
            </td>
        </tr>
    </table>
    </fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="ECR_COM_COM_NAME" />
<input type="hidden" name="id" value="<?php echo $this->ECR_COM_NAME->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="ECR_COM_TBL_NAME" />
</form>
