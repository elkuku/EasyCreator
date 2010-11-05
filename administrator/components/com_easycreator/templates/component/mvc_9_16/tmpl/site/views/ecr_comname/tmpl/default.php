<?php
##*HEADER*##

JHTML::stylesheet('default.css', 'components/_ECR_COM_COM_NAME_/assets/css/');

?>
<div id="_ECR_COM_COM_NAME__content">
	<h1 class="componentheading">_ECR_COM_NAME_</h1>
<?php if( ! $this->data) : ?>
    <h3><?php echo JText::_('No records found'); ?></h3>
<?php else : ?>
<?php $row = $this->data; ?>
<!--site.viewitem.div._ECR_COM_TBL_NAME_.divrow-->
<?php endif; ?>
</div>
