<?php
##*HEADER*##

echo JText::sprintf('Random Users for %s', 'ECR_COM_NAME');
?>

<ul>
    <?php foreach($items as $item) : ?>
    <li>
        <?php echo JText::sprintf('%s is a randomly selected user', $item->name); ?>
    </li>
    <?php endforeach; ?>
</ul>
