<?php
##*HEADER*##

?>
<div class="content">
<?php foreach($this->data as $data) : ?>
<!--viewitem.div.ECR_COM_TBL_NAME.site.divrow-->
	<?php foreach($data as $key => $value) : ?>
        <strong>
            <?php echo $key; ?>
        </strong>&nbsp;<?php echo $value; ?>
        <br />
    <?php endforeach; ?>
    <hr />
<?php endforeach; ?>
</div>
