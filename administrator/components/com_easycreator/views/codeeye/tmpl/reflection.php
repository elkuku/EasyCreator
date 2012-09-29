<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 26-Mar2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

JHTML::_('behavior.modal', 'a.ecr_modal');

ecrStylesheet('doccomment');
ecrScript('doccomment');

jimport('joomla.html.pane');
$pane = JPane::getInstance('sliders');
$input = JFactory::getApplication()->input;

$the_path = $input->getPath('file_path');
$the_file = $input->getPath('file_name');
?>

<script type="text/javascript">
    var divCount = 0;

    function reflectFile(file_path, file_nameame) {
        document.id('file_path').value = file_path;
        document.id('file_name').value = file_nameame;
        submitform('reflect');
    }
</script>

<div id="ecr_title_file"></div>
<table width="100%">
    <tr valign="top">
        <td>
            <div class="ecr_floatbox">
                <?php echo drawFileTree($this->project); ?>
            </div>
        </td>

        <td>
            <?php
            if( ! $the_path || ! $the_file)
            {
                if($this->task != 'reflect'
                    && $this->task != 'aj_reflect'
                )
                {
                    ?>
                    <h3 style="color: red" align="center">ALPHA testing with PHP 5 reflection class to inspect
                        classes.</h3>
                    <h3 style="color: red" align="center">The files will be included to inspect them !!</h3>
                    <h3 style="color: red" align="center">Only try to include files containing classes !!!</h3>
                    <div align="center">
                        See: <a href="http://de.php.net/manual/en/language.oop5.reflection.php"
                                class="external">php.net/manual/reflection</a>
                    </div>
                    <div style="border: 1px dotted red;" align="center">
                        <h4>Or use your browsers <span
                            style="background-color: green; color: white; padding: 5px;">BACK</span>
                            &nbsp;button in case of <span
                                style="background-color: red; padding: 5px;">FATAL ERROR</span>s....</h4>
                    </div>
                    <?php
                }

                if($this->task == 'reflect')
                {
                    drawProject($this->project);
                }
                else if($this->task == 'aj_reflect')
                {
                    aj_drawProject($this->project);
                }
                else
                {
                    echo '<br /><br />';
                    $msgs = array();
                    $msgs[] = jgettext('Please click to');
                    $msgs[] = sprintf(jgettext('Reflect the %s project'), $this->project->comName);
                    $msgs[] = jgettext('Or click on an individual file on the left side');
                    EcrHtml::message($msgs, 'notice');
                }
            }
            else
            {
                reflect($the_path, $the_file);
            }

            echo '<div class="ecr_button" onclick="submitbutton(\'reflect\');">';
            echo '<img src="'.JURI::root().'media/com_easycreator/admin/images/splash_green.png" />';
            echo '<br />';
            echo sprintf(jgettext('Reflect the %s project'), $this->project->comName);
            echo '</div>';
//            #echo '<div class="ecr_button" onclick="submitbutton(\'aj_reflect\');">AJ Reflect</div>';
            ?>
        </td>
    </tr>
</table>
<?php

/**
 * 2 B moooved...
 */

function aj_drawProject(EcrProjectBase $project)
{
    echo '<h1>'.$project->name.'</h1>';
    echo '<h3>'.$project->comName.'</h2>';
    echo '<h3>credits..</h2>';

    $reflection = new EcrProjectReflection;

    switch($project->type)
    {
        case 'component':
            $reflection->aj_reflectProject($project);

//			$reflections = $reflection->getReflections();
//			if(ECR_DEBUG) EcrDebugger::dPrint($reflections, 'Reflection');
            break;

        default:
            echo 'Sorry '.$project->type.' not supported yet..';

            return;
            break;
    }

    /*
	?>
	<table width="100%" class="adminlist">
		<tr>
			<th>Models</th>
			<th>Tables</th>
		</tr>
		<tr valign="top">
			<td><?php displayReflectedFiles($reflections, 'models'); ?></td>
			<td><?php displayReflectedFiles($reflections, 'tables'); ?></td>
		</tr>
		<tr>
			<th>Controllers</th>
			<th>Views</th>
		</tr>
		<tr valign="top">
			<td><?php displayReflectedFiles($reflections, 'controllers'); ?></td>
			<td><?php displayReflectedFiles($reflections, 'views'); ?></td>
		</tr>
	</table>
	<?php
	*/
}

//function

/**
 * @param EcrProjectBase $project
 *
 * @return mixed
 */
function drawProject(EcrProjectBase $project)
{
    echo '<h1>'.$project->name.'</h1>';
    echo '<h3>'.$project->comName.'</h2>';
    echo '<h3>credits..</h2>';

    $reflection = new EcrProjectReflection;

    switch($project->type)
    {
        case 'component':
//    #		$comPath = 'components'.DS.$project->com_com_name;
            $reflection->reflectProject($project);

            $reflections = $reflection->getReflections();
            ECR_DEBUG ? EcrDebugger::dPrint($reflections, 'Reflection') : null;
//			{
//			echo '<pre>';
//				print_r($reflections);
//			}
//			echo '</pre>';
            break;
        default:
            echo 'Sorry '.$project->type.' not supported yet..';

            return;
            break;
    }//switch
    ?>
<table width="100%" class="adminlist">
    <tr>
        <th>Controllers</th>
        <th>Views</th>
    </tr>
    <tr valign="top">
        <td><?php displayReflectedFiles($reflections, 'controllers', $project); ?></td>
        <td><?php displayReflectedFiles($reflections, 'views', $project); ?></td>
    </tr>
    <tr>
        <th>Models</th>
        <th>Tables</th>
    </tr>
    <tr valign="top">
        <td><?php displayReflectedFiles($reflections, 'models', $project); ?></td>
        <td><?php displayReflectedFiles($reflections, 'tables', $project); ?></td>
    </tr>
</table>
<?php
}

//function

/**
 * @param                $reflections
 * @param                $type
 * @param EcrProjectBase $project
 */
function displayReflectedFiles($reflections, $type, EcrProjectBase $project)
{
    switch($type)
    {
        case 'controllers':
        case 'models':
        case 'tables':
            foreach($reflections[$type] as $name => $refl)
            {
                ?>
            <table style="border: 1px solid grey;" width="100%">
                <tr valign="top">
                    <td>
                        <strong><?php echo $name; ?></strong><br/>
                        props
                    </td>
                    <td>
                        <?php
                        foreach($refl->methods as $method)
                        {
                            echo $method->name;
                            $comment = str_replace("\t", ' ', $method->docComment);
                            $comment = str_replace("\n", '<br />', $method->docComment);

                            if($comment)
                            {
                                echo '<span class="hasTip img icon16-comment" title="'.$comment.'"></span>';
                            }

                            $link = 'index.php?option=com_easycreator&amp;ecr_project='
                                .$project->comName.'&amp;tmpl=component&amp;controller=stuffer&amp;task=display_snip';
                            $link .= '&amp;file_path='.$method->fileName; //.'&amp;the_file='.$file;
                            $link .= '&amp;start='.$method->startLine.'&amp;end='.$method->endLine;

                            $fileLocation = substr($method->fileName, strlen(JPATH_SITE))
                                .'<br />'.jgettext('Lines').' # '.$method->startLine.' - '.$method->endLine;
                            echo NL.'<a class="ecr_modal img icon16-php hasTip" title="'
                                .jgettext('View Source').'::'.$fileLocation
                                .'" rel="{handler: \'iframe\', size: {x: 950, y: 550}}" href="'.$link.'">';
                            echo NL.'</a>';
//echo NL.'<span class="doccomment_sourcecode hasTip" title="'.$fileLocation.'">';
//echo NL.'<a class="ecr_modal" rel="{handler: \'iframe\', size: {x: 950, y: 550},
//effects:Fx.Transitions.Bounce.easeOut}" href="'.$link.'">';
//echo jgettext('Source');
//echo NL.'</a></span>';

                            echo '<br />';

                            if(count($method->jcommands))
                            {
                                foreach($method->jcommands as $jcommand)
                                {
                                    echo "|-";
                                    echo $jcommand->name;
                                    echo '&nbsp;'; //<br />';
                                    echo $jcommand->params;
                                    echo '<br />';
                                }
                            }

                            echo '<pre>';
//                        #		print_r($method);
                            echo '</pre>';
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <?php
            }

            break;
        case 'views':
            foreach($reflections[$type] as $name => $types)
            {
                $cPath = '';
                ?>
            <table style="border: 1px solid grey;" width="100%">
                <tr valign="top">
                    <td>
                        <strong><?php echo $name; ?></strong><br/>
                        props
                    </td>
                    <td>
                        <?php
                        foreach($types as $tName => $refl)
                        {
                            echo '<strong>'.$tName.'</strong><br />';

                            foreach($refl->methods as $method)
                            {
                                echo '<strong style="color: blue;">'.$method->name.'</strong>';

                                if($method->docComment)
                                {
                                    $comment = nl2br($method->docComment);
                                    echo '<span class="hasTip img icon16-comment" title="docComment::'
                                        .$comment.'"></span>';
                                }

                                $link = 'index.php?option=com_easycreator&amp;ecr_project='
                                    .$project->comName.'&amp;tmpl=component&amp;controller=stuffer'
                                    .'&amp;task=display_snip';

                                $link .= '&amp;file_path='.$method->fileName; //.'&amp;the_file='.$file;
                                $link .= '&amp;start='.$method->startLine.'&amp;end='.$method->endLine;

                                $fileLocation = substr($method->fileName, strlen(JPATH_SITE))
                                    .'<br />'.jgettext('Lines').' # '.$method->startLine.' - '.$method->endLine;

                                echo NL.'<a class="ecr_modal img icon16-php hasTip" title="'
                                    .jgettext('View Source').'::'.$fileLocation.'" rel="{handler: \'iframe\','
                                    .' size: {x: 950, y: 550}}" href="'.$link.'">';

                                echo NL.'</a>';

                                echo '<br />';
                                $jViewCommands = array('assignRef' => 'orange', 'setLayout' => 'red');

                                if(count($method->jcommands))
                                {
                                    foreach($method->jcommands as $jcommand)
                                        if(array_key_exists($jcommand->name, $jViewCommands))
                                        {
                                            {
                                                echo "|-";
                                                echo '<strong style="color: '.$jViewCommands[$jcommand->name].';">'
                                                    .$jcommand->name.'</strong>';

                                                echo '&nbsp;'; //<br />';
                                                echo $jcommand->params;
                                                echo '<br />';
                                            }
                                        }
                                }

                                if( ! $cPath)
                                {
                                    $cPath = substr($method->fileName, 0
                                        , strlen($method->fileName) - strlen(JFile::getName($method->fileName)));
                                }
                            }
                        }
                        ?>
                    </td>
                    <?php
                    $templates = JFolder::files($cPath.DS.'tmpl');

                    if(count($templates))
                    {
                        echo '<td>';
                        echo '<strong>'.jgettext('Templates').'</strong>';
                        echo '<br />';

                        foreach($templates as $template)
                        {
                            echo $template.'<br />';
                            $templateVars = EcrProjectReflection::inspectTemplate($cPath.DS.'tmpl'.DS.$template);

                            if(count($templateVars))
                            {
                                foreach($templateVars as $templateVar)
                                {
                                    echo '<strong style="color: orange;">'.$templateVar->name.'</strong>&nbsp;';
                                    echo '<span class="hasTip img icon16-comment" title="Raw::'
                                        .html_entity_decode($templateVar->raw).'"></span>';
                                    echo $templateVar->params;
                                    echo '<br />';
                                }
                            }
                        }

                        echo '</td>';
                    }

                    ?>
                </tr>
            </table>
            <br/>
            <?php
            }

            break;
    }
}

/**
 * @param EcrProjectBase $project
 *
 * @return string
 */
function drawFileTree(EcrProjectBase $project)
{
    ecrLoadMedia('php_file_tree');

    $ret = '';

    $javascript = '';
    $javascript .= " onclick=\"reflectFile('[folder]', '[file]', '[id]');\"";

    $jsFolder = '';
    $fileTree = new EcrFileTree('', '', $javascript, $jsFolder);

    foreach($project->copies as $dir)
    {
        if(is_dir($dir))
        {
            $d = str_replace(JPATH_ROOT, '', $dir);
            $dspl = str_replace(DS, ' '.DS.' ', $d);
            $ret .= '<div class="file_tree_path"><strong>JROOT</strong>'.BR.$dspl.'</div>';

            $fileTree->setDir($dir);
            $ret .= $fileTree->startTree();
            $ret .= $fileTree->drawTree();
            $ret .= $fileTree->endTree();
        }
        else if(JFile::exists($dir))
        {
            $show = true;

            foreach($project->copies as $test)
            {
                if(strpos($dir, $test))
                {
                    $show = false;
                }
            }

            if($show)
            {
                //--This shows a single file not included in anterior directory list ;) - hi plugins...
                $fileName = JFile::getName(JPath::clean($dir));
                $dirName = substr($dir, 0, strlen($dir) - strlen($fileName));
                $oldDir = (isset($oldDir)) ? $oldDir : '';

                if($dirName != $oldDir)
                {
                    $d = str_replace(JPATH_ROOT, '', $dir);
                    $dspl = str_replace(DS, ' '.DS.' ', $d);
                    $ret .= '<div class="file_tree_path"><strong>JROOT</strong>'.BR.$dspl.'</div>';
                }

                $oldDir = $dirName;

                if(false == isset($fileTree))
                {
                    $fileTree = new EcrFileTree($dir, "javascript:", $javascript);
                }
                else
                {
                    $fileTree->setDir($dir);
                }

                $ret .= $fileTree->startTree();
                $ret .= $fileTree->getLink($dirName, $fileName);
                $ret .= $fileTree->endTree();

                $ret .= '<br />';
            }
        }
    }

    return $ret;
}

/**
 * reflect a class
 *
 * @param string $path from JROOT
 * @param string $file filename
 */
function reflect($path, $file)
{
    $fileName = JPATH_ROOT.DS.$path.$file;
    $fullPathFileName = JPATH_ROOT.DS.$path.DS.$file;
    $ecr_project = JFactory::getApplication()->input->get('ecr_project');

    $reflection = new EcrProjectReflection;

//#	$reflection->printDeclaredClasses();

//#	echo $path.'<br />'.$file.'<br />';

    if(false == JFile::exists($fullPathFileName))
    {
        EcrHtml::message(array(jgettext('File not found'), $fullPathFileName), 'error');
    }
    else
    {
        echo '<div class="explanation">'.$fullPathFileName.'</div>';

        if(strpos($path, 'controllers'.DS))
        {
            //we are including a controller.. maybe we should include the base controller
            $cPath = substr($path, 0, strlen($path) - strlen('controllers/'));

            if(JFile::exists(JPATH_ROOT.DS.$cPath.'controller.php'))
            {
                //include a base controller
//#				echo '<h1>Base controller controller.php included</h1>';
                include_once JPATH_ROOT.DS.$cPath.'controller.php';
            }
        }

        $allClasses = get_declared_classes();

        /*
         * WE INCLUDE A FILE !!
         * TODO whatelse ??
         */
        include_once $fullPathFileName;

        $foundClasses = array_diff(get_declared_classes(), $allClasses);

        if(0 == count($foundClasses))
        {
            EcrHtml::message(jgettext('No classes found'), 'error');
        }
        else
        {
            foreach($foundClasses as $clas)
            {
                $theClass = new ReflectionClass($clas);
                $parentName = $theClass->getParentClass();
                $parentName = ($parentName) ? 'Extends: <span style="color: orange;">'.$parentName->name.'</span>' : '';
                echo '<h1>';
                printf("%s%s%s %s"
                    , $theClass->isInternal() ? 'internal' : 'user-defined'
                    , $theClass->isAbstract() ? ' abstract' : ''
                    , $theClass->isFinal() ? ' final' : ''
                    , $theClass->isInterface() ? 'Interface' : 'Class'
                );
                echo ' <span style="color: green;">'.$clas.'</span> '.$parentName;
                echo '</h1>';
                echo NL.'<table width="100%"><tr valign="top"><td>';
                echo NL.'<h2>'.jgettext('Properties').'</h2>';
                echo NL.'<pre>';
                $properties = $theClass->getProperties();

                foreach($properties as $prop)
                {
                    $property = $theClass->getProperty($prop->name);

                    printf(
                        "%s%s%s%s property <strong>%s</strong>"
                        , $property->isPublic() ? ' <strong style="color: green">public</strong>' : ''
                        , $property->isPrivate() ? ' <strong style="color: orange">private</strong>' : ''
                        , $property->isProtected() ? ' <strong style="color: red">protected</strong>' : ''
                        , $property->isStatic() ? ' <strong style="color: black">static</strong>' : ''
                        , $property->getName()
                    );

                    echo '<br />';
                }

                echo NL.'</pre>';

                echo NL.'</td><td>';
                echo NL.'<pre>'.$theClass->getDocComment().'</pre>';
                echo NL.'</td></tr></table>';

//				$constants = $theClass->getConstants();
//				print_r($constants);
// Dokumentarischen Kommentar ausgeben
//printf("---> Dokumentation:\n %s\n", var_export($theClass->getDocComment(), 1));
//
//// Ausgeben, welche Interfaces von der Klasse implementiert werden
//printf("---> Implementiert:\n %s\n", var_export($theClass->getInterfaces(), 1));
//
//// Klassenkonstanten ausgeben
//printf("---> Konstanten: %s\n", var_export($theClass->getConstants(), 1));
//
//// Klasseneigenschaften ausgeben
//printf("---> Eigenschaften: %s\n", var_export($theClass->getProperties(), 1));
//
//// Klassenmethoden ausgeben
//printf("---> Methoden: %s\n", var_export($theClass->getMethods(), 1));echo '</pre>';
//                #echo $theClass->getDocComment();
//				echo '<pre>';
//				printf(
//				   "===> %s%s%s %s '%s' [extends %s]\n" .
//				   "    deklariert in %s\n" .
//				   "    Zeilen %d bis %d\n" .
//				   "    hat die Modifizierer %d [%s]\n",
//				       $theClass->isInternal() ? 'internal' : 'user-defined',
//				       $theClass->isAbstract() ? ' abstract' : '',
//				       $theClass->isFinal() ? ' final' : '',
//				       $theClass->isInterface() ? 'interface' : 'class',
//				       $theClass->getName(),
//				       var_export($theClass->getParentClass(), 1),
//				       $theClass->getFileName(),
//				       $theClass->getStartLine(),
//				       $theClass->getEndline(),
//				       $theClass->getModifiers(),
//				       implode(' ', Reflection::getModifierNames($theClass->getModifiers()))
//				);

                echo NL.'<h2>'.jgettext('Methods').'</h2>';

                $cMethods = $theClass->getMethods();

                $pane =& JPane::getInstance('sliders');
                echo $pane->startPane("the-pane");
                $indent = 0;
                $displayClassName = '';

                foreach($cMethods as $cMethod)
                {
                    $mPath = $cMethod->getFileName();

                    //--$this class or $that class ;)
                    //--..base or extended
                    //..also marks the extended extended classes orange.. TODO !
//                    #$color =( $mPath == $fullPathFileName ) ? 'green' : 'orange';
                    $titel = sprintf(
                        "%s%s%s%s%s%s Method <strong style='color: orange;'>%s</strong>",
                        $cMethod->isAbstract() ? ' abstract' : '',
                        $cMethod->isFinal() ? ' final' : '',
                        $cMethod->isPublic() ? ' <strong style="color: green">public</strong>' : '',
                        $cMethod->isPrivate() ? ' <strong style="color: orange">private</strong>' : '',
                        $cMethod->isProtected() ? ' <strong style="color: red">protected</strong>' : '',
                        $cMethod->isStatic() ? ' <strong style="color: black">static</strong>' : '',
                        $cMethod->getName()
                    );
                    $pClass = $cMethod->getDeclaringClass();
                    $s = $pClass->getName();

                    if($s != $displayClassName)
                    {
                        $indent ++;
                        echo '<h1>';
                        echo ($displayClassName) ? '<span style="color: orange">Extends</span>&nbsp;'.$s : $s;
                        echo '</h1>';
                        $displayClassName = $s;
                    }

                    $paramString = array();
                    $parameters = $cMethod->getParameters();

                    foreach($parameters as $parameter)
                    {
//                        #$color =($parameter->isOptional() ) ? 'blue' : 'brown';
                        $s = '';
                        $s .= sprintf("%s<strong style='color: brown;'>$%s</strong>",
//#					       $parameter->isOptional() ? '<strong style="color: blue;">optional</strong> ' : '',
                            $parameter->isPassedByReference() ? '<strong style="color: blue;"> & </strong>' : '',
                            $parameter->getName()
                        );

                        if($parameter->isDefaultValueAvailable())
                        {
                            $def = $parameter->getDefaultValue();

                            if($def === null)
                            {
                                $s .= '=NULL';
                            }
                            else if($def === false)
                            {
                                $s .= '=FALSE';
                            }
                            else if($def === true)
                            {
                                $s .= '=TRUE';
                            }
                            else if($def === array())
                            {
                                $s .= '=array()';
                            }
                            else if($def === '')
                            {
                                $s .= '=\'\'';
                            }
                            else
                            {
                                $s .= '='.$parameter->getDefaultValue();
                            }
                        }

                        $paramString[] = $s;
                    }

                    $paramString = implode(', ', $paramString);
                    $titel .= '( '.$paramString.' )';
//				#	echo '<h3>'.$titel.'</h3>';
//				#	$titel = '<span style="color: '.$color.'">'.$cMethod->name.'</span>';
//		#				echo str_repeat("&nbsp;&nbsp;", $indent);
//		#			echo $titel;
                    echo $pane->startPanel($titel, 'one-page');
                    //--draw a link to the source code
                    $link = 'index.php?option=com_easycreator&amp;ecr_project='.$ecr_project
                        .'&amp;tmpl=component&amp;controller=stuffer&amp;task=display_snip';

                    $link .= '&amp;file_path='.$mPath.'&amp;the_file='.$file;
                    $link .= '&amp;start='.$cMethod->getStartLine().'&amp;end='.$cMethod->getEndLine();
                    $comment = $cMethod->getDocComment();

                    if($comment)
                    {
                        echo NL.'<div style="border: 1px dotted grey; background-color: white;"'
                            .' class="doccomment_doccomment">';
//                    #	echo '<pre>';
//                    #	echo $comment;
//                    	#$comment = str_replace("\n", '<br />', $comment);
                        echo nl2br($comment);
//                        #echo $comment;
//                #		echo '</pre>';
                        echo '</div>';
                    }

                    $fileLocation = substr($cMethod->getFileName(), strlen(JPATH_SITE))
                        .'<br />'.jgettext('Lines').' # '.$cMethod->getStartLine().' - '.$cMethod->getEndLine();

                    echo NL.'<span class="doccomment_sourcecode hasTip" title="'.$fileLocation.'">';
                    echo NL.'<a class="ecr_modal" rel="{handler: \'iframe\', size: {x: 950, y: 550}'
                        .', effects:Fx.Transitions.Bounce.easeOut}"    href="'.$link.'">';

                    echo jgettext('Source code');
                    echo NL.'</a></span>';

//        #			print_r($parameters);
//#					echo var_export($cMethod->getDocComment(), 1);
//        #			echo '<br />';
                    echo NL.'<pre>';
                    // Grundlegende Informationen ausgeben

                    // Dokumentarischen Kommentar ausgeben
//#					printf("---> Dokumentation:\n %s\n", var_export($cMethod->getDocComment(), 1));

                    // Statische Variablen ausgeben, falls welche existieren
                    $statics = $cMethod->getStaticVariables();

                    if($statics)
                    {
                        printf("---> Statische Variablen: %s\n", var_export($statics, 1));
                    }

//                    #					echo '<hr />';
//                    #print_r ($cMethod);
                    echo NL.'</pre>';
//                    #					echo '<hr />';
                    echo $pane->endPanel();
                }

                echo $pane->endPane();
            }
        }
    }
}
