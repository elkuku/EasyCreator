<?php
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 23-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.controller');

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerStuffer extends JController
{
    /**
     * @var EasyLogger
     */
    private $logger;

    /**
     * Standard display method.
     *
     * @param boolean    $cachable  If true, the view output will be cached
     * @param array|bool $urlparams An array of safe url parameters and their variable types,
     *                              for valid values see {
     *
     * @link JFilterInput::clean()}.
     *
     * @return void
     * @see  JController::display()
     */
    public function display($cachable = false, $urlparams = false)
    {
        if(JRequest::getVar('tmpl') != 'component')
        {
            $ecr_project = JRequest::getCmd('ecr_project');

            if( ! $ecr_project)
            {
                //---NO PROJECT SELECTED - abort to mainscreen
                JRequest::setVar('view', 'easycreator');
                parent::display($cachable, $urlparams);

                return;
            }
        }

        JRequest::setVar('view', 'stuffer');

        parent::display($cachable, $urlparams);
    }//function

    /**
     * Insert a new part from templates/parts folder.
     *
     * @return void
     */
    public function new_element()
    {
        $ecr_project = JRequest::getCmd('ecr_project');
        $group = JRequest::getCmd('group');
        $part = JRequest::getCmd('part');

        $element = JRequest::getCmd('element');
        $scope = JRequest::getCmd('element_scope');

        $old_task = JRequest::getCmd('old_task', 'stuffer');

        //--Get the project
        try
        {
            $project = EasyProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::displayMessage($e);

            parent::display();

            return;
        }//try

        //        JRequest::setVar('task', $old_task);
        JRequest::setVar('view', 'stuffer');
        JRequest::setVar('file', '');

        if( ! $ePart = EasyProjectHelper::getPart($group, $part, $element, $scope))
        {
            EcrHtml::displayMessage(array(jgettext('Unable to load part').' [group, part]', $group, $part), 'error');
            parent::display();

            return;
        }

        if( ! $project->prepareAddPart($ecr_project))
        {
            EcrHtml::displayMessage(array(jgettext('Unable to prepare part').' [group, part]', $group, $part), 'error');
            parent::display();

            return;
        }

        //--Setup logging
        ecrLoadHelper('logger');

        $buildOpts = JRequest::getVar('buildopts', array());
        $buildOpts['fileName'] = date('ymd_Hi').'_add_part.log';

        $logger = easyLogger::getInstance('ecr', $buildOpts);

        $options = new stdClass;
        $options->ecr_project = $ecr_project;
        $options->group = $group;
        $options->part = $part;

        //JPath::clean @since J 1.7
        $options->pathSource = JPath::clean(ECRPATH_PARTS.DS.$group.DS.$part.DS.'tmpl');

        $string = '';
        $string .= '<h2>Add Element</h2>';
        $string .= 'Project: '.$ecr_project.BR;
        $string .= 'Group: '.$group.BR;
        $string .= 'Part:  '.$part.BR;
        $string .= 'Source:'.BR.$options->pathSource;
        $string .= '<hr />';

        $logger->log($string);

        if( ! $ePart->insert($project, $options, $logger))
        {
            EcrHtml::displayMessage(array(jgettext('Unable to insert part').' [group, part]', $group, $part), 'error');
            $logger->writeLog();
        }
        else
        {
            EcrHtml::displayMessage(array(jgettext('Part added').' [group, part]', $group, $part));
            $logger->writeLog();

            $cache = JFactory::getCache();
            $cache->clean('EasyCreator_'.$ecr_project);
        }

        parent::display();
    }//function

    /**
     * Create a new relation for tables.
     *
     * @return void
     */
    public function new_relation()
    {
        //--Get the project
        try
        {
            if( ! $tableName = JRequest::getCmd('table_name'))
            throw new Exception(jgettext('No table given'));

            $project = EasyProjectHelper::getProject();

            if( ! array_key_exists($tableName, $project->tables))
            throw new Exception(jgettext('Invalid Table'));

            $relations = JRequest::getVar('relations');

            if( ! isset($relations[$tableName]['foreign_table_field']))
            throw new Exception(jgettext('Invalid options'));

            $relation = new EasyTableRelation;

            $relation->type = $relations[$tableName]['join_type'];
            $relation->field = $relations[$tableName]['own_field'];
            $relation->onTable = $relations[$tableName]['foreign_table'];
            $relation->onField = $relations[$tableName]['foreign_table_field'];

            $alias = new EasyTableRelationAlias;

            $alias->alias = $relations[$tableName]['alias'];
            $alias->aliasField = $relations[$tableName]['alias_field'];

            $relation->addAlias($alias);

            $project->tables[$tableName]->addRelation($relation);

            $project->writeProjectXml();
        }
        catch(Exception $e)
        {
            EcrHtml::displayMessage($e);
        }//try

        JRequest::setVar('view', 'stuffer');
        JRequest::setVar('task', 'tables');

        parent::display();
    }//function

    /**
     * Updates AutoCode.
     *
     * @return void
     */
    public function autocode_update()
    {
        $ecr_project = JRequest::getCmd('ecr_project');
        $group = JRequest::getCmd('group');
        $part = JRequest::getCmd('part');

        $element = JRequest::getCmd('element');
        $scope = JRequest::getCmd('element_scope');

        $old_task = JRequest::getVar('old_task', null);
        $task =($old_task) ? $old_task : 'stuffer';

        JRequest::setVar('task', $task);
        JRequest::setVar('view', 'stuffer');

        $key = "$scope.$group.$part.$element";

        try
        {
            if( ! $AutoCode = EasyProjectHelper::getAutoCode($key))
            throw new Exception(jgettext('Unable to load Autocode').sprintf(' [group, part] [%s, %s]', $group, $part));

            if( ! method_exists($AutoCode, 'insert'))
            throw new Exception(sprintf(jgettext('Insert method not found in Autocode %s'), $key));

            $project = EasyProjectHelper::getProject();

            if( ! $project->prepareAddPart($ecr_project))
            throw new Exception(sprintf(jgettext('Unable to prepare project [project, group, part] [%s, %s, %s]')
            , $ecr_project, $group, $part));

            //--Setup logging
            ecrLoadHelper('logger');

            $buildOpts = JRequest::getVar('buildopts', array());
            $buildOpts['fileName'] = date('ymd_Hi').'_add_part.log';

            $logger = easyLogger::getInstance('ecr', $buildOpts);

            $options = new stdClass;
            $options->ecr_project = $ecr_project;
            $options->group = $group;
            $options->part = $part;
            $options->pathSource = ECRPATH_AUTOCODES.DS.$scope.DS.$group.DS.$part.DS.'tmpl';

            $string = '';
            $string .= '<h2>Add Element</h2>';
            $string .= 'Project: '.$ecr_project.BR;
            $string .= 'Group: '.$group.BR;
            $string .= 'Part:  '.$part.BR;
            $string .= 'Source:'.BR.$options->pathSource;
            $string .= '<hr />';

            $logger->log($string);

            if( ! $AutoCode->insert($project, $options, $logger))
            throw new Exception(jgettext('Unable to update AutoCode').' [group, part]', $group, $part);

            EcrHtml::displayMessage(array(jgettext('AutoCode updated').' [group, part]', $group, $part));
        }
        catch(Exception $e)
        {
            EcrHtml::displayMessage($e);

            $logger->log($e->getMessage());
        }//try

        $logger->writeLog();

        parent::display();
    }//function

    /**
     * Delete a file.
     *
     * @return void
     */
    public function delete_file()
    {
        $file_path = JRequest::getVar('file_path', NULL);
        $file_name = JRequest::getVar('file_name', NULL);

        $path = JPATH_ROOT.DS.$file_path.$file_name;

        if( ! JFile::exists($path))
        {
            EcrHtml::displayMessage(jgettext('invalid file'), 'error');
            echo $path;
        }
        else
        {
            if(JFile::delete($path))
            {
                EcrHtml::displayMessage(jgettext('File has been deleted'));

                //--Clean the cache
                $ecr_project = JRequest::getVar('ecr_project', NULL);

                JFactory::getCache('EasyCreator_'.$ecr_project)->clean();
            }
            else
            {
                EcrHtml::displayMessage(jgettext('Unable to deleted the file'), 'error');
            }
        }

        $old_task = JRequest::getVar('old_task', NULL);
        $task =($old_task) ? $old_task : 'stuffer';
        JRequest::setVar('task', $task);
        JRequest::setVar('view', 'stuffer');

        parent::display();
    }//function

    /**
     * Save the parameters from request.
     *
     * @todo convert JSimpleXML to SimpleXML
     *
     * @return void
     */
    public function save_params()
    {
        JRequest::setVar('view', 'stuffer');
        JRequest::setVar('task', 'projectparams');

        /*
         * Parameter definition
         * Object[name] = array(extra fields)
         * TODO define elsewhere for php and js
         */
        $paramTypes = array(
        'calendar'     => array('format')
        , 'category'   => array('class', 'section', 'scope')
        , 'editors'    => array()
        , 'filelist'   => array('directory', 'filter', 'exclude', 'hide_none'
        , 'hide_default', 'stripext')
        , 'folderlist' => array('directory', 'filter', 'exclude', 'hide_none', 'hide_default')
        , 'helpsites'  => array()
        , 'hidden'     => array('class')
        , 'imagelist'  => array('directory', 'filter', 'exclude', 'hide_none'
        , 'hide_default', 'stripext')
        , 'languages'  => array('client')
        , 'list'       => array()
        , 'menu'       => array()
        , 'menuitem'   => array('state')
        , 'password'   => array('class', 'size')
        , 'radio'      => array()
        , 'section'    => array()
        , 'spacer'     => array()
        , 'sql'        => array('query', 'key_field', 'value_field')
        , 'text'       => array('class', 'size')
        , 'textarea'   => array('rows', 'cols', 'class')
        , 'timezones'  => array()
        , 'usergroup'  => array('class', 'multiple', 'size')
        );

        $defaultFields = array('name', 'label', 'default', 'description');

        $requestParams = JRequest::getVar('params', array());
        $selected_xml = JRequest::getVar('selected_xml', '');

        if( ! count($requestParams))
        {
            JFactory::getApplication()->enqueueMessage('No params ?', 'error');
            parent::display();

            return;
        }

        $fileName = JFile::getName($selected_xml);
        $rootElementName = 'root';
        $config = array();
        $config['type'] = 'unknown';

        switch($fileName)
        {
            case 'config.xml':

                //--temp solution..
                $rootElementName = 'config';
                $config['type'] = 'config';
                break;

            default:
                JFactory::getApplication()->enqueueMessage(sprintf(
                    jgettext('The type %s is not supported yet.. remember - this is a beta')
                    , $fileName), 'error');
                JFactory::getApplication()->enqueueMessage(
                    jgettext('But you can copy + paste the code below to your params section..'), 'error');
                break;
        }//switch

        $xml = new SimpleXMLElement('<'.$rootElementName.' />');

        if( ! $xml instanceof SimpleXMLElement)
        {
            JFactory::getApplication()->enqueueMessage(100, jgettext('Could not create XML'), 'error');

            return false;
        }

        switch($config['type'])
        {
            case 'component':
                $xml->addAttribute('type', $config['type']);
                $xml->addAttribute('version', '1.5.0');
                break;
        }//switch

        foreach($requestParams as $groupName => $elements)
        {
            $paramsElem = $xml->addChild('params');

            if($groupName != '_default')
            {
                $paramsElem->addAttribute('group', $groupName);
            }

            foreach($elements as $value => $data)
            {
                $paramElem = $paramsElem->addChild('param');

                $paramType = '';

                foreach($data as $k => $v)
                {
                    if($k == 'children')
                    {
                        //--we have options
                        foreach($v as $pos => $child)
                        {
                            //-- first step - create the element
                            foreach($child as $childK => $childV)
                            {
                                if($childK == 'data')
                                {
                                    $childElem = $paramElem->addChild('option', $childV);
                                }
                            }//foreach

                            //-- second step - add attributes
                            foreach($child as $childK => $childV)
                            {
                                if($childK != 'data')
                                {
                                    $childElem->addAttribute($childK, $childV);
                                }
                            }//foreach
                        }//foreach
                    }
                    else
                    {
                        if($k == 'type')
                        {
                            if( ! array_key_exists($v, $paramTypes))
                            {
                                JFactory::getApplication()->enqueueMessage(
                                    'EasyCreatorControllerStuffer::save_params undefined type: '.$v, 'error');

                                $paramElem->addAttribute($k, $v);
                            }
                            else
                            {
                                $paramElem->addAttribute($k, $v);
                                $paramType = $v;
                            }
                        }
                        else
                        {
                            if( ! $paramType)
                            {
                                //--no type set so far (bad) we include the element anyway..
                                $paramElem->addAttribute($k, $v);
                            }
                            else if(in_array($k, $paramTypes[$paramType])
                            || in_array($k, $defaultFields))
                            {
                                $paramElem->addAttribute($k, $v);
                            }
                        }
                    }
                }//foreach
            }//foreach
        }//foreach

        //-- Save the file
        if($config['type'] != 'unknown')
        {
            if(JFile::write(JPATH_SITE.DS.$selected_xml, $this->formatXML($xml)))
            {
                JFactory::getApplication()->enqueueMessage(jgettext('The XML file has been saved'));
            }
            else
            {
                JFactory::getApplication()->enqueueMessage(jgettext('Could not save XML file'), 'error');

                return false;
            }
        }

        else if( ! ECR_DEBUG)
        {
            //-- unknown type - unable to save
            echo '<pre class="ecr_debug">'.htmlentities($this->formatXML($xml)).'</pre>';
        }

        if(ECR_DEBUG)
        echo '<pre class="ecr_debug">'.htmlentities($this->formatXML($xml)).'</pre>';

        parent::display();
    }//function

    /**
     * Save the configuration.
     *
     * @return void
     */
    public function save_config()
    {
        $old_task = JRequest::getCmd('old_task', 'stuffer');

        try
        {
            $project = EasyProjectHelper::getProject();

            $project->updateProjectFromRequest();

            //-- Reload the project
            $project = EasyProjectHelper::getProject('', true);

            JFactory::getApplication()->enqueueMessage(jgettext('The Settings have been updated'));
        }
        catch(Exception $e)
        {
            EcrHtml::displayMessage($e);

            parent::display();

            return;
        }//try

        JRequest::setVar('view', 'stuffer');
        JRequest::setVar('task', $old_task);

        parent::display();
    }//function

    /**
     * Deletes a project manifest file and uninstalls the project.
     *
     * @return void
     */
    public function delete_project_full()
    {
        $this->delete_project(true);
    }//function

    /**
     * Deletes a project using the Joomla! installer.
     *
     * @param boolean $complete True to delete the whole project.
     *
     * @return void
     */
    public function delete_project($complete = false)
    {
        //--Get the project
        try
        {
            $project = EasyProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::displayMessage($e);

            parent::display();

            return;
        }//try

        if($project->remove($complete))
        {
            $this->setRedirect('index.php?option=com_easycreator'
            , sprintf(jgettext('The Project %s has been removed'), $project->name));
        }
        else
        {
            JFactory::getApplication()->enqueueMessage(sprintf(
            jgettext('The Project %s could not be removed'), $project->name), 'error');

            JRequest::setVar('view', 'stuffer');
            JRequest::setVar('task', 'stuffer');

            parent::display();
        }
    }//function

    /**
     * Register a table with EasyCreator.
     *
     * @return void
     */
    public function register_table()
    {
        JRequest::setVar('view', 'stuffer');

        $table_name = JRequest::getCmd('register_tbl');

        if( ! $table_name)
        {
            echo 'EMPTY';
            parent::display();

            return;
        }

        //--Get the project
        try
        {
            $project = EasyProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::displayMessage($e);

            parent::display();

            return;
        }//try

        $table = new EasyTable($table_name);

        if( ! $project->addTable($table))
        {
            echo 'ERROR adding table';
            parent::display();

            return;
        }

        $project->update();

        parent::display();
    }//function

    /**
     * Create a table.
     *
     * @return void
     */
    public function createTable()
    {
        echo 'StufferController::createTable';

        JRequest::setVar('view', 'stuffer');

        parent::display();
    }//function

    /**
     * Format an XML document.
     *
     * @param object $xml SimpleXMLElement XML document
     *
     * @todo remove
     * @deprecated
     * @return string XML
     */
    private function formatXML($xml)
    {
        $document = DOMImplementation::createDocument();
        $domnode = dom_import_simplexml($xml);

        if( ! $domnode)
        {
            return false;
        }

        $domnode = $document->importNode($domnode, true);
        $domnode = $document->appendChild($domnode);

        $document->encoding = 'utf-8';
        $document->formatOutput = true;

        return $document->saveXML();
    }//function

    /**
     * Creates install files.
     *
     * @return void
     */
    public function create_install_file()
    {
        $type1 = JRequest::getCmd('type1');
        $type2 = JRequest::getCmd('type2');

        JRequest::setVar('task', 'install');

        if( ! $type1 || ! $type2)
        {
            EcrHtml::displayMessage(__METHOD__.' - Missing values', 'error');

            parent::display();

            return;
        }

        try
        {
            ecrLoadHelper('logger');

            //--Get the project
            $project = EasyProjectHelper::getProject();

            $installPath = JPATH_ADMINISTRATOR.DS.'components'.DS.$project->comName.DS.'install';

            //-- Init buildopts
            $buildopts = JRequest::getVar('buildopts', array());

            //-- Setup logging
            $buildopts['fileName'] = date('ymd_Hi').'_'.$type1.'_'.$type2.'.log';
            $this->logger = easyLogger::getInstance('ecr', $buildopts);

            $this->logger->log('Start: '.$type1.' - '.$type2);

            if( ! JFolder::exists($installPath))
            {
                if( ! JFolder::create($installPath))
                throw new Exception('Unable to create install folder '.$installPath);

                $this->logger->log('Folder created: '.$installPath);
            }

            //-- php or sql
            switch($type1)
            {
                case 'php' :
                    //-- install or uninstall
                    switch($type2)
                    {
                        case 'install' :
                            EcrHtml::displayMessage(__METHOD__.' Unfinished install php', 'notice');
                            break;

                        case 'uninstall' :
                            EcrHtml::displayMessage(__METHOD__.' Unfinished uninstall php', 'notice');
                            break;
                        default :
                            throw new Exception('Unknown type: '.$type1.' - '.$type2);
                            break;
                    }//switch

                    break;

                case 'sql' :
                    //-- install or uninstall
                    switch($type2)
                    {
                        case 'install' :
                        case 'uninstall' :
                        case 'update' :
                            $f = 'process'.$type1.$type2;
                            $this->$f($project, $installPath);
                            break;

                        default :
                            throw new Exception('Unknown type: '.$type1.' - '.$type2);
                            break;
                    }//switch

                    break;

                default :
                    EcrHtml::displayMessage('Unknown type: '.$type1, 'error');
                    break;
            }//switch
        }
        catch(EcrLogException $e)
        {
            EcrHtml::displayMessage($e);

            parent::display();

            return;
        }//try
        catch(Exception $e)
        {
            EcrHtml::displayMessage($e);

            $this->logger->log($e->getMessage(), 'exception');
        }//try

        $this->logger->log('Finished =;)');

        $this->logger->writeLog();

        echo $this->logger->printLogBox();

        parent::display();
    }//function

    /**
     * Process the SQL install file.
     *
     * @param EasyProject $project The project.
     * @param             $installPath
     */
    private function processSQLInstall(EasyProject $project, $installPath)
    {
        $xmlPath = $this->createDbExport($project);

        $xml = JFactory::getXML($xmlPath);

        ecrLoadHelper('SQL.formatter');

        foreach($project->dbTypes as $dbType)
        {
            ecrLoadHelper('SQL.format.'.$dbType);

            $className = 'Xml2SqlFormat'.$dbType;

            /* @var Xml2SqlFormatter $formatter */
            $formatter = new $className;

            $sql = array();

            foreach($xml->database->table_structure as $tableStructure)
            {
                $sql[] = $formatter->formatCreate($tableStructure);
            }//foreach

            $string = implode("\n", $sql);

            $encoding = 'utf8';

            $fullPath = $installPath."/sql/$dbType/install.$encoding.sql";

            $msg =(JFile::exists($fullPath))
            ? sprintf(jgettext('%s Install sql file updated'), $dbType)
            : sprintf(jgettext('%s Install sql file created'), $dbType);

            if( ! JFile::write($fullPath, $string))
            {
                EcrHtml::displayMessage(jgettext('Can not create file at '.$fullPath), 'error');

                $this->logger->log($fullPath, 'Can not create file');
            }
            else
            {
                EcrHtml::displayMessage($msg);

                $this->logger->logFileWrite('DB', $fullPath, $string);
            }
        }//foreach
    }//function

    /**
     * Process the SQL uninstall file.
     *
     * @param EasyProject $project The project
     * @param string      $installPath    Path to the project root.
     */
    private function processSQLUnInstall(EasyProject $project, $installPath)
    {
        ecrLoadHelper('SQL.formatter');

        $xmlPath = $this->createDbExport($project);

        $xml = JFactory::getXML($xmlPath);

        $db = JFactory::getDbo();
        $prefix = $db->getPrefix();

        foreach($project->dbTypes as $dbType)
        {
            ecrLoadHelper('SQL.formats.'.$dbType);

            $className = 'Xml2SqlFormat'.$dbType;

            /* @var Xml2SqlFormatter $formatter */
            $formatter = new $className;

            $sql = array();

            foreach($xml->database->table_structure as $tableStructure)
            {
                $sql[] = $formatter->formatDropTable($tableStructure);
            }//foreach

            $string = implode(NL, $sql);

            $encoding = 'utf8';

            $fullPath = $installPath."/sql/$dbType/uninstall.$encoding.sql";

            $msg =(JFile::exists($fullPath))
                ? sprintf(jgettext('%s uninstall sql file updated'), $dbType)
                : sprintf(jgettext('%s uninstall sql file created'), $dbType);

            if( ! JFile::write($fullPath, $string))
            {
                EcrHtml::displayMessage(jgettext('Can not create file at '.$fullPath), 'error');

                $this->logger->log($fullPath, 'Can not create file');
            }
            else
            {
                EcrHtml::displayMessage($msg);

                $this->logger->logFileWrite('DB', $fullPath, $string);
            }
        }//foreach
    }//function

    private function createDbExport(EasyProject $project)
    {
        $db = JFactory::getDbo();

        $prefix = $db->getPrefix();

        $tableList = array();

        foreach($project->tables as $table)
        {
            $this->logger->log('Processing table '.$table);

            if('1' == $table->foreign)
            {
                $this->logger->log('Is a foreign table');

                continue;
            }

            $tableList[] = $prefix.$table;
        }//foreach

        ecrLoadHelper('SQL.mysqlexporter');

        $exporter = new JDatabaseExporterMySQL;

        $xmlString = (string)$exporter->setDbo($db)->from($tableList);

        $fullPath = $project->getExtensionPath().'/install/sql/tables.xml';

        if( ! JFile::write($fullPath, $xmlString))
        {
            throw new Exception(__METHOD__.' - Can not write the file: '.$fullPath);
        }

        return $fullPath;
    }//function

    /**
     * Process a SQL update file.
     *
     * @param EasyProject $project The project.
     * @param string      $installPath    Path to the project root.
     *
     * @return mixed
     */private function processSQLUpdate(EasyProject $project, $installPath)
    {
        ecrLoadHelper('dbupdater');

        $dbType = 'mysql';

        $updater = new dbUpdater($project, $dbType, $this->logger);

        if($updater->versions)
        {
            $this->logger->log('Updating...');

            if($updater->buildFromECRBuildDir())
            {
                EcrHtml::displayMessage(jgettext('Update sql file has been written'));
            }
            else
            {
                EcrHtml::displayMessage(jgettext('Can not create the update sql file'), 'error');
            }

            return;
        }

        $this->logger->log('Initing...');

        $fileName = $project->version.'.sql';

        $fullPath = $installPath.'/sql/updates/'.$dbType.'/'.$fileName;

        if(JFile::exists($fullPath))
        {
            EcrHtml::displayMessage(jgettext('Update sql already exists'), 'error');

            $this->logger->log('Update sql already exists in '.$fullPath);

            return;
        }

        $contents = '';

        if(JFile::write($fullPath, $contents))
        {
            EcrHtml::displayMessage(jgettext('Update sql file has been written'));

            $this->logger->logFileWrite('dbUpdate', $fullPath, $contents);
        }
        else
        {
            EcrHtml::displayMessage(jgettext('Can not create the update sql file'), 'error');

            $this->logger->logFileWrite('dbUpdate', $fullPath, $contents, 'Can not create the update sql file');
        }
    }//function
}//class
