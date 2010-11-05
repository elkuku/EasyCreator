<?php
##*HEADER*##

?>

<h1>
    <?php echo $this->item->greeting;?>
    <?php if($this->item->params->get('show_category') && ! empty($this->category->title)):?>
            (<?php echo $this->category->title;?>)
    <?php endif;?>
</h1>
