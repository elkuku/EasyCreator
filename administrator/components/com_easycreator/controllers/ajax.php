<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 20-Apr-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator Controller.
 *
 * @todo in which type of controller will i place the ajax methods ??
 *
 * @package EasyCreator
 */
class EasyCreatorControllerAjax extends JControllerLegacy
{
    /**
     * Response array for json encoded output
     * @var array
     */
    private $response = array('status' => 0, 'text' => '', 'debug' => '');

    /**
     * @var EcrResponseJson
     */
    private $responseJson;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->responseJson = new EcrResponseJson;

        parent::__construct($config);
    }

    /**
     * Shows a part from templates/parts folder. Calls the 'getOptions' function in part class.
     *
     * @return void
     */
    public function show_part()
    {
        $input = JFactory::getApplication()->input;

        $group = $input->get('group');
        $part = $input->get('part');
        $element = $input->get('element');
        $scope = $input->get('scope');

        $ecrPart = EcrProjectHelper::getPart($group, $part, $element, $scope);

        if( ! $ecrPart)
        {
            return;
        }

        if(method_exists($ecrPart, 'info'))
        {
            $info = $ecrPart->info();

            if( ! get_class($info) == 'EcrProjectPart')
            {
                echo 'Part info must be a EcrProjectPart class.. not : ';
                echo get_class($info);

                return;
            }

            echo $info->format('erm', 'add');
        }
        else
        {
            echo '<div style="color: blue; font-weight: bold; text-align:center;">'
            .ucfirst($group).' - '.ucfirst($part)
            .'</div>';
        }

        //-- Additional request vars
        echo '<input type="hidden" name="group" value="'.$group.'" />';
        echo '<input type="hidden" name="part" value="'.$part.'" />';

        //-- Additional options from part file
        echo $ecrPart->getOptions();
    }

    /**
     * Calls the 'edit' function in part class.
     *
     * @return void
     */
    public function edit_part()
    {
        $input = JFactory::getApplication()->input;

        try
        {
            //-- Get the project
            $project = EcrProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            parent::display();

            return;
        }//try

        $group = $input->get('group');
        $part = $input->get('part');
        $element = $input->get('element');
        $scope = $input->get('scope');

        if( ! $ecrPart = EcrProjectHelper::getPart($group, $part, $element, $scope, true))
        {
            echo '<h4 style="color: red;">PART not found</h4>';

            return;
        }

        if( ! method_exists($ecrPart, 'edit'))
        {
            echo '<h4 style="color: red;">EDIT function not found</h4>';

            return;
        }

        if( ! isset($ecrPart->key)
        || ! isset($project->autoCodes[$ecrPart->key]))
        {
            echo '<h4 style="color: red;">No AutoCode found</h4>';

            return;
        }

        if(method_exists($ecrPart, 'info'))
        {
            $info = $ecrPart->info();

            if( ! get_class($info) == 'EcrProjectPart')
            {
                echo 'Part info must be a EcrProjectPart class.. not : ';
                echo get_class($info);

                return;
            }

            echo $info->format('erm', 'edit');
        }
        else
        {
            echo '<div style="color: blue; font-weight: bold; text-align:center;">'
            .ucfirst($group).' - '.ucfirst($part)
            .'</div>';
        }

        //-- Additional request vars
        echo '<input type="hidden" name="group" value="'.$group.'" />';
        echo '<input type="hidden" name="part" value="'.$part.'" />';

        echo $ecrPart->edit($project->autoCodes[$ecrPart->key]);
    }

    /**
     * For JHelp.
     *
     * @return void
     */
    public function show_source()
    {
        $input = JFactory::getApplication()->input;

        $rClass = $input->get('class');
        $rMethod = $input->get('method');

        $classlistPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'jclasslists';
        $fName = 'jclasslist_'.str_replace('.', '_', JVERSION);

        if( ! JFile::exists($classlistPath.DS.$fName.'.php'))
        {
            echo sprintf(jgettext('The class file for your Joomla version %s has not been build yet.'), JVERSION);
            echo BR.$classlistPath.DS.$fName.'.php';

            return;
        }

        JLoader::import($fName, $classlistPath);

        $cList = getJoomlaClasses();

        if( ! class_exists($rClass))
        {
            if(array_key_exists($rClass, $cList))
            {
                if(defined('JPATH_PLATFORM'))
                {
                    $path = JPATH_PLATFORM.DS.'joomla'.DS.$cList[$rClass][1];
                }
                else
                {
                    $path = JPATH_LIBRARIES.DS.'joomla'.DS.$cList[$rClass][1];
                }

                if( ! JFile::exists($path))
                {
                    echo '<strong style="color: red">'.jgettext('File not found').'</strong>'.BR;

                    return;
                }

                fakeClasses(JFile::getName($cList[$rClass][1]));

                require_once $path;
            }

            if( ! class_exists($rClass))
            {
                echo '<strong style="color: red">'.sprintf(jgettext('Class %s not found'), $rClass).'</strong>'.BR;

                var_dump($cList);

                return;
            }
        }

        $class = new ReflectionClass($rClass);
        $methods = $class->getMethods();

        if($rMethod != 'NULL')
        {
            foreach($methods as $method)
            {
                if($method->name != $rMethod)
                {
                    continue;
                }

                $fileContents = file($method->getFileName());
                $code = '';

                for($i = $method->getStartLine() - 1; $i < $method->getEndline(); $i++)
                {
                    $l = rtrim($fileContents[$i]).NL;

                    //-- Strip leading tabs
                    if(substr($l, 0, 1) == "\t")
                    {
                        $l = substr($l, 1);
                    }

                    //-- Convert tabs to three spaces
                    $l = str_replace("\t", '   ', $l);
                    $code .= $l;
                }//for

                $docComment = nl2br(htmlentities($method->getDocComment()));
                $pattern = '#(@[a-z]+)#';
                $docComment = preg_replace($pattern, '<strong>$1</strong>', $docComment);
                echo "<h1>$rClass - $rMethod</h1>";
                echo '<div class="path">';
                echo '<span class="img icon16-directory" style="font-size: 1.3em; font-weight: bold;">'
                .str_replace(JPATH_ROOT, '', $method->getFileName())
                .'</span> - # '.$method->getStartLine().' - '.$method->getEndline();
                echo '</div>';
                echo '<div style="font-size: 1.2em;">'.$docComment.'</div>';
                $hlCode = highlight_string('<?php'.NL.$code, true);
                $hlCode = str_replace('&lt;?php', '', $hlCode);
                echo '<div style="border: 1px dashed gray; background-color: #eee; padding: 0.5em;">'.$hlCode.'</div>';
            }//foreach
        }
        else
        {
            //-- No method given - output the whole class
            echo "<h1>$rClass</h1>";

            /* @var ReflectionMethod $method */
            foreach($methods as $method)
            {
                if($method->getDeclaringClass()->name != $rClass)
                {
                    continue;
                }

                $title = sprintf(
                "%s%s%s%s%s%s%s function <span style='color: blue;'>%s</span> %s",
                $method->isInternal() ? 'internal' : '',
                $method->isAbstract() ? ' abstract' : '',
                $method->isFinal() ? ' final' : '',
                $method->isPublic() ? ' <span style="color: green;">public</span>' : '',
                $method->isPrivate() ? ' <strong style="color: orange">private</strong>' : '',
                $method->isProtected() ? ' <strong style="color: red">protected</strong>' : '',
                $method->isStatic() ? ' <strong style="color: black">static</strong>' : '',
                $method->getName(),
                $method->isConstructor() ? '<span style="color: green;">Konstruktor</span>' : ''
                );
                $parameters = $method->getParameters();
                $paramString = '';

                foreach($parameters as $parameter)
                {
                    $s = '';
                    $s .= sprintf("%s<strong style='color: brown;'>$%s</strong>",
                    //-- C$parameter->isOptional() ? '<strong style="color: blue;">optional</strong> ' : '',
                    $parameter->isPassedByReference() ? '<strong style="color: blue;"> & </strong>' : '',
                    $parameter->getName()
                    );

                    if($parameter->isDefaultValueAvailable())
                    {
                        $def = $parameter->getDefaultValue();

                        if($def === null)
                        {
                            $s .= '=null';
                        }
                        else if($def === false)
                        {
                            $s .= '=false';
                        }
                        else if($def === true)
                        {
                            $s .= '=true';
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

                if($paramString)
                {
                    $paramString = implode(', ', $paramString);
                }

                echo JHTML::tooltip(nl2br(htmlentities($method->getDocComment())), $method->getName());
                echo $title.'('.$paramString.')';
                echo BR;
            }//foreach
        }
    }//function

    /**
     * Executes a function inside a 'part' from templates/parts folder.
     *
     * @return void
     */
    public function part_task()
    {
        $input = JFactory::getApplication()->input;

        $group = $input->get('group');
        $part = $input->get('part');
        $element = $input->get('element');
        $scope = $input->get('scope');

        $partTask = $input->get('part_task');

        if( ! $ecrPart = EcrProjectHelper::getPart($group, $part, $element, $scope))
        {
            EcrHtml::message(array(jgettext('Unable to load part').' [group, part]', $group, $part), 'error');

            return;
        }

        if( ! method_exists($ecrPart, $partTask))
        {
            EcrHtml::message(array(jgettext('Function not found'), $partTask), 'error');

            return;
        }

        //-- Execute the task
        return $ecrPart->$partTask($element);
    }//function

    /**
     * Saves a translation.
     *
     * @throws Exception
     * @return void
     */
    public function translate()
    {
        $input = JFactory::getApplication()->input;

        ob_start();

        try
        {
            if( ! $scope = $input->get('scope'))
            throw new Exception(jgettext('No scope given'));

            if( ! $translation = $input->getHtml('translation'))
                throw new Exception(jgettext('Empty translation'));

            //-- Strip line breaks
            $translation = str_replace("\n", '<br />', $translation);

            $project = EcrProjectHelper::getProject();

            if( ! count($project->langs))
            throw new Exception(jgettext('No languages found'));

            $easyLanguage = new EcrLanguage($project, $scope, array());

            $easyLanguage->saveTranslation(
                $input->get('trans_lang'), $input->getString('trans_key')
                , $translation);
        }
        catch(Exception $e)
        {
            $this->response['status'] = 1;
            $this->response['text'] .= $e->getMessage();
        }//try

        $buffer = ob_get_clean();

        $this->response['debug'] = htmlspecialchars($buffer);

        if($buffer)
        {
            //-- C            echo $buffer;
            //-- C            jexit();
            $this->response['status'] = 1;
        }

        echo json_encode($this->response);

        jexit();
    }//function

    /**
     * Deletes a translation.
     *
     * @throws Exception
     * @return void
     */
    public function delete_translation()
    {
        $input = JFactory::getApplication()->input;

        ob_start();

        try
        {
            if( ! $scope = $input->get('scope'))
            throw new Exception(jgettext('No scope given'));

            $project = EcrProjectHelper::getProject();

            if( ! count($project->langs))
            throw new Exception(jgettext('No languages found'));

            $easyLanguage = new EcrLanguage($project, $scope, array());

            $trans_lang = $input->get('trans_lang');
            $trans_key = $input->getString('trans_key');

            $easyLanguage->deleteTranslation($trans_lang, $trans_key);
        }
        catch(Exception $e)
        {
            $this->response['status'] = 1;
            $this->response['text'] .= $e->getMessage();
        }//try

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response['status'] = 1;
            $this->response['text'] .= $buffer;
        }

        echo json_encode($this->response);

        jexit();
    }//function

    /**
     * Load a file.
     *
     * @return void
     */
    public function loadFile()
    {
        $input = JFactory::getApplication()->input;

        $response = array();
        $response['status'] = 0;
        $response['text'] = '';

        $filePath = $input->getPath('file_path');
        $fileName = $input->getPath('file_name');

        $path = JPath::clean(JPATH_ROOT.DS.$filePath.DS.$fileName);

        if( ! JFile::exists($path))
        {
            $response['text'] = '<b style="color: red;">'.jgettext('File not found').'</b> - '.$path;
            echo json_encode($response);

            return;
        }

        if( ! $fileContents = JFile::read($path))
        {
            $response['text'] = sprintf(jgettext('The file %s is empty'), str_replace(JPATH_ROOT, 'JPATH_ROOT', $path));
            echo json_encode($response);

            return;
        }

        $response['status'] = 1;
        $response['text'] = $fileContents;

        echo json_encode($response);

        jexit();
    }//function

    /**
     * Load a picture.
     *
     * @return void
     */
    public function loadPic()
    {
        $input = JFactory::getApplication()->input;

        $response = array();
        $response['status'] = 0;
        $response['text'] = '';

        $filePath = $input->getPath('file_path');
        $fileName = $input->getPath('file_name');

        $test = JPath::clean(JPATH_ROOT.DS.$filePath.DS.$fileName);

        if(JFile::exists($test))
        {
            $fileName = JPath::clean($filePath.DS.$fileName);
            $file = JURI::root().'/'.$fileName;

            $response['status'] = 1;
            $response['text'] = JHTML::_('image', $file, 'Image');
        }
        else
        {
            $response['text'] = '<b style="color: red;">'.jgettext('Invalid file').'</b> - '.$test;
        }

        echo json_encode($response);

        jexit();
    }//function

    /**
     * Save a file.
     *
     * @return void
     */
    public function save()
    {
        $input = JFactory::getApplication()->input;

        $old_task = $input->get('old_task', null);
        $task =($old_task) ? $old_task : 'stuffer';

        $input->set('task', $task);
        $input->set('view', 'stuffer');

        ob_start();

        try
        {
            EcrFile::saveFile();

            $this->response['text'] = jgettext('The file has been saved');
        }
        catch(Exception $e)
        {
            $this->response['status'] = 1;
            $this->response['text'] = $e->getMessage();
        }//try

        $buffer = ob_get_clean();

        $this->response['debug'] = $buffer;

        echo json_encode($this->response);

        jexit();
    }//function

    /**
     * Create a new folder.
     *
     * @return void
     */
    public function new_folder()
    {
        $input = JFactory::getApplication()->input;

        $ecr_project = $input->get('ecr_project');

        if($input->get('do_action') == 'new_folder')
        {
            $act_path = $input->getPath('act_path');
            $act_name = $input->getPath('act_name');
            $act_path = str_replace('/', DS, $act_path);
            $path = JPATH_ROOT.DS.$act_path;

            if( ! JFolder::exists($path))
            {
                EcrHtml::message(array(jgettext('Wrong base folder'), $path), 'error');

                return;
            }

            $path .= DS.$act_name;

            if(JFolder::exists($path))
            {
                EcrHtml::message(array(jgettext('The folder already exists'), $path), 'error');

                return;
            }

            if( ! JFolder::create($path))
            {
                EcrHtml::message(array(jgettext('Unable to create folder'), $path), 'error');

                return;
            }

            //-- Clean the cache
            JFactory::getCache('EasyCreator_'.$ecr_project)->clean();

            echo '*OK*';

            return;
        }

        $this->actForm(jgettext('New folder'), 'add', jgettext('Create'));
        $this->processForm('new_folder', $ecr_project, 'folder', 'new', true, true);
    }//function

    /**
     * Create a new file.
     *
     * Path and filename come from $_REQUEST
     *
     * @return void
     */
    public function new_file()
    {
        $input = JFactory::getApplication()->input;

        $ecr_project = $input->get('ecr_project');

        if($input->get('do_action') == 'new_file')
        {
            $reqPath = $input->getPath('act_path');
            $reqName = $input->getPath('act_name');

            $path = JPath::clean(JPATH_ROOT.DS.$reqPath.DS.$reqName);

            if(is_dir($path))
            {
                EcrHtml::message(array(jgettext('This is a folder'), $path), 'error');

                return;
            }

            if(is_file($path))
            {
                EcrHtml::message(array(jgettext('The file already exists'), $path), 'error');

                return;
            }

            //-- @todo file from template
            $template = 'new file';

            if( ! is_int(file_put_contents($path, $template)))
            {
                EcrHtml::message(array(jgettext('Unable to create file'), $path), 'error');

                return;
            }

            //-- Clean the cache
            JFactory::getCache('EasyCreator_'.$ecr_project)->clean();

            echo '*OK*';

            return;
        }

        $this->actForm(jgettext('New file'), 'add', jgettext('Create'));
        $this->processForm('new_file', $ecr_project, 'file', 'new', true, true);
    }//function

    /**
     * Delete a folder.
     *
     * @return void
     */
    public function delete_folder()
    {
        $input = JFactory::getApplication()->input;

        $ecr_project = $input->get('ecr_project');

        if($input->get('do_action') == 'delete_folder')
        {
            if( ! $act_path = $input->getPath('act_path'))
            {
                EcrHtml::message(jgettext('Empty'), 'error');

                return;
            }

            $path = JPATH_ROOT.DS.$act_path;

            if( ! JFolder::exists($path))
            {
                EcrHtml::message(array(jgettext('Folder does not exist'), $path), 'error');

                return;
            }

            if( ! JFolder::delete($path))
            {
                EcrHtml::message(array(jgettext('Unable to delete folder'), $path), 'error');

                return;
            }

            //-- Clean the cache
            JFactory::getCache('EasyCreator_'.$ecr_project)->clean();

            echo '*OK*';

            return;
        }

        $this->actForm(jgettext('Delete folder'), 'delete', jgettext('Delete'), false);
        $this->processForm('delete_folder', $ecr_project, 'folder', 'delete', true);
    }//function

    /**
     * Delete a file.
     *
     * @return void
     */
    public function delete_file()
    {
        $input = JFactory::getApplication()->input;

        $ecr_project = $input->get('ecr_project', NULL);

        if($input->get('do_action') == 'delete_file')
        {
            $act_path = $input->getPath('act_path');
            $act_name = $input->getPath('act_name');

            if( ! $act_path
            || ! $act_name)
            {
                EcrHtml::message(jgettext('Empty'), 'error');

                return;
            }

            $path = JPATH_ROOT.DS.$act_path.DS.$act_name;

            if( ! JFile::exists($path))
            {
                EcrHtml::message(array(jgettext('File does not exist'), $path), 'error');

                return;
            }

            if( ! JFile::delete($path))
            {
                EcrHtml::message(array(jgettext('Unable to delete file'), $path), 'error');

                return;
            }

            //-- Clean the cache
            JFactory::getCache('EasyCreator_'.$ecr_project)->clean();

            echo '*OK*';

            return;
        }

        $this->actForm(jgettext('Delete file'), 'delete', jgettext('Delete'), false);
        $this->processForm('delete_file', $ecr_project, 'file', 'delete', true);
    }//function

    /**
     * Rename a folder.
     *
     * @return void
     */
    public function rename_folder()
    {
        $input = JFactory::getApplication()->input;

        $ecr_project = $input->get('ecr_project');

        if($input->get('do_action') == 'rename_folder')
        {
            $act_path = $input->getPath('act_path');
            $old_name = $input->getPath('old_name');
            $act_name = $input->getPath('act_name');
            $path = JPATH_ROOT.DS.$act_path;

            if( ! JFolder::exists($path))
            {
                EcrHtml::message(jgettext('Wrong base folder'), 'error');

                return;
            }

            $ret = JFolder::move($old_name, $act_name, $path);

            if($ret !== true)
            {
                $ret .= BR.$act_path.BR.$old_name.BR.$act_name;
                EcrHtml::message($ret, 'error');

                return;
            }

            //-- Clean the cache
            JFactory::getCache('EasyCreator_'.$ecr_project)->clean();

            echo '*OK*';

            return;
        }

        $this->actForm(jgettext('Rename folder'), 'rename', jgettext('Rename'), true);
        $this->processForm('rename_folder', $ecr_project, 'folder', 'rename', true);
    }//function

    /**
     * Rename a file.
     *
     * @return void
     */
    public function rename_file()
    {
        $input = JFactory::getApplication()->input;

        $ecr_project = $input->get('ecr_project');

        if($input->get('do_action') == 'rename_file')
        {
            $act_path = $input->getPath('act_path');
            $old_name = $input->getPath('old_name');
            $act_name = $input->getPath('act_name');
            $path = JPATH_ROOT.DS.$act_path;

            if( ! JFile::exists($path.DS.$old_name))
            {
                EcrHtml::message(jgettext('File not found'), 'error');

                return;
            }

            $ret = JFile::move($old_name, $act_name, $path);

            if($ret !== true)
            {
                EcrHtml::message($ret, 'error');

                return;
            }

            //-- Clean the cache
            JFactory::getCache('EasyCreator_'.$ecr_project)->clean();

            echo '*OK*';

            return;
        }

        $this->actForm(jgettext('Rename file'), 'rename', jgettext('Rename'), true);
        $this->processForm('rename_file', $ecr_project, 'file', 'rename', true);
    }//function

    /**
     * Displays a form for right click menu actions (add/edit/delete..).
     *
     * @param string   $title    The title
     * @param string   $icon     The icon
     * @param string   $text     The text
     * @param bool|int $hasInput If it has an input field
     *
     * @return void
     */
    public function actForm($title, $icon, $text, $hasInput = true)
    {
        $inputType =($hasInput) ? 'text' : 'hidden';
        ?>
<style type="text/css">
body {
    background-color: #eee;
}
</style>
<h3 class="img icon16-<?php echo $icon; ?>">
    <?php echo $title ?>
</h3>
<div
    style="background-color: #ffff99; border: 1px solid gray; padding: 0.5em;">
    <div id="displ_folder" style="display: inline;"></div>
    <input type="<?php echo $inputType; ?>" id="act_name" />
</div>
<br />
<div style="text-align: center;">
    <span class="btn" onclick="processForm();">
        <i class="img icon16-<?php echo $icon; ?>"></i>
        <?php echo $text ?>
    </span>
</div>
<div id="log"></div>
<input type="hidden" id="act_folder" />

<?php
    }//function

    /**
     * Handel right click actions.
     *
     * @param string $task The task
     * @param string $ecr_project The project name
     * @param string $type The tyspe
     * @param string $action The action
     * @param boolean $hasName If it has a name
     * @param boolean $isNew If it is new
     *
     * @return void
     */
    public function processForm($task, $ecr_project, $type, $action, $hasName = false, $isNew = false)
    {
        $input = JFactory::getApplication()->input;

        $baseLink = 'index.php?option=com_easycreator';

        $hrefLink = $baseLink;
        $hrefLink .= '&ecr_project='.$input->get('ecr_project');
        $hrefLink .= '&task='.$input->get('old_task', 'stuffer');
        $hrefLink .= '&controller='.$input->get('old_controller', 'stuffer');

        $ajaxLink = $baseLink.'&tmpl=component&controller=ajax&format=raw';
        $ajaxLink .= '&task='.$task.'&do_action='.$task;
        ?>

<script type="text/javascript">
            var FBPresent = true;
            if(window.console == undefined)
            {
                if(window.parent.console != undefined)
                {
                    console = window.parent.console;
                }
                else
                {
                    FBPresent = false;
                }
            }
            frm = window.parent.document.adminForm;
            path = frm.act_folder.value;
            act_file = frm.act_file.value;

            if(FBPresent) console.log(path);
            if(FBPresent)console.log('act_file '+act_file);

            //-- No dot found in filename - must be a folder @todo
            if( act_file.indexOf('.') === -1 )
            {
                path += '/' + act_file;

                if(FBPresent) console.log('no dot found - append filename to path - '.path);
            }

            subPath = path.split('/');
            folderName = subPath[subPath.length-1];

            display = path;
<?php
            switch($type)
            {
                case 'folder':
                    switch($action)
                    {
                        case 'delete':
                            echo "display = path.replace(folderName, "
                            ."'<strong style=\"color: red;\">'+folderName+'</strong>');".NL;
                            echo "$('act_name').value = act_file".NL;
                            break;

                        case 'rename':
                            echo "path = path.replace(folderName, '');".NL;
                            echo "display = path;".NL;
                            echo "$('act_name').value = act_file".NL;
                            break;

                        default:
                        break;
                    }//switch
                break;

                case 'file':
                    switch($action)
                    {
                        case 'delete':
                            echo "display = path+'".DS."<strong style=\"color: red;\">'+act_file+'</strong>';".NL;
                            echo "$('act_name').value = act_file;".NL;
                        break;

                        case 'rename':
                            echo "$('act_name').value = act_file;".NL;
                        break;

                        default:
                        break;
                    }//switch
                break;

                default:
                break;
            }//switch
            ?>
            /*
            * Javascript again..
            */

            document.id('displ_folder').innerHTML = display;
            document.id('act_folder').value = path;

            function processForm()
            {
                post = '';
                post += '&act_path=' + document.id('act_folder').value;
                post += '&ecr_project=<?php echo $ecr_project; ?>';

                act_name = document.id('act_name').value;
                post += '&act_name='+act_name;

                <?php
                if($action == 'rename')
                {
                    echo "post += '&old_name='+window.parent.document.adminForm.act_file.value;";
                }

                if($hasName)
                {
                    ?>
                    uri = '<?php echo $ajaxLink; ?>'+post;

                    if( ! act_name)
                    {
                        document.id('act_name').setStyle('background-color', 'red');

                        return false;
                    }
                    <?php
                }
                ?>

                new Request({
                    url: uri,
                    'postBody': post,
                    'onComplete': function(result)
                    {
                        if(result == '*OK*')
                        {
                            window.parent.location = '<?php echo $hrefLink; ?>';
                        }
                        else
                        {
                            document.id('log').innerHTML = result;
                        }
                    }
                }).send();
            }//function
        </script>

        <?php
    }//function

    /**
     * Dislays the fields of a given table in a <select> box.
     *
     * @return void
     */
    public function get_table_field_selector()
    {
        $input = JFactory::getApplication()->input;

        $response = array();
        $table = $input->get('table');
        $fieldName = $input->get('field_name');

        if( ! $table)
        {
            $response['text'] = '';
        }
        else
        {
            $db = JFactory::getDBO();
            $dbTables = $db->getTableList();
            $dbPrefix = $db->getPrefix();

            if( ! in_array($dbPrefix.$table, $dbTables))
            {
                $response['text'] = 'Invalid table';
            }
            else
            {
                $fields = $db->getTableFields($dbPrefix.$table);

                $fields = $fields[$dbPrefix.$table];

                $out = '';
                $out .= '<select name="'.$fieldName.'">';
                $out .= '<option value="">'.jgettext('Select...').'</option>';

                foreach(array_keys($fields) as $fieldName)
                {
                    $out .= '<option>'.$fieldName.'</option>';
                }//foreach

                $out .= '</select>';
                $response['text'] = $out;
                $response['status'] = 1;
            }
        }

        echo json_encode($response);

        jexit();
    }
}
