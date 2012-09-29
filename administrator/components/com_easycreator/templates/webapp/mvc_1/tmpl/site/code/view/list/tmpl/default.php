<?php
##*HEADER*##

?>
<h1>ECR_COM_NAME List</h1>

<p>
    <a class="btn btn-success" href="index.php?do=ECR_LOWER_COM_NAME">
        <i class="icon-plus"></i> New ECR_COM_NAME
    </a>
</p>

<? if(! count($this->data)) : ?>

<div class="alert">
    No ECR_COM_NAMEs found - let's <a href="index.php?do=ECR_LOWER_COM_NAME">create a new one</a>.
</div>

<? else: ?>

<table class="table table-striped table-bordered table-condensed">
    <thead>
    <tr>
        <th width="5%">Id</th>
        <th>A</th>
        <th>B</th>
        <th>C</th>
        <th width="5%">Action</th>
    </tr>
    </thead>

    <tbody>

    <? foreach($this->data as $item) : ?>

    <tr>
        <td><?= $item->ECR_COM_TBL_NAME_id ?></td>
        <td><?= $item->a ? : '&nbsp;' ?></td>
        <td><?= $item->b ? : '&nbsp;' ?></td>
        <td><?= $item->c ? : '&nbsp;' ?></td>
        <td nowrap="nowrap">
            <a class="btn btn-mini" href="index.php?do=ECR_LOWER_COM_NAME&id=<?= $item->ECR_COM_TBL_NAME_id ?>">
                <i class="icon-edit"></i>Edit
            </a>
            <a class="btn btn-mini" href="index.php?do=delete&id=<?= $item->ECR_COM_TBL_NAME_id ?>">
                <i class="icon-remove"></i>Delete
            </a>
        </td>
    </tr>

    <? endforeach; ?>

    </tbody>
</table>

<? endif; ?>
