<?php
/**
 * @version SVN: $Id$
 * @package
 * @subpackage
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 26.05.2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$buildOpts = $this->project->buildOpts;

?>

<?php echo ecrHTML::floatBoxStart(); ?>

<div class="infoHeader img icon-24-package_creation">
	<?php echo jgettext('Package'); ?>
</div>

<br />

<strong class="img icon-16-joomla">
    <?php echo jgettext('Joomla! version'); ?>
</strong>
<br />

<input type="radio" id="jversion15" name="jcompat" value="1.5"
<?php echo ($this->project->JCompat == '1.5') ? ' checked="checked"' : ''; ?>
/>
<label for="jversion15" class="img32b icon-joomla-compat-15"></label>

<input type="radio" id="jversion16" name="jcompat" value="1.6"
<?php echo ($this->project->JCompat == '1.6') ? ' checked="checked"' : ''; ?>
/>
<label for="jversion16" class="img32b icon-joomla-compat-16"></label>

<br />
<br />

<strong>
    <?php echo jgettext('Compression'); ?>
</strong>
<?php ecrHTML::drawPackOpts($buildOpts); ?>

<br />

<strong>
<?php echo jgettext('Options'); ?>
</strong>
<br />
<input type="checkbox" name="buildopts[]" id="lbl_create_index_html"
 <?php echo (isset($buildOpts['create_indexhtml']) && $buildOpts['create_indexhtml'] == 'ON') ? ' checked="checked"' : ''; ?>
 value="create_indexhtml" />
<label for="lbl_create_index_html"><?php echo jgettext('Create index.html files'); ?></label>
<br />

<input type="checkbox" name="buildopts[]" id="lbl_create_md5"
 <?php echo (isset($buildOpts['create_md5']) && $buildOpts['create_md5'] == 'ON') ? ' checked="checked"' : ''; ?>
 value="create_md5" />
<label for="lbl_create_md5"><?php echo jgettext('Create MD5 checksum file'); ?></label>
<br />
<br />

<strong class="img icon-16-easy-joomla"><?php echo jgettext('EasyCreator Options'); ?></strong>
<br />

<input type="checkbox" name="buildopts[]" id="lbl_include_ecr_projectfile"
 <?php echo (isset($buildOpts['include_ecr_projectfile']) && $buildOpts['include_ecr_projectfile'] == 'ON')
 ? ' checked="checked"'
 : ''; ?>
 value="include_ecr_projectfile" />
<label for="lbl_include_ecr_projectfile"><?php echo jgettext('Include EasyCreator Project file'); ?></label>
<br />

<input type="checkbox" name="buildopts[]" id="lbl_remove_autocode"
 <?php echo (isset($buildOpts['remove_autocode']) && $buildOpts['remove_autocode'] == 'ON') ? ' checked="checked"' : ''; ?>
 value="remove_autocode" />
<label for="lbl_remove_autocode"><?php echo jgettext('Remove EasyCreator AutoCode'); ?></label>

<?php echo ecrHTML::floatBoxEnd();
