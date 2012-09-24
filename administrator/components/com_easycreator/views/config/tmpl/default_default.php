<?php
/**
 * User: elkuku
 * Date: 23.09.12
 * Time: 14:51
 */
?>
<?php foreach($this->form->getFieldSets() as $fieldSet) : ?>
<?php if('Debug' == $fieldSet->name && ! ECR_DEV_MODE) continue; ?>
<div class="ecr_floatbox">

    <div class="infoHeader imgbarleft icon24-<?php echo $fieldSet->name; ?>">
        <?php echo jgettext($fieldSet->label); ?>
    </div>

    <fieldset class="form-horizontal">
        <?php
        foreach($this->form->getFieldset($fieldSet->name) as $field):
            ?>
            <div class="control-group">
                <div class="control-label"><?php echo $field->label; ?></div>
                <div class="controls"><?php echo $field->input; ?></div>
            </div>
            <?php
        endforeach;
        ?>
    </fieldset>

</div>
<?php endforeach;
