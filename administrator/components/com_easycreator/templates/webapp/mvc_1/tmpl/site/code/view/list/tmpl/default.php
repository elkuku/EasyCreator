<?php
##*HEADER*##
?>
<h1>_ECR_COM_NAME_ List</h1>
<? if(! count($this->data)) : ?>
<div class="alert">
    Oops, no _ECR_COM_NAME_s found - let's <a href="index.php?do=_ECR_LOWER_COM_NAME_">create one</a>.
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
        <td><?= $item->_ECR_COM_TBL_NAME__id ?></td>
        <td><?= $item->a ? : '&nbsp;' ?></td>
        <td><?= $item->b ? : '&nbsp;' ?></td>
        <td><?= $item->c ? : '&nbsp;' ?></td>
        <td nowrap="nowrap">
            <a class="btn btn-mini" href="index.php?do=_ECR_LOWER_COM_NAME_&id=<?= $item->_ECR_COM_TBL_NAME__id ?>">
                <i class="icon-edit"></i>Edit
            </a>
            <a class="btn btn-mini" href="index.php?do=delete&id=<?= $item->_ECR_COM_TBL_NAME__id ?>">
                <i class="icon-remove"></i>Delete
            </a>
        </td>
    </tr>
        <? endforeach; ?>
    </tbody>
</table>

<? endif; ?>

<?php //var_dump($this->data);
