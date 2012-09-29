<?php
/**
 * @package       EasyCreator
 * @subpackage    Help
 * @author        Nikolai Plath (elkuku)
 * @author        Created on 15-Jul-2009
 * @license       GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$input = JFactory::getApplication()->input;

$pathHelp = JPATH_COMPONENT.DS.'helpers'.DS.'jclasslists';
$fName = 'jclasslist_'.str_replace('.', '_', JVERSION);

//-- Add javascript
ecrScript('jhelp', 'php2js');

if(false == JFile::exists($pathHelp.DS.$fName.'.php')):
    //-- Class list not found
    EcrHtmlButton::createClassList();
    echo '<div id="jsonDebug"></div>';

    return;
endif;

JLoader::import($fName, $pathHelp);

JHTML::_('behavior.tooltip');

$out_format = $input->get('out_format', 'source');
if($out_format == '') $out_format = 'source';

$className = $input->get('className');
$methodName = $input->get('methodName');
$packageName = $input->get('packageName');

$formats = array(
    array('Source', 'source', 'source', 'local')
, array('api.joomla.org'
    , 'api.joomla.org/Joomla-Platform/[package]/[class].html#[method]'
    , 'api', 'remote')
, array('docs.joomla.org', 'docs.joomla.org/[class]/[method]', 'docs', 'remote')
, array('wiki.joomla-nafu.de'
    , 'wiki.joomla-nafu.de/joomla-dokumentation/Joomla!_Programmierung/Framework/[class]/[method]'
    , 'nafu', 'remote')
, array('Google', 'google.com/search?q=joomla!+framework+[class]+[method]', 'googlecom', 'remote')
);

$local_api_copy = JComponentHelper::getParams('com_easycreator')->get('local_api_copy');

if($local_api_copy)
{
    if(strrpos($local_api_copy, '/') + 1 != strlen($local_api_copy))
    {
        //-- Add a slash
        $local_api_copy .= '/';
    }

    if(strpos($local_api_copy, '//'))
    {
        $local_api_copy = substr($local_api_copy, strpos($local_api_copy, '//') + 2);
    }

    $formats[] = array('api local', $local_api_copy.'[package]/[class].html#[method]', 'apilocal', 'local');
}
else
{
    echo '<span style="float: right; background-color: #ffc;">';
    echo jgettext('No local API copy specified. you may do this in configuration.');
    echo '</span>';
}

$cList = getJoomlaClasses();
$packages = getJoomlaPackages();

ecrLoadMedia('php_file_tree');

$fileTree = new EcrFileTree('', '', " onclick=\"changeFrame('[folder]', '[file]');\"");
$fileTree->setDir($pathHelp);
$fileTree->showExtension = false;

?>
<script type="text/javascript">
    var fId = '<?php echo $out_format; ?>';
    var ECR_DEBUG = <?php echo (ECR_DEBUG) ? 'true' : 'false'; ?>;
</script>

<table width="100%">
    <tr valign="top">
        <td width="5%" nowrap="nowrap">
            <div class="ecr_floatbox">
		  <span class="img icon16-joomla"
                style="font-size: 1.4em; font-weight: bold;">Joomla! Framework</span>

                <div style="float: right;"><?php echo JVERSION; ?></div>
                <div class="php-file-tree">
                    <ul>
                        <li class="pft-directory">
                            <div style="font-size: 1.3em;"><?php echo jgettext('By Package'); ?></div>
                            <ul><?php
                                natcasesort($packages);

                                foreach($packages as $pName):
                                    echo EcrHtml::idt('+').'<li class="pft-directory"><div>'.$pName.'</div>';
                                    echo EcrHtml::idt('+').'<ul>';

                                    foreach($cList as $cName => $cl):

                                        if($cl[0] != $pName):
                                            continue;
                                        endif;

                                        echo EcrHtml::idt('+').'<li class="pft-directory"><div>'.$cName.'</div>';
                                        echo EcrHtml::idt('+').'<ul>';
                                        $ms = $cl[2];
                                        natcasesort($ms);
                                        echo EcrHtml::idt()
                                            .'<li class="pft-file ext-joo" onclick="changeFrame('
                                            ."'$cName', 'NULL', '$cl[0]'".');" style="font-weight: bold;">'
                                            .$cName
                                            .'</li>';

                                        foreach($ms as $m):
                                            echo EcrHtml::idt()
                                                .'<li class="pft-file ext-joo" onclick="changeFrame('
                                                ."'$cName', '$m', '$cl[0]'".');">'.$m.'</li>';
                                        endforeach;

                                        echo EcrHtml::idt('-').'</ul>';
                                        echo EcrHtml::idt('-').'</li>';
                                    endforeach;

                                    echo EcrHtml::idt('-').'</ul>';
                                    echo EcrHtml::idt('-').'</li>';
                                endforeach;
                                ?>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="php-file-tree">
                    <ul>
                        <li class="pft-directory">
                            <div style="font-size: 1.3em;"><?php echo jgettext('By Name'); ?></div>
                            <ul><?php
                                $ltr = '';
                                foreach($cList as $cName => $cl):
                                    $t = substr($cName, 1, 1);
                                    if($t != $ltr):
                                        if($ltr):
                                            echo EcrHtml::idt('-').'</ul>';
                                            echo EcrHtml::idt('-').'</li>';
                                        endif;

                                        $title = (substr($cName, 0, 1) == 'J') ? 'J____'.strtoupper($t) : $cName;
                                        echo EcrHtml::idt('+').'<li class="pft-directory"><div>'.$title.'</div>';
                                        echo EcrHtml::idt('+').'<ul>';
                                        $ltr = $t;
                                    endif;

                                    echo EcrHtml::idt('+').'<li class="pft-directory"><div>'.$cName.'</div>';
                                    echo EcrHtml::idt('+').'<ul>';
                                    $ms = $cl[2];
                                    natcasesort($ms);
                                    echo EcrHtml::idt()
                                        .'<li class="pft-file ext-joo" onclick="changeFrame('
                                        ."'$cName', 'NULL', '$cl[0]'".');" style="font-weight: bold;">'.$cName.'</li>';

                                    foreach($ms as $m):
                                        echo EcrHtml::idt()
                                            .'<li class="pft-file ext-joo" onclick="changeFrame('
                                            ."'$cName', '$m', '$cl[0]'".');">'.$m.'</li>';
                                    endforeach;

                                    echo EcrHtml::idt('-').'</ul>';
                                    echo EcrHtml::idt('-').'</li>';
                                endforeach;

                                echo EcrHtml::idt('-').'</ul>';
                                echo EcrHtml::idt('-').'</li>';
                                ?>

                            </ul>
                        </li>
                    </ul>
                </div>

                <div style="border: 1px solid orange; padding: 0.5em; background-color: #eee;">
                    <strong><?php echo jgettext('So many empty pages on the wiki ?'); ?></strong>
                    <ul style="list-style: none; padding-left: 0.5em;">
                        <li class="img icon16-joomla">
                            <a href="http://docs.joomla.org/API_Reference_Project" class="external">Help
                                docs.joomla.org</a>
                        </li>
                        <li class="img icon16-joomla">
                            <a href="http://wiki.joomla-nafu.de/joomla-dokumentation/Joomla!_API_Referenz_Projekt"
                               class="external">Hilf wiki.joomla-nafu.de</a>
                        </li>
                    </ul>
                </div>
            </div>

        </td>
        <td>
            <div>
                <?php
                foreach($formats as $f):
                    $selected = ($f[2] == $out_format) ? ' active' : '';
                    echo '<span class="btn'.$selected.'" id="'.$f[2].'"'
                        .' onclick="changeOutFormat(\''.$f[2].'\', \''.$f[1].'\');">'.$f[0].'</span>'.NL;
                endforeach;
                ?>
                <span id="linkDisplay"></span>
            </div>
            <div id="jsourceDisplay">
                <h1><?php echo jgettext('Joomla! constants'); ?></h1>
                <?php
                $Cs = get_defined_constants(true);
                $excludes = array('BR', 'NL');
                echo '<table>';
                echo '<tr>';
                echo '<th>Constant</th>';
                echo '<th>Value</th>';
                echo '</tr>';

                foreach($Cs['user'] as $C => $v)
                {
                    if(strpos($C, 'ECR') === 0) continue;
                    if(in_array($C, $excludes)) continue;

                    echo '<tr>';
                    echo '<td>'.$C.'</td>';
                    echo '<td>'.$v.'</td>';
                    echo '<tr>';
                }//foreach
                echo '</table>';
                ?>
            </div>
            <iframe id="jhelpDisplay" name="jhelpDisplay" src="" height="700px;"
                    width="100%" marginwidth="0" marginheight="0" frameborder="0">NO FRAMES IN YOUR BROWSER ??
            </iframe>
            <script type="text/javascript">
                $('jhelpDisplay').onload = function() {
                    $('linkDisplay').className = '';
                };
            </script>

        </td>
    </tr>
</table>

<input type="hidden" id="out_format" name="out_format" value="<?php echo $formats[0][2]; ?>"/>
<input type="hidden" id="out_link" name="out_link" value="<?php echo $formats[0][1]; ?>"/>
<input type="hidden" id="className" name="className" value="<?php echo $className; ?>"/>
<input type="hidden" id="methodName" name="methodName" value="<?php echo $methodName; ?>"/>
<input type="hidden" id="packageName" name="packageName" value="<?php echo $packageName; ?>"/>
