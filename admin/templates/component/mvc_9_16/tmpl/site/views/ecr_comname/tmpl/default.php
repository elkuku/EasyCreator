<?php
##*HEADER*##

JHTML::stylesheet('default.css', 'components/ECR_COM_COM_NAME/assets/css/');

?>
<div id="ECR_COM_COM_NAME_content">
	<h1 class="componentheading">ECR_COM_NAME</h1>
<?php if( ! $this->data) : ?>
    <h3><?php echo JText::_('No records found'); ?></h3>
<?php else : ?>
<?php $row = $this->data; ?>
<!--site.viewitem.div.ECR_COM_TBL_NAME.divrow-->
<?php endif; ?>
</div>
