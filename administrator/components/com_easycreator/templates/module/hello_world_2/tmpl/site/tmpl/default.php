<?php
##*HEADER*##

echo JText::sprintf('Random Users for %s', '_ECR_COM_NAME_');
?>

<ul>
    <?php foreach($items as $item) : ?>
    <li>
        <?php echo JText::sprintf('%s is a randomly selected user', $item->name); ?>
    </li>
    <?php endforeach; ?>
</ul>
