<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 29-Dec-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

if('package' == $this->project->type)
    return;

$buildOpts = $this->project->buildOpts;
?>

<div class="ecr_floatbox">
    <div class="infoHeader imgbarleft icon24-locale">
        <?php echo jgettext('Languages') ?>
    </div>
    <ul>
        <li>
            <label for="extension_prefix" class="creditsLabel hasTip"
                   title="<?php echo jgettext('Extension prefix').'::'.jgettext('A custom prefix for your extension.'); ?>">
                <?php echo jgettext('Extension prefix'); ?>
            </label>
            <input type="text" name="buildvars[extensionPrefix]" id="extension_prefix"
                   size="10" value="<?php echo $this->project->extensionPrefix; ?>"/>
        </li>

        <li>
            <label for="lng_separate_javascript" class="creditsLabel hasTip"
                   title="<?php echo jgettext('Separate JavaScript').'::'.jgettext('Separate JavaScript from PHP translations'); ?>">
                <?php echo jgettext('Separate JavaScript'); ?>
            </label>
            <input type="checkbox" name="buildopts[]" value="lng_separate_javascript" id="lng_separate_javascript"
                <?php echo (isset($buildOpts['lng_separate_javascript'])
                && $buildOpts['lng_separate_javascript'] == 'ON')
                ? ' checked="checked"'
                : ''; ?>
                />
        </li>
    </ul>

    <strong>
    <?php echo jgettext('Language file format'); ?>
    </strong>
    <br/>

    <input type="radio" id="langformat_ini" name="buildvars[langFormat]" value="ini"
        <?php echo ($this->project->langFormat == 'ini') ? ' checked="checked"' : ''; ?>
        />
    <label class="inline" for="langformat_ini">INI</label>
    <br/>

    <input type="radio" id="langformat_nafuini" name="buildvars[langFormat]" value="nafuini"
        <?php echo ($this->project->langFormat == 'nafuini') ? ' checked="checked"' : ''; ?>
        />
    <label class="inline" for="langformat_nafuini">NAFUINI *</label>
    <br/>

    <input type="radio" id="langformat_po" name="buildvars[langFormat]" value="po"
        <?php echo ($this->project->langFormat == 'po') ? ' checked="checked"' : ''; ?>
        />
    <label class="inline" for="langformat_po">PO *</label>


    <br/>
    <br/>
    <?php echo jgettext("* Note that those language file formats need a special language handler called \"g11n\""); ?>
    <br />
    <?php echo sprintf(jgettext("Please refer to <a href=\"%s\">Wiki.Joomla-Nafu.de</a> for more information.")
    , 'http://wiki.joomla-nafu.de/joomla-dokumentation/Benutzer:Elkuku/Proyektz/g11n'); ?>
</div>
