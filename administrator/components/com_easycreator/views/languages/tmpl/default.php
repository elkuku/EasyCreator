<?php defined('_JEXEC') || die('=;)');
/**
 * @package       EasyCreator
 * @subpackage    Views
 * @author        Nikolai Plath
 * @author        Created on 10-Oct-2009
 * @license       GNU/GPL, see JROOT/LICENSE.php
 */

$langs = JFactory::getLanguage()->getKnownLanguages();

//-- Joomla! 1.6 test language
if(array_key_exists('xx-XX', $langs)) unset($langs['xx-XX']);

//-- Order en-GB at first position
if(count($langs) > 1) :
    $l = array('en-GB' => $langs['en-GB']);
    unset($langs['en-GB']);
    $langs = $l + $langs;
endif;

$scopes = $this->project->getLanguagePaths();
?>

<table class="table table-striped table-bordered table-condensed">
    <thead>
    <tr>
        <td>&nbsp;</td>
        <?php
        foreach($scopes as $scope => $path) :
            echo '<th>'.jgettext($scope).'</th>';
        endforeach;
        ?>
    </tr>
    </thead>

    <tbody>
    <?php foreach($langs as $tag => $lang): ?>
    <tr>
        <td width="10%" nowrap="nowrap">
            <strong><?php echo $tag; ?></strong>
            <br/>
            <?php echo $lang['name']; ?>
        </td>
        <?php foreach($scopes as $scope => $path) : ?>
        <td>
            <?php EcrLanguageHelper::checkFile($this->project, $tag, $scope); ?>
        </td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<input type="hidden" id="lng_scope" name="lng_scope" value=""/>
<input type="hidden" id="lngcreate_lang" name="lngcreate_lang" value=""/>
