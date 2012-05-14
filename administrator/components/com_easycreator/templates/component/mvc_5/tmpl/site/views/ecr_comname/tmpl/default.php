<?php
##*HEADER*##

JHTML::stylesheet('default.css', 'components/ECR_COM_COM_NAME/assets/css/');

?>
<div id="ECR_COM_COM_NAME_content">
	<div class="componentheading">ECR_COM_NAME</div>

    <?php if( ! count($this->data)) : ?>
	    <h3><?php echo JText::_('No_records found'); ?></h3>
    <?php endif; ?>

<?php foreach($this->data as $row) : ?>
<!--site.viewitem.div.ECR_COM_TBL_NAME.divrow-->
<?php endforeach; ?>
</div>
