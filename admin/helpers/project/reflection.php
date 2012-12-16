<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 19-Oct-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Fake some Joomla! classes for reflection.
 *
 * @param string $fName File name
 *
 * @return void
 */
function fakeClasses($fName)
{
    // @codingStandardsIgnoreStart
    if($fName != 'cache.php' && ! class_exists('JCache'))
    {
        class JCache{}
    }

    if($fName != 'storage.php' && ! class_exists('JCacheStorage'))
    {
        class JCacheStorage{}
    }
    // @codingStandardsIgnoreEnd
}//function

/**
 * Enter description here ...
 */
class EcrProjectReflection
{
    private $declared_classes = array();

    private $files = array();

    private $basePath = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
    }//function

    /**
     * Reflect a project.
     *
     * @param EcrProjectBase $project The project
     *
     * @return void
     */
    public function reflectProject(EcrProjectBase $project)
    {
        switch($project->type)
        {
            case 'component':
                $comPath = 'components'.DS.$project->comName;
                //                $scopes = array( 'administrator', '');
                //                foreach($scopes as $scope)
                //                {
                $scope = 'administrator';

                $scopePath = JPATH_ROOT;
                $scopePath .=($scope) ? DS.$scope : '';
                $scopePath .= DS.$comPath;

                $this->addReflectionFile('controllers', $scopePath.DS.'controller.php');

                $types = array('controllers', 'models', 'tables');

                foreach($types as $type)
                {
                    if( ! isset($this->files[$type]))
                    {
                        $this->files[$type] = array();
                    }

                    if( ! JFolder::exists($scopePath.DS.$type))
                    {
                        continue;
                    }

                    $files = JFolder::files($scopePath.DS.$type);

                    if($files)
                    {
                        foreach($files as $file)
                        {
                            $this->addReflectionFile($type, $scopePath.DS.$type.DS.$file);
                        }//foreach
                    }
                }//foreach
                if( ! isset($this->files['views']))
                {
                    $this->files['views'] = array();
                }

                if(JFolder::exists($scopePath.DS.'views'))
                {
                    $views = JFolder::folders($scopePath.DS.'views');

                    foreach($views as $view)
                    {
                        $files = JFolder::files($scopePath.DS.'views'.DS.$view);

                        foreach($files as $file)
                        {
                            $this->addReflectionFile('views', $scopePath.DS.'views'.DS.$view.DS.$file, $view);
                        }//foreach
                    }//foreach
                }
                break;
            default:
                echo 'Sorry '.$project->type.' not supported yet..';
                break;
        }//switch
    }//function

    /**
     * Reflect a project with AJAX - aka second try.
     *
     * @param EcrProjectBase $project The project
     *
     * @return void
     */
    public function aj_reflectProject(EcrProjectBase $project)
    {
        switch($project->type)
        {
            case 'component':
                $this->initBattleField();
                $comPath = 'components'.DS.$project->comName;
                //                $scopes = array( 'administrator', '');
                //                foreach($scopes as $scope)
                //                {
                $scope = 'administrator';

                $scopePath = JPATH_ROOT;
                $scopePath .=($scope) ? DS.$scope : '';
                $scopePath .= DS.$comPath;

                $this->basePath = $scopePath;

                //    #            $this->addReflectionFile('controllers', $scopePath.DS.'controller.php');
                $this->aj_addFile('controllers', 'controller.php');

                $types = array('controllers', 'models', 'tables');
                ?>
<script type="text/javascript">
<!--
//-->
</script>
                <?php
                foreach($types as $type)
                {
                    if( ! isset($this->files[$type]))
                    {
                        $this->files[$type] = array();
                    }

                    if( ! JFolder::exists($scopePath.DS.$type))
                    {
                        continue;
                    }

                    $files = JFolder::files($scopePath.DS.$type);

                    if($files)
                    {
                        foreach($files as $file)
                        {
                            // $this->addReflectionFile($type, $scopePath.DS.$type.DS.$file);
                            $this->aj_addFile($type, $type.DS.$file);
                        }//foreach
                    }
                }//foreach
                if( ! isset($this->files['views']))
                {
                    $this->files['views'] = array();
                }

                if(JFolder::exists($scopePath.DS.'views'))
                {
                    $views = JFolder::folders($scopePath.DS.'views');

                    foreach($views as $view)
                    {
                        $files = JFolder::files($scopePath.DS.'views'.DS.$view);

                        foreach($files as $file)
                        {
                            ?>
<script type="text/javascript">
                            name = 'insertDiv_'+divCount;
            html = '<div id="'+name+'"><strong style="color: red;">Loading...</strong>'+divCount+'</div>';
            $('field_<?php echo $type; ?>').innerHTML += html;
            </script>
                            <?php
                        }//foreach
                        foreach($files as $file)
                        {
                            //$this->addReflectionFile('views', $scopePath.DS.'views'.DS.$view.DS.$file, $view);
                            $this->aj_addFile('views', 'views'.DS.$view.DS.$file);
                        }//foreach
                    }//foreach
                }
                break;

default:
    echo 'Sorry '.$project->type.' not supported yet..';
    break;
        }//switch
        ?>
<script type="text/javascript">
//        alert($('field_models').innerHtml);
//        alert($('insertDiv_4').innerHtml);
//        $('insertDiv_2').innerHtml='schnubbiDu';
//        alert($('insertDiv_2').innerHtml);
        </script>

        <?php
    }//function

    /**
     * Add a file.
     *
     * @param string $type File type
     * @param string $fileName File name
     *
     * @return void
     */
    private function aj_addFile($type, $fileName)
    {
        ?>
<script type="text/javascript">

            url = 'index.php?option=com_easycreator&controller=stuffer&task=aj_reflection'
                +'&tmpl=component&el_num='+divCount;

            name = 'insertDiv_'+divCount;
//            html = '<div id="'+name+'"><strong style="color: red;">Loading...</strong>'+divCount+'</div>';
//
//            $('field_<?php echo $type; ?>').innerHTML += html;
            new Request({
                url: url,
                method: 'get',
                update: $(name)
            }).send();


            divCount ++;
        </script>
        <?php
    }//function

    /**
     * Setup.
     *
     * @return void
     */
    private function initBattleField()
    {
        ?>

<script type="text/javascript">
        </script>

<table width="100%" class="adminlist">
    <tr>
        <th>Models</th>
        <th>Tables</th>
    </tr>
    <tr>
        <td>
        <div id="field_models"></div>
        </td>
        <td>
        <div id="field_tables"></div>
        </td>
    </tr>
    <tr>
        <th>Controllers</th>
        <th>Views</th>
    </tr>
    <tr>
        <td>
        <div id="field_controllers"></div>
        </td>
        <td>
        <div id="field_views"></div>
        </td>
    </tr>

</table>
        <?php
    }//function

    /**
     * Print out declared classes for debugging.
     *
     * @return void
     */
    public function printDeclaredClasses()
    {
        echo '<pre>';
        print_r($this->declared_classes);
        echo '<pre>';
    }//function

    /**
     * Add a file for reflection.
     *
     * @param string $type File type
     * @param string $fileName File name
     * @param string $subtype Subtype ..
     *
     * @return boolean
     */
    public function addReflectionFile($type, $fileName, $subtype = '')
    {
        $allClasses = get_declared_classes();

        $rName = JFile::getName($fileName);

        if($this->requireIfExists($fileName))
        {
            $foundClasses = array_diff(get_declared_classes(), $allClasses);

            if( ! count($foundClasses))
            {
                EcrHtml::message(array(jgettext('No classes found'), $fileName), 'error');

                return false;
            }

            $reflection = new JObject;

            foreach($foundClasses as $clas)
            {
                $theClass = new ReflectionClass($clas);
                $reflection->parentName = $theClass->getParentClass()->name;
                $properties = $theClass->getProperties();
                $cMethods = $theClass->getMethods();

                $reflection->methods = array();

                foreach($cMethods as $cMethod)
                {
                    $mPath = $cMethod->getFileName();
                    //#                    echo '<h2>'.$mPath.'</h2>';
                    $pClass = $cMethod->getDeclaringClass();
                    $mPath = $cMethod->getDeclaringClass()->getFileName();

                    if($mPath != $fileName)
                    {
                        continue;
                    }

                    $s = $cMethod->getName();
                    $method = new JObject;
                    $method->name = $cMethod->getName();
                    $method->startLine = $cMethod->getStartLine();
                    $method->endLine = $cMethod->getEndLine();
                    $method->docComment = $cMethod->getDocComment();
                    $method->parameters = array();
                    $method->fileName = $mPath;
                    //                    #$reflection->methods[$s] = new JObject;

                    //                    }
                    //                    if( $s != $displayClassName )
                    //                    {
                    //
                    //                        $indent++;
                    //                        echo '<h1>';
                    //                    #    echo str_repeat("&nbsp;&nbsp;", $indent);
                    //echo ( $displayClassName ) ? '<span style="color: orange">Extends</span>&nbsp;'.$s : $s;
                    //                        echo '</h1>';
                    //                        $displayClassName = $s;
                    //
                    //                    }
                    $paramString = array();
                    $parameters = $cMethod->getParameters();

                    foreach($parameters as $parameter)
                    {
                        $oParameter = new JObject;
                        $oParameter->name = $parameter->getName();
                        $oParameter->isOptional = $parameter->isOptional();
                        $oParameter->isPassedByReference = $parameter->isPassedByReference();
                        $oParameter->isDefaultValueAvailable = $parameter->isDefaultValueAvailable();
                        $method->parameters[] = $oParameter;
                        //$color =($parameter->isOptional() ) ? 'blue' : 'brown';
                        $s = '';
                        $s .= sprintf("%s<strong style='color: brown;'>$%s</strong>",
                        //$parameter->isOptional() ? '<strong style="color: blue;">optional</strong> ' : '',
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
                    }//foreach
                    $paramString = implode(', ', $paramString);
                    //#                    $titel .= '( '.$paramString.' )';
                    switch($type)
                    {
                        case 'controllers':
                            $this->inspectMethod($method, $cMethod);
                            break;

                        case 'models':
                            $this->inspectMethod($method, $cMethod);
                            break;

                        case 'views':
                            $this->inspectMethod($method, $cMethod);
                            break;

                        case 'tables':
                            $this->inspectMethod($method, $cMethod);
                            break;

                        default:
                            echo '<br />No inspection for type '.$type.'<br />';
                            $method->jcommands = array();
                            break;
                    }//switch
                    $reflection->methods[] = $method;
                }//foreach
                if($subtype)
                {
                    $this->files[$type][$subtype][$rName] = $reflection;
                }
                else
                {
                    $this->files[$type][$rName] = $reflection;
                }
            }//foreach
        }
    }//function

    /**
     * Inspect a method.
     *
     * @param object &$method A method object
     * @param object $cMethod The method
     *
     * @return void
     */
    private function inspectMethod(&$method, $cMethod)
    {
        $fileName = $cMethod->getFileName();
        $fileContents = explode("\n", JFile::read($fileName));
        $startLine = $cMethod->getStartLine();
        $endLine = $cMethod->getEndLine();
        $method->jcommands = array();

        //        #        echo '<pre>';
        for($i = $startLine - 1; $i < $endLine; $i++)
        {
            //-- do we have a $this ?
            if( ! strpos($fileContents[$i], '$this') === false)
            {
                //                #    echo $fileContents[$i].NL;
                $commandLine = trim($fileContents[$i]);
                $jCommand = new JObject;

                $jCommand->raw = trim($fileContents[$i]);
                $pattern = '/\$this->(\w+)/';//(\w+)/';
                preg_match($pattern, $commandLine, $matches);//, PREG_OFFSET_CAPTURE, 3);
                //                #                print_r($matches);
                if(isset($matches[1]))
                {
                    $paramString = trim(substr($commandLine
                    , (strpos($commandLine, '$this') + strlen('$this->') + strlen($matches[1]))));

                    if(substr($paramString, strlen($paramString) - 1) == ';')
                    {
                        //-- we have a semicolon at end of line.. proceed
                        $paramString = substr($paramString, 0, strlen($paramString) - 1);

                        if(substr($paramString, 0, 1) == '(')
                        {
                            $paramString = substr($paramString, 1);

                            if(substr($paramString, strlen($paramString) - 1) == ')')
                            {
                                $paramString = substr($paramString, 0, strlen($paramString) - 1);
                            }
                        }

                        //                        #                echo substr($paramString,0,1);
                        //                        #                echo substr($paramString, strlen($paramString)-1);
                    }

                    //                    #                    echo $paramString.NL;
                    $jCommand->name = $matches[1];
                    $jCommand->params = $paramString;
                    $pattern = '/\$this->(\w+)/';//(\w+)/';
                    preg_match($pattern, $fileContents[$i], $matches);//, PREG_OFFSET_CAPTURE, 3);
                    //                    #                print_r($matches);
                    $method->jcommands[] = $jCommand;
                }
            }
        }//for
        //        #        echo '</pre>';
    }//function

    /**
     * Inspect a template file for the use of $this vars.
     *
     * @param string $fileName Template file name
     *
     * @return array
     */
    public static function inspectTemplate($fileName)
    {
        $fileContents = explode("\n", JFile::read($fileName));
        $jCommands = array();

        foreach($fileContents as $line)
        {
            if( ! strpos($line, '$this') === false)
            {
                $jCommand = new JObject;
                //                #    echo $fileContents[$i].NL;
                $commandLine = trim($line);
                $jCommand->raw = $commandLine;
                $pattern = '/\$this->(\w+)/';//(\w+)/';
                preg_match($pattern, $commandLine, $matches);//, PREG_OFFSET_CAPTURE, 3);

                if(isset($matches[1]))
                {
                    $paramString = trim(substr($commandLine
                    , (strpos($commandLine, '$this') + strlen('$this->') + strlen($matches[1]))));
                    //                    if( substr($paramString, strlen($paramString)-1) == ';')
                    //                    {
                    //                        //-- we have a semicolon at end of line.. proceed
                    //                        $paramString = substr($paramString, 0,strlen($paramString)-1);
                    //                        if( substr($paramString,0,1) == '(')
                    //                        {
                    //                            $paramString = substr($paramString,1);
                    //                            if( substr($paramString, strlen($paramString)-1) == ')')
                    //                            {
                    //                                $paramString = substr($paramString, 0, strlen($paramString)-1);
                    //                            }
                    //                        }
                    //        #                echo substr($paramString,0,1);
                    //        #                echo substr($paramString, strlen($paramString)-1);
                    //                    }

                    //                    #                    echo $paramString.NL;
                    $jCommand->name = $matches[1];
                    $jCommand->params = $paramString;
                    $jCommands[] = $jCommand;
                }
            }
        }//foreach

        return $jCommands;
    }//function

    /**
     * Get the reflections.
     *
     * @return array
     */
    public function getReflections()
    {
        return $this->files;
    }//function

    /**
     * Test if a file exists and then inlude it.
     *
     * @param string $fileName The file name
     *
     * @return boolean
     */
    private function requireIfExists($fileName)
    {
        if(file_exists($fileName))
        {
            include_once $fileName;

            return true;
        }

        if(ECR_DEBUG)
        {
            EcrHtml::message(sprintf(jgettext('File %s not found'), $fileName), 'error');
        }

        return false;
    }//function
}//class
