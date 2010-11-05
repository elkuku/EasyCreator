<?php
##*HEADER*##

JHTML::stylesheet('default.css', 'components/_ECR_COM_COM_NAME_/assets/css/');

?>
<div id="_ECR_COM_COM_NAME__content">
	<div class="componentheading">_ECR_COM_NAME_</div>

    <?php if( ! count($this->data)) : ?>
	    <h3><?php echo JText::_('No_records found'); ?></h3>
    <?php endif; ?>

<?php foreach($this->data as $row) : ?>
<!--site.viewitem.div._ECR_COM_TBL_NAME_.divrow-->
<?php endforeach; ?>
</div>