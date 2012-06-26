<?php
##*HEADER*##

?>
<h1>ECR_COM_NAME Item</h1>
<form method="post" action="index.php?do=save" class="form-horizontal well">
    <fieldset>
        <div class="control-group">
            <label class="control-label">Id</label>

            <div class="controls">
                <input type="text" class="disabled input-small" disabled="" value="<?= $this->data->id ?>"/>
                <input name="id" type="hidden" value="<?= $this->data->id ?>"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">A</label>

            <div class="controls">
                <input name="a" type="text" class="" value="<?= $this->data->a ?>"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">B</label>

            <div class="controls">
                <input name="b" type="text" class="" value="<?= $this->data->b ?>"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">C</label>

            <div class="controls">
                <input name="c" type="text" class="" value="<?= $this->data->c ?>"/>
            </div>
        </div>
    </fieldset>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit"><i class="icon-ok"></i> Save</button>
        <a class="btn" href="index.php?do=list"><i class="icon-remove"></i> Cancel</a>
    </div>
</form>
