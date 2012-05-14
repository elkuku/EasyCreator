<?php
/**
 * @package    EasyCreator
 * @subpackage Parts
 * @author     hidabe -- pls add your credits =;)
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 20-Apr-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * ECR_ELEMENT_NAME model.
 *
 * @package    EasyCreator
 * @subpackage Templates.Parts
 */
class PartTablesAdmin_create
{
    public $group = 'tables';

    /**
     * Info about the thing.
     *
     * @return EcrTemplateInfo
     */
    public function info()
    {
        $info = new EcrProjectTemplateInfo;

        $info->group = $this->group;
        $info->title = 'Create and Admin';
        $info->description = jgettext('This will create a new table with admin interface');

        return $info;
    }//function

    /**
     * Get insert options.
     *
     * @return void
     */
    public function getOptions()
    {
        ?>
<strong><?php echo jgettext('Include Fields');?></strong>
<table>
	<tr>
		<td><?php echo jgettext('Id');?></td>
		<td><?php echo JHTML::_('select.booleanlist', 'pack_mat_id', 'readonly; disabled=true', 1);?></td>
	</tr>
	<tr>
		<td><?php echo jgettext('Title');?></td>
		<td><?php echo JHTML::_('select.booleanlist', 'pack_mat_title', 'readonly; disabled=true', 1);?></td>
	</tr>
	<tr>
		<td><?php echo jgettext('Description');?></td>
		<td><?php echo JHTML::_('select.booleanlist', 'pack_mat_description', '', 1);?></td>
	</tr>
	<tr>
		<td><?php echo jgettext('Published');?></td>
		<td><?php echo JHTML::_('select.booleanlist', 'pack_mat_published', '', 1);?></td>
	</tr>
	<tr>
		<td><?php echo jgettext('Ordering');?></td>
		<td><?php echo JHTML::_('select.booleanlist', 'pack_mat_ordering', '', 1);?></td>
	</tr>
</table>
        <?php
        EcrHtml::drawSelectName();
        EcrHtml::drawLoggingOptions();

        $requireds = array('element_name');
        EcrHtml::drawSubmitParts($requireds);
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EcrProjectBase $easyProject The project.
     * @param array $options Insert options.
     * @param EcrLogger $logger The EcrLogger.
     *
     * @return boolean
     */
    public function insert(EcrProjectBase $easyProject, $options, EcrLogger $logger)
    {
        $element_name = JRequest::getCmd('element_name');

        $element_params = array();
        $element_params['description'] = JRequest::getVar('pack_mat_description', 0);
        $element_params['published'] = JRequest::getVar('pack_mat_published', 0);
        $element_params['ordering'] = JRequest::getVar('pack_mat_ordering', 0);

        /*
         * Add substitutes
         */
        $easyProject->addSubstitute('ECR_ELEMENT_NAME', $element_name);
        $easyProject->addSubstitute('_ECR_LOWER_ELEMENT_NAME_', strtolower($element_name));
        $easyProject->addSubstitute('_ECR_TABLE_NAME_'
        , strtolower($easyProject->getSubstitute('ECR_COM_NAME').'_'.$element_name));

        /*
         * Read part options files
         */
        $files = JFolder::files($options->pathSource.DS.'options', '.', true, true);

        foreach($files as $file)
        {
            $fileContents = JFile::read($file);

            if(strpos($fileContents, '<?php') === 0)
            {
                $fileContents = substr($fileContents, 6);
            }

            $easyProject->substitute($fileContents);
            $easyProject->addSubstitute('##'.strtoupper(JFile::stripExt(JFile::getName($file))).'##', $fileContents);
        }//foreach

        /*
         * Add/remove substitutes
         */
        $tableVars = '';
        $tableVars .= $this->formatTableVar('id', 'int', '0', array('Primary key'));
        $tableVars .= $this->formatTableVar('title', 'varchar');

        if($element_params['description'])
        {
            $tableVars .= $this->formatTableVar('description', 'text');
        }
        else
        {
            $easyProject->addSubstitute('##ECR_MAT_DESCRIPTION_VIEW1##', '');
            $easyProject->addSubstitute('##ECR_MAT_DESCRIPTION_VIEW2##', '');
            $easyProject->addSubstitute('##ECR_SMAT_DESCRIPTION_VIEW0##', '');
            $easyProject->addSubstitute('##ECR_SMAT_DESCRIPTION_VIEW1##', '');
            $easyProject->addSubstitute('##ECR_SMAT_DESCRIPTION_CONTROLLER1##', '');
        }

        if($element_params['ordering'])
        {
            $tableVars .= $this->formatTableVar('ordering', 'int', '0');
        }
        else
        {
            $easyProject->addSubstitute('##ECR_MAT_ORDERING_VIEW0##', '');
            $easyProject->addSubstitute('##ECR_MAT_ORDERING_VIEW1##', '');
            $easyProject->addSubstitute('##ECR_MAT_ORDERING_VIEW2##', '');
            $easyProject->addSubstitute('##ECR_MAT_ORDERING_MODAL1##', "");
            $easyProject->addSubstitute('##ECR_MAT_ORDERING_CONTROLLER1##', '');
        }

        if($element_params['published'])
        {
            $tableVars .= $this->formatTableVar('published', 'int', '0');
        }
        else
        {
            $easyProject->addSubstitute('##ECR_MAT_PUBLISHED_VIEW1##', '');
            $easyProject->addSubstitute('##ECR_MAT_PUBLISHED_VIEW11##', '');
            $easyProject->addSubstitute('##ECR_MAT_PUBLISHED_VIEW2##', '');
            $easyProject->addSubstitute('##ECR_SMAT_PUBLISHED_VIEW0##', '');
            $easyProject->addSubstitute('##ECR_SMAT_PUBLISHED_VIEW1##', '');
        }

        $easyProject->addSubstitute('##ECR_TABLE_VARS##', $tableVars);

        /*
         * Process files
         */
        JRequest::setVar('element_scope', 'admin');

        if( ! $easyProject->insertPart($options, $logger))
        {
            return false;
        }

        /*
         * Create the table
         */
        $db =& JFactory::getDBO();
        $query = 'CREATE TABLE #__'.$easyProject->getSubstitute('_ECR_TABLE_NAME_').' ('
        . '`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY'
        . ', `title` VARCHAR( 255 ) NOT NULL';

        if($element_params['description'])
        $query .= ', `description` TEXT NOT NULL';

        if($element_params['published'])
        $query .= ', `published` TINYINT( 1 ) NOT NULL';

        if($element_params['ordering'])
        $query .= ', `ordering` INT( 11 ) NOT NULL';

        $query .= ') ENGINE = MYISAM ';
        $db->setQuery($query);

        if( ! $db->query())
        {
            JFactory::getApplication()->enqueueMessage($db->getError(), 'error');
            $logger->logQuery($query, $db->getError());

            return false;
        }

        $logger->logQuery($query);

        return true;
    }//function

    /**
     * Format variables to be displayed as docComment in class header.
     *
     * @param string $var Variable name
     * @param string $type Data type
     * @param string $def Default value
     * @param array $adds Additional comments
     *
     * @return string
     */
    private function formatTableVar($var, $type, $def = 'NULL', $adds = array())
    {
        $string = '';
        $string .= '	/**'.NL;
        $string .= '	 * @var '.$type.NL;

        foreach($adds as $add)
        {
            $string .= '	 * '.$add.NL;
        }//foreach

        $string .= '	 */'.NL;
        $string .= '	var $'.$var.' = '.$def.';'.NL.NL;

        return $string;
    }//function
}//class
