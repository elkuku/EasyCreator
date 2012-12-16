<?php
/**
 * @package    EasyCreator
 * @subpackage  Views
 * @author      Nikolai Plath (elkuku)
 * @author      Created on 08-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

if( ! $this->easyLanguage)
{
    EcrHtml::message(jgettext('No languages defined'), 'error');

    return;
}

JHTML::_('behavior.modal', 'a.ecr_modal');
$input = JFactory::getApplication()->input;
$lang_first_line_comment = 3;
$lang_first_line_comment_cnt = 0;
$tableHeader = '';
$tableHeader .= NL.'<table class="adminlist">';
$tableHeader .= NL.'<thead>';
$tableHeader .= '<tr>';
$tableHeader .= '<th style="background-color: #CCE5FF;">'.jgettext('KEY').'</th>';

foreach($this->languages as $lang)
{
    if(in_array($lang, $this->hideLangs))
        continue;

    $tableHeader .= '<th width="'.(100 / (count($this->languages) + 2)).'%">'.$lang.'</th>';
}//foreach

$tableHeader .= '<th style="background-color: #FFFFB2;">'.jgettext('Used in File').'</th>';
$tableHeader .= '</tr>';
$tableHeader .= '</thead>'.NL;
$sliderDrawed = false;
$started = false;

$lang_fileanalysis_fold = $input->get('lang_fileanalysis_fold');
$lang_fileanalysis_comment_num = $input->getInt('lang_fileanalysis_comment_num', 0);
$lang_fileanalysis_active = $input->getInt('lang_fileanalysis_active', 0);

$checked =($lang_fileanalysis_fold) ? ' checked="checked"' : '';

if($checked)
{
    jimport('joomla.html.pane');
}
?>
<h2><?php echo jgettext('Language files analysis'); ?> :: <span style="color: green;">
<?php echo ucfirst($this->scope); ?></span></h2>
<div style="background-color: #ffffdd;">
<?php
    $selector = '<select class="span1" name="lang_fileanalysis_comment_num"'
    .' onchange="submitbutton(\'translations\');">';

    for($i = 0; $i < 10; $i++)
    {
        $selected =($lang_fileanalysis_comment_num == $i) ? ' selected="selected"' : '';
        $selector .= '<option'.$selected.'>'.$i.'</option>';
    }//for
    $selector .= '</select>';
    echo sprintf(jgettext('Treat the first %s lines as comment'), $selector);
    ?>
    &nbsp;&bull;&nbsp;
    <input type="checkbox" name="lang_fileanalysis_fold"
        id="lang_fileanalysis_fold" value="lang_fileanalysis_fold"
        onclick="submitbutton('translations');" <?php echo $checked; ?>>
    <label class="inline" for="lang_fileanalysis_fold"><?php echo jgettext('Fold'); ?></label>
</div>
<hr />
<?php
$k = 0;
$folder_num = -1;
$fieldID = 0;
$ecr_project = $input->get('ecr_project');
$baseLink = 'index.php?option=com_easycreator&amp;task=translate&amp;tmpl=component'
    .'&amp;view=languages&amp;controller=languages';

$baseLink .= '&amp;ecr_project='.$ecr_project;
$baseLink .= '&amp;scope='.$this->scope;

foreach($this->default_file as $line)
{
    foreach($line as $key => $value)
    {
        switch($key)
        {
            case 'comment':
                if($lang_first_line_comment_cnt < $lang_fileanalysis_comment_num)
                {
                    echo $value.'<br />';
                    $lang_first_line_comment_cnt ++;
                }
                else
                {
                    if($lang_fileanalysis_fold)
                    {
                        $value = substr($value, 1);

                        if($sliderDrawed)
                        {
                            echo '</table>';
                            echo $pane2->endPanel();
                            echo $pane2->startPanel($value, $value.'-ini-analysis-page');
                            echo $tableHeader;
                        }
                        else
                        {
                            $pane2 =& JPane::getInstance('sliders'
                            , array('startOffset' => $lang_fileanalysis_active, 'startTransition' => ''));

                            echo $pane2->startPane('fold-pane');
                            echo $pane2->startPanel($value, $value.'-analysis-page');
                            echo $tableHeader;
                            $sliderDrawed = true;
                        }

                        $k = 0;
                        $folder_num ++;
                    }
                    else
                    {
                        if( ! $started)
                        {
                            echo $tableHeader;
                            $started = true;
                        }

                        echo NL.'<tr>';
                        echo '<td colspan="'.(count($this->languages) + 2)
                        .'" style="background-color: #FFE5B2;">'.$value.'</td>';

                        echo '</tr>';
                    }
                }
            break;

            case 'key':
                $lang_first_line_comment_cnt = $lang_fileanalysis_comment_num;

                if( ! $sliderDrawed && ! $started)
                {
                    echo $tableHeader;
                    $started = true;
                }

                echo NL.'<tr class="row'.$k.'">';

                foreach($value as $skey => $svalue)
                {
                    echo '<td align="left"><span style="color: #666666;">'.$skey.'</span></td>';

                    foreach($this->languages as $lang)
                    {
                        if(in_array($lang, $this->hideLangs))
                        {
                            continue;
                        }

                        $fieldID ++;
                        $link = $baseLink;
                        $link .= '&amp;trans_lang='.$lang;
                        $link .= '&amp;trans_key='.$skey;
                        $link .= '&amp;field_id='.$fieldID;

                        echo '<td>';
                        ?>
                        <a class="ecr_modal" title="<?php echo jgettext('Click to translate'); ?>"
                          href="<?php echo $link; ?>"
                          rel="{handler: 'iframe', size: {x: 820, y: 310}}"
                          id="trfield_<?php echo $fieldID; ?>">
                                <?php
                                $tmpStrings =(isset($this->strings[$skey][$lang]))
                                    ? $this->strings[$skey][$lang]
                                    : array();

                                displayField($lang, $skey, $tmpStrings);
                                ?>
                        </a>
                        <?php
                        echo '</td>';
                    }//foreach
                    $used = false;
                    echo '<td>';

                    foreach($this->definitions as $definition)
                    {
                        if($skey == strtoupper($definition->definition))
                        {
                            foreach($this->languages as $lang)
                            {
                                if(in_array($lang, $this->hideLangs))
                                {
                                    continue;
                                }

                                if(isset($this->_strings[$skey][$lang]) && $this->_strings[$skey][$lang])
                                {
                                    $definition->translated[] = $lang;
                                }

                                if(isset($this->_coreStrings[$skey][$lang]) && $this->_coreStrings[$skey][$lang])
                                {
                                    $definition->coreTranslated[] = $lang;
                                }
                            }//foreach

                            foreach($definition->files as $fName => $fCount)
                            {
                                if($this->scope == 'menu')
                                {
                                    echo '<span style="background-color: #FFFFB2;">'.$fCount.'</span><br />';
                                }
                                else
                                {
                                    echo '<span style="background-color: #FFFFB2;">'.JFile::getName($fName)
                                    .'('.$fCount.') (<strong class="hasTip" style="color:blue;" title="'
                                    .$fName.'">'.jgettext('PATH').'</strong>)</span><br />';
                                }
                            }//foreach
                            $used = true;
                        }
                    }//foreach

                    if( ! $used)
                    {
                        echo '<strong style="color: red;">'.jgettext('NOT USED').'</strong>';
                    }
                }//foreach
                echo '</td>';
                echo '</tr>';
            break;

            case 'version':
            case 'etc':

            break;
        }//switch
        $k = 1 - $k;
    }//foreach
}//foreach

echo '</table>';

if($sliderDrawed)
{
    echo $pane2->endPanel();
    echo $pane2->endPane();
}

function displayField($lang, $key, $value)
{
    if( ! $value)
    {
        echo '<strong style="color: red">'.jgettext('Empty').'</strong>';

        return;
    }

    if($value == '**TRANSLATE**')
    {
        echo '<strong style="color: red">'.jgettext('Translate').'</strong>';
    }
    else
    {
        echo EcrHtml::cleanHTML($value);
    }
}//function
