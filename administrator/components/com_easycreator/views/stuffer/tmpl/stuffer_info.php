<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 02-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
?>

<?php echo ecrHTML::floatBoxStart(); ?>
<div class="infoHeader imgbarleft icon-24-info">
    <?php echo jgettext('Info') ?>
</div>
<ul>
<li>
    <label class="creditsLabel">
        <?php echo jgettext('Extension type'); ?>
    </label>
    <span class="img icon-12-<?php echo $this->project->type ?>">
    <?php echo $this->project->type ?></span>
</li>
<li>
    <label class="creditsLabel">
        <?php echo jgettext('Name'); ?>
    </label>
    <?php echo $this->project->name; ?>
</li>
<li>
    <label class="creditsLabel">
        <?php echo jgettext('Extension name'); ?>
    </label>
    <?php echo $this->project->comName; ?>
</li>
    <?php if($this->project->scope): ?>

<li>
    <label class="creditsLabel">
        <?php echo jgettext('Component scope'); ?>
    </label>
    <?php echo $this->project->scope ?>
</li>
    <?php endif; ?>

<li>
    <label class="creditsLabel" for="buildvars_version">
        <?php echo jgettext('Version'); ?>
    </label>
    <input type="text" id="buildvars_version" name="buildvars[version]"
    size="10" value="<?php echo $this->project->version; ?>" />
</li>
<li>
    <label class="creditsLabel" for="buildvars_description">
        <?php echo jgettext('Description'); ?>
    </label>
    <input type="text" id="buildvars_description" name="buildvars[description]"
    size="50" value="<?php echo $this->project->description; ?>" />
</li>
<li>
    <label class="creditsLabel">
        <?php echo jgettext('From template'); ?>
    </label>
    <?php echo ($this->project->fromTpl) ? $this->project->fromTpl : '?'; ?>
</li>
</ul>
<?php echo ecrHTML::floatBoxEnd();
