<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 12-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

echo $this->loadTemplate('buttons')
?>

<?php foreach($this->form->getFieldSets() as $fieldSet) : ?>
<?php if('Debug' == $fieldSet->name && ! ECR_DEV_MODE) continue; ?>
<div class="ecr_floatbox">

    <div class="infoHeader imgbarleft icon24-<?php echo $fieldSet->name; ?>">
		<?php echo jgettext($fieldSet->label); ?>
    </div>

    <fieldset class="adminform">
        <ul class="adminformlist">
			<?php foreach($this->form->getFieldset($fieldSet->name) as $field): ?>
            <li>
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
            </li>
			<?php endforeach; ?>
        </ul>
    </fieldset>

</div>
<?php endforeach;
