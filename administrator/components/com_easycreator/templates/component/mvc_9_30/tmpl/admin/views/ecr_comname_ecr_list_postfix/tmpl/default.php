<?php
##*HEADER*##

JHtml::_('behavior.tooltip');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <table class="table table-striped table-bordered table-hover table-condensed">
        <thead><?php echo $this->loadTemplate('head');?></thead>
        <tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
        <tbody><?php echo $this->loadTemplate('body');?></tbody>
    </table>

    <div>
        <input type="hidden" name="option" value="ECR_COM_COM_NAME" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="controller" value="ECR_COM_TBL_NAME" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>

<?php
$params = JComponentHelper::getParams('ECR_COM_COM_NAME');
echo '<h4>Config values</h4>';
echo 'Emotional status: '. $params->get('emotional_status').'<br />';
echo 'Custom message: '. $params->get('my_custom_message').'<br />';

