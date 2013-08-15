<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 23-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerStuffer extends JControllerLegacy
{
    /**
     * @var EcrLogger
     */
    private $logger;

    /**
     * @var EcrResponseJson
     */
    private $response;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->response = new EcrResponseJson;

        parent::__construct($config);
    }

    /**
     * Standard display method.
     *
     * @param bool       $cachable  If true, the view output will be cached
     * @param array|bool $urlparams An array of safe url parameters and their variable types,
     *                              for valid values see {@link JFilterInput::clean()}.
     *
     * @return \JController|void
     */
    public function display($cachable = false, $urlparams = false)
    {
        $input = JFactory::getApplication()->input;

        if($input->get('tmpl') != 'component')
        {
            $ecr_project = $input->get('ecr_project');

            if('' == $ecr_project)
            {
                //-- NO PROJECT SELECTED - abort to mainscreen
                $input->set('view', 'easycreator');
                parent::display($cachable, $urlparams);

                return;
            }
        }

        $input->set('view', 'stuffer');

        parent::display($cachable, $urlparams);
    }

    /**
     * Insert a new part from templates/parts folder.
     *
     * @return void
     */
    public function new_element()
    {
        $input = JFactory::getApplication()->input;

        $ecr_project = $input->get('ecr_project');
        $group = $input->get('group');
        $part = $input->get('part');

        $element = $input->get('element');
        $scope = $input->get('element_scope');

        //$old_task = $input->get('old_task', 'stuffer');

        //-- Get the project
        try
        {
            $project = EcrProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            parent::display();

            return;
        }

        $input->set('view', 'stuffer');
        $input->set('file', '');

        if( ! $ePart = EcrProjectHelper::getPart($group, $part, $element, $scope))
        {
            EcrHtml::message(array(jgettext('Unable to load part').' [group, part]', $group, $part), 'error');
            parent::display();

            return;
        }

        if( ! $project->prepareAddPart($ecr_project))
        {
            EcrHtml::message(array(jgettext('Unable to prepare part').' [group, part]', $group, $part), 'error');
            parent::display();

            return;
        }

        //-- Setup logging
        $buildOpts = $input->get('buildopts', array(), 'array');
        $buildOpts['fileName'] = date('ymd_Hi').'_add_part.log';

        $logger = EcrLogger::getInstance('ecr', $buildOpts);

        $options = new stdClass;
        $options->ecr_project = $ecr_project;
        $options->group = $group;
        $options->part = $part;

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
            EcrHtml::message(array(jgettext('Unable to insert part').' [group, part]', $group, $part), 'error');
            $logger->writeLog();
        }
        else
        {
            EcrHtml::message(array(jgettext('Part added').' [group, part]', $group, $part));
            $logger->writeLog();

            $cache = JFactory::getCache();
            $cache->clean('EasyCreator_'.$ecr_project);
        }

        parent::display();
    }

    /**
     * Create a new relation for tables.
     *
     * @throws Exception
     * @return void
     */
    public function new_relation()
    {
        $input = JFactory::getApplication()->input;

        try
        {
            $tableName = $input->get('table_name', '');

            if('' == $tableName)
                throw new Exception(jgettext('No table given'));

            $project = EcrProjectHelper::getProject();

            if(false == array_key_exists($tableName, $project->tables))
                throw new Exception(jgettext('Invalid Table'));

            $relations = $input->get('relations', array(), 'array');

            if(false == isset($relations[$tableName]['foreign_table_field']))
                throw new Exception(jgettext('Invalid options'));

            $relation = new EcrTableRelation;

            $relation->type = $relations[$tableName]['join_type'];
            $relation->field = $relations[$tableName]['own_field'];
            $relation->onTable = $relations[$tableName]['foreign_table'];
            $relation->onField = $relations[$tableName]['foreign_table_field'];

            $alias = new EcrTableRelationalias;

            $alias->alias = $relations[$tableName]['alias'];
            $alias->aliasField = $relations[$tableName]['alias_field'];

            $relation->addAlias($alias);

            $project->tables[$tableName]->addRelation($relation);

            $project->update();
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);
        }

        $input->set('view', 'stuffer');
        $input->set('task', 'tables');

        parent::display();
    }

    /**
     * Updates AutoCode.
     *
     * @throws Exception
     * @return void
     */
    public function autocode_update()
    {
        $input = JFactory::getApplication()->input;

        $ecr_project = $input->get('ecr_project');
        $group = $input->get('group');
        $part = $input->get('part');

        $element = $input->get('element');
        $scope = $input->get('element_scope');

        $old_task = $input->get('old_task');
        $task = ($old_task) ? $old_task : 'stuffer';

        $input->set('task', $task);
        $input->set('view', 'stuffer');

        $key = "$scope.$group.$part.$element";

        try
        {
            if(false == $AutoCode = EcrProjectHelper::getAutoCode($key))
                throw new Exception(jgettext('Unable to load Autocode').sprintf(' [group, part] [%s, %s]', $group, $part));

            if(false == method_exists($AutoCode, 'insert'))
                throw new Exception(sprintf(jgettext('Insert method not found in Autocode %s'), $key));

            $project = EcrProjectHelper::getProject();

            if( ! $project->prepareAddPart($ecr_project))
                throw new Exception(sprintf(jgettext('Unable to prepare project [project, group, part] [%s, %s, %s]')
                    , $ecr_project, $group, $part));

            //-- Setup logging
            $buildOpts = $input->get('buildopts', array(), 'array');
            $buildOpts['fileName'] = date('ymd_Hi').'_add_part.log';

            $logger = EcrLogger::getInstance('ecr', $buildOpts);

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

            EcrHtml::message(array(jgettext('AutoCode updated').' [group, part]', $group, $part));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            $logger->log($e->getMessage());
        }

        $logger->writeLog();

        parent::display();
    }

    /**
     * Delete a file.
     *
     * @return void
     */
    public function delete_file()
    {
        $input = JFactory::getApplication()->input;

        $file_path = $input->getPath('file_path');
        $file_name = $input->getPath('file_name');

        $path = JPATH_ROOT.DS.$file_path.$file_name;

        if(false == JFile::exists($path))
        {
            EcrHtml::message(jgettext('invalid file'), 'error');
            echo $path;
        }
        else
        {
            if(JFile::delete($path))
            {
                EcrHtml::message(jgettext('File has been deleted'));

                //-- Clean the cache
                $ecr_project = $input->get('ecr_project');

                JFactory::getCache('EasyCreator_'.$ecr_project)->clean();
            }
            else
            {
                EcrHtml::message(jgettext('Unable to deleted the file'), 'error');
            }
        }

        $old_task = $input->get('old_task');
        $task = ($old_task) ? $old_task : 'stuffer';
        $input->set('task', $task);
        $input->set('view', 'stuffer');

        parent::display();
    }

    /**
     * Save the parameters from request.
     *
     * @todo convert JSimpleXML to SimpleXML
     *
     * @return void
     */
    public function save_params()
    {
        $input = JFactory::getApplication()->input;

        $input->set('view', 'stuffer');
        $input->set('task', 'projectparams');

        /*
         * Parameter definition
         * Object[name] = array(extra fields)
         * TODO define elsewhere for php and js
         */
        $paramTypes = array(
            'calendar' => array('format')
        , 'category' => array('class', 'section', 'scope')
        , 'editors' => array()
        , 'filelist' => array('directory', 'filter', 'exclude', 'hide_none'
            , 'hide_default', 'stripext')
        , 'folderlist' => array('directory', 'filter', 'exclude', 'hide_none', 'hide_default')
        , 'helpsites' => array()
        , 'hidden' => array('class')
        , 'imagelist' => array('directory', 'filter', 'exclude', 'hide_none'
            , 'hide_default', 'stripext')
        , 'languages' => array('client')
        , 'list' => array()
        , 'menu' => array()
        , 'menuitem' => array('state')
        , 'password' => array('class', 'size')
        , 'radio' => array()
        , 'section' => array()
        , 'spacer' => array()
        , 'sql' => array('query', 'key_field', 'value_field')
        , 'text' => array('class', 'size')
        , 'textarea' => array('rows', 'cols', 'class')
        , 'timezones' => array()
        , 'usergroup' => array('class', 'multiple', 'size')
        );

        $defaultFields = array('name', 'label', 'default', 'description');

        $requestParams = $input->get('params', array(), 'array');
        $selected_xml = $input->getPath('selected_xml');

        if(0 == count($requestParams))
        {
            JFactory::getApplication()->enqueueMessage('No params ?', 'error');
            parent::display();

            return;
        }

        $fileName = basename($selected_xml);
        $rootElementName = 'root';
        $config = array();
        $config['type'] = 'unknown';

        switch($fileName)
        {
            case 'config.xml':

                //-- Temp solution..
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
        }

        $xml = new SimpleXMLElement('<'.$rootElementName.' />');

        if(false == $xml instanceof SimpleXMLElement)
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
        }

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
                        //-- We have options
                        foreach($v as $pos => $child)
                        {
                            //-- First step - create the element
                            foreach($child as $childK => $childV)
                            {
                                if($childK == 'data')
                                {
                                    $childElem = $paramElem->addChild('option', $childV);
                                }
                            }

                            //-- Second step - add attributes
                            foreach($child as $childK => $childV)
                            {
                                if($childK != 'data')
                                {
                                    $childElem->addAttribute($childK, $childV);
                                }
                            }
                        }
                    }
                    else
                    {
                        if($k == 'type')
                        {
                            if(false == array_key_exists($v, $paramTypes))
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
                            if('' == $paramType)
                            {
                                //-- No type set so far (bad) we include the element anyway..
                                $paramElem->addAttribute($k, $v);
                            }
                            else if(in_array($k, $paramTypes[$paramType])
                                || in_array($k, $defaultFields)
                            )
                            {
                                $paramElem->addAttribute($k, $v);
                            }
                        }
                    }
                }
            }
        }

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
        else if(false == ECR_DEBUG)
        {
            //-- Unknown type - unable to save
            echo '<pre class="ecr_debug">'.htmlentities($this->formatXML($xml)).'</pre>';
        }

        if(ECR_DEBUG)
            echo '<pre class="ecr_debug">'.htmlentities($this->formatXML($xml)).'</pre>';

        parent::display();
    }

    /**
     * Save the configuration.
     *
     * @return void
     */
    public function save_config()
    {
        $input = JFactory::getApplication()->input;

        $old_task = $input->get('old_task', 'stuffer');

        try
        {
            EcrProjectHelper::getProject()
                ->updateFromRequest();

            //-- Reload the project
            EcrProjectHelper::getProject('', true);

            JFactory::getApplication()
                ->enqueueMessage(jgettext('The Settings have been updated'));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            parent::display();

            return;
        }

        $input->set('view', 'stuffer');
        $input->set('task', $old_task);

        parent::display();
    }

    /**
     * Deletes a project manifest file and uninstalls the project.
     *
     * @return void
     */
    public function delete_project_full()
    {
        $this->delete_project(true);
    }

    /**
     * Deletes a project using the Joomla! installer.
     *
     * @param boolean $complete True to delete the whole project.
     *
     * @return void
     */
    public function delete_project($complete = false)
    {
        $input = JFactory::getApplication()->input;

        try
        {
            $project = EcrProjectHelper::getProject();

            $project->remove($complete);

            $this->setRedirect('index.php?option=com_easycreator'
                , sprintf(jgettext('The Project %s has been removed'), $project->name));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);
            EcrHtml::message(jgettext('The Project could not be removed'), 'error');

            $input->set('view', 'stuffer');
            $input->set('task', 'stuffer');

            parent::display();

            return;
        }
    }

    /**
     * Register a table with EasyCreator.
     *
     * @return void
     */
    public function register_table()
    {
        $input = JFactory::getApplication()->input;

        $input->set('view', 'stuffer');

        $table_name = $input->get('register_tbl');

        if('' == $table_name)
        {
            echo 'EMPTY';
            parent::display();

            return;
        }

        //-- Get the project
        try
        {
            $project = EcrProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            parent::display();

            return;
        }

        $table = new EcrTable($table_name);

        if(false == $project->addTable($table))
        {
            echo 'ERROR adding table';
            parent::display();

            return;
        }

        $project->update();

        parent::display();
    }

    /**
     * Create a table.
     *
     * @return void
     */
    public function createTable()
    {
        echo 'StufferController::createTable';

        JFactory::getApplication()->input->set('view', 'stuffer');

        parent::display();
    }

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
    }

    /**
     * Creates install files.
     *
     * @throws Exception
     * @return void
     */
    public function create_install_file()
    {
        $input = JFactory::getApplication()->input;

        $type1 = $input->get('type1');
        $type2 = $input->get('type2');

        $input->set('task', 'install');

        if( ! $type1 || ! $type2)
        {
            EcrHtml::message(__METHOD__.' - Missing values', 'error');

            parent::display();

            return;
        }

        try
        {
            //-- Get the project
            $project = EcrProjectHelper::getProject();

            $installPath = JPATH_ADMINISTRATOR.DS.'components'.DS.$project->comName.DS.'install';

            //-- Init buildopts
            $buildopts = $input->get('buildopts', array(), 'array');

            //-- Setup logging
            $buildopts['fileName'] = date('ymd_Hi').'_'.$type1.'_'.$type2.'.log';
            $this->logger = EcrLogger::getInstance('ecr', $buildopts);

            $this->logger->log('Start: '.$type1.' - '.$type2);

            if(false == JFolder::exists($installPath))
            {
                if(false == JFolder::create($installPath))
                    throw new Exception('Unable to create install folder '.$installPath);

                $this->logger->log('Folder created: '.$installPath);
            }

            //-- PHP or SQL
            switch($type1)
            {
                case 'php' :
                    //-- Install or uninstall
                    switch($type2)
                    {
                        case 'install' :
                            EcrHtml::message(__METHOD__.' Unfinished install php', 'notice');
                            break;

                        case 'uninstall' :
                            EcrHtml::message(__METHOD__.' Unfinished uninstall php', 'notice');
                            break;
                        default :
                            throw new Exception('Unknown type: '.$type1.' - '.$type2);
                            break;
                    }

                    break;

                case 'sql' :
                    //-- Install or uninstall
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
                    }

                    break;

                default :
                    EcrHtml::message('Unknown type: '.$type1, 'error');
                    break;
            }
        }
        catch(EcrExceptionLog $e)
        {
            EcrHtml::message($e);

            parent::display();

            return;
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            $this->logger->log($e->getMessage(), 'exception');
        }

        $this->logger->log('Finished =;)');

        $this->logger->writeLog();

        echo $this->logger->printLogBox();

        parent::display();
    }

    /**
     * Process the SQL install file.
     *
     * @param EcrProjectBase $project The project.
     * @param                $installPath
     *
     * @return void
     */
    private function processSQLInstall(EcrProjectBase $project, $installPath)
    {
        $xmlPath = $this->createDbExport($project, $installPath);

        /* @var JXMLElement $xml */
        $xml = JFactory::getXML($xmlPath);

        foreach($project->dbTypes as $dbType)
        {
            if('xml' == $dbType)
            {
                $type = 'xml';
                $string = $xml->asFormattedXML();
            }
            else
            {
                $type = 'sql';
                $className = 'EcrSqlFormat'.ucfirst($dbType);

                /* @var EcrSqlFormat $formatter */
                $formatter = new $className;

                $sql = array();

                foreach($xml->database->table_structure as $tableStructure)
                {
                    $sql[] = $formatter->formatCreate($tableStructure);
                }

                $string = implode("\n", $sql);
            }

            $encoding = 'utf8';

            $fullPath = "$installPath/sql/$dbType/install.$encoding.$type";

            $msg = (JFile::exists($fullPath))
                ? sprintf(jgettext('%s Install sql file updated'), $dbType)
                : sprintf(jgettext('%s Install sql file created'), $dbType);

            if(false == JFile::write($fullPath, $string))
            {
                EcrHtml::message(sprintf(jgettext('Can not create file at %s'), $fullPath), 'error');

                $this->logger->log($fullPath, 'Can not create file');
            }
            else
            {
                EcrHtml::message($msg);

                $this->logger->logFileWrite('DB', $fullPath, $string);
            }
        }
    }

    /**
     * Process the SQL uninstall file.
     *
     * @param EcrProjectBase $project        The project
     * @param string         $installPath    Path to the project root.
     *
     * @return void
     */
    private function processSQLUnInstall(EcrProjectBase $project, $installPath)
    {
        $xmlPath = $this->createDbExport($project, $installPath);

        $xml = JFactory::getXML($xmlPath);

        foreach($project->dbTypes as $dbType)
        {
            if('xml' == $dbType)
            {
                $type = 'xml';
                $string = $xml->asFormattedXML();
            }
            else
            {
                $type = 'sql';

                $className = 'EcrSqlFormat'.ucfirst($dbType);

                /* @var EcrSqlFormat $formatter */
                $formatter = new $className;

                $sql = array();

                foreach($xml->database->table_structure as $tableStructure)
                {
                    $sql[] = $formatter->formatDropTable($tableStructure);
                }

                $string = implode(NL, $sql);
            }

            $encoding = 'utf8';

            $fullPath = "$installPath/sql/$dbType/uninstall.$encoding.$type";

            $msg = (JFile::exists($fullPath))
                ? sprintf(jgettext('%s uninstall sql file updated'), $dbType)
                : sprintf(jgettext('%s uninstall sql file created'), $dbType);

            if(false == JFile::write($fullPath, $string))
            {
                EcrHtml::message(jgettext('Can not create file at '.$fullPath), 'error');

                $this->logger->log($fullPath, 'Can not create file');
            }
            else
            {
                EcrHtml::message($msg);

                $this->logger->logFileWrite('DB', $fullPath, $string);
            }
        }
    }

    /**
     * Create the XML database export file.
     *
     * @param EcrProjectBase $project
     * @param string         $installPath
     *
     * @return string
     * @throws Exception
     */
    private function createDbExport(EcrProjectBase $project, $installPath)
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
        }

        if(empty($tableList))
            throw new Exception('The project contains no tables');

        $exporter = new EcrSqlMysqlexporter;

        $xmlString = (string)$exporter->setDbo($db)->from($tableList);

        $fullPath = $installPath.'/sql/tables.xml';

        if(false == JFile::write($fullPath, $xmlString))
            throw new Exception(__METHOD__.' - Can not write the file: '.$fullPath);

        return $fullPath;
    }

    /**
     * Process a SQL update file.
     *
     * @param EcrProjectBase $project        The project.
     * @param string         $installPath    Path to the project root.
     *
     * @return mixed
     */
    private function processSQLUpdate(EcrProjectBase $project, $installPath)
    {
        $dbType = 'mysql';

        $updater = new EcrDbUpdater($project, $dbType, $this->logger);

        if($updater->versions)
        {
            $this->logger->log('Updating...');

            if($updater->buildFromECRBuildDir())
            {
                EcrHtml::message(jgettext('Update sql file has been written'));
            }
            else
            {
                EcrHtml::message(jgettext('Can not create the update sql file'), 'error');
            }

            return;
        }

        $this->logger->log('Initing...');

        $fileName = $project->version.'.sql';

        $fullPath = $installPath.'/sql/updates/'.$dbType.'/'.$fileName;

        if(JFile::exists($fullPath))
        {
            EcrHtml::message(jgettext('Update sql already exists'), 'error');

            $this->logger->log('Update sql already exists in '.$fullPath);

            return;
        }

        $contents = '';

        if(JFile::write($fullPath, $contents))
        {
            EcrHtml::message(jgettext('Update sql file has been written'));

            $this->logger->logFileWrite('dbUpdate', $fullPath, $contents);
        }
        else
        {
            EcrHtml::message(jgettext('Can not create the update sql file'), 'error');

            $this->logger->logFileWrite('dbUpdate', $fullPath, $contents, 'Can not create the update sql file');
        }
    }

    public function loadPreset()
    {
        try
        {
            $this->response->data = EcrProjectHelper::getProject()
                ->getPreset(JFactory::getApplication()->input->get('preset'))->toJson();
        }
        catch(Exception $e)
        {
            $this->response->message = $e->getMessage();
            $this->response->debug = $e->getTraceAsString();
            $this->response->status = 1;
        }

        echo $this->response;

        jexit();
    }

    /**
     * Get an action.
     *
     * @AJAX
     *
     * @return void
     */
    public function getAction()
    {
        $input = JFactory::getApplication()->input;

        try
        {
            $options = json_decode($input->getHtml('options'));
            $options = $options ?: array();

            $cnt = $input->getInt('cnt');

            $this->response->message = EcrProjectAction::getInstance($input->get('type'))
                ->setOptions($options)
                ->getFields($cnt);
        }
        catch(Exception $e)
        {
            $this->response->debug = $e->getMessage();
            $this->response->status = $e->getCode() ? : 1;
        }

        echo $this->response;

        jexit();
    }

    /**
     * Get EasyCreator paramaters.
     *
     * @AJAX
     *
     * @return void
     */
    public function getEcrParams()
    {
        $this->response->status = 0;

        $this->response->message = json_encode(JComponentHelper::getParams('com_easycreator')->toArray());

        echo $this->response;

        jexit();
    }

    public function checkDropBox()
    {
        require JPATH_COMPONENT.'/helpers/Dropbox/bootstrap.php';

        $input = JFactory::getApplication()->input;

        $key = $input->get('key');
        $secret = $input->get('seqret');

        $protocol = ( ! empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $callback = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
        $encrypter = new \Dropbox\OAuth\Storage\Encrypter('12312323423435456654457545646542');
        $storage = new \Dropbox\OAuth\Storage\Session($encrypter);
        $OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, $storage, $callback);
        $dropbox = new \Dropbox\API($OAuth);

        $accountInfo = $dropbox->accountInfo();

        if($accountInfo && '200' == $accountInfo['code'])
        {
            EcrHtml::message('Welcome to Dropbox.');

            if(ECR_DEBUG) var_dump($accountInfo);
        }
        else
        {
            EcrHtml::message('Something went wrong...');

            var_dump($accountInfo);
        }

        parent::display();
    }
}
