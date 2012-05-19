<?php
/**
 * @package    EasyCreator
 * @subpackage Templates
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 09-May-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Custom options for EasyCreator extension templates.
 *
 * @package EasyCreator
 */
class TemplateOptions extends EcrProjectTemplateOptions
{
    /**
     * Displays available options with input fields.
     *
     * @param EcrProjectBase $project The project
     *
     * @return string HTML
     */
    public function displayOptions(EcrProjectBase $project)
    {
        ecrScript('dbtables');
        ecrStylesheet('dbtables');

        $html = array();

        $html[] = '<script type="text/javascript">';
        $html[] = '//--Set object count to 1 - 0 is the standard field "id"';
        $html[] = 'var obCount = 1;';
        $html[] = 'var obCountOrig = 1';
        $html[] = '</script>';
        $html[] = '<strong>'.jgettext('User defined table fields').'</strong>';

        $html[] = '<br /><strong>'
            .sprintf(jgettext('Please define the fields for the table %s that will be created for your component.')
                , $project->name).'</strong>';

        $html[] = EcrTableHelper::startDbEditor();

        $html[] = EcrTableHelper::endDbEditor();

        return implode(NL, $html);
    }

    /**
     * Get the required fields.
     *
     * @return array Required fields.
     */
    public function getRequireds()
    {
        return array();
    }

    /**
     * Process custom options.
     *
     * @param EcrProjectBuilder $builder The Builder class
     *
     * @return boolean True on sucess.
     */
    public function processOptions(EcrProjectBuilder $builder)
    {
        $fields = JRequest::getVar('fields');

        if(false == is_array($fields))
        {
            JFactory::getApplication()->enqueueMessage('No fields to process', 'error');

            return false;
        }

        $tableName = strtolower($builder->project->name);

        $builder->replacements->ECR_COM_TBL_NAME = $tableName;

        //-- Prepare extension table
        $table = new EcrTable($tableName);

        for($i = 0; $i < count($fields); $i ++)
        {
            $field = new EcrTableField($fields[$i]);
            $table->addField($field);
        }

        $builder->project->addTable($table);

        $codes = array();

        /*
         * Admin
         */
        $c = EcrProjectHelper::getAutoCode('admin.sql.insert.'.$tableName);
        $c->elements = array('field');
        $codes[] = $c;

        $c = EcrProjectHelper::getAutoCode('admin.tableclass.classvar.'.$tableName);
        $c->elements = array('var');
        $c->options['varscope'] = ($builder->project->phpVersion == '4') ? 'var' : 'protected';
        $codes[] = $c;

        $c = EcrProjectHelper::getAutoCode('admin.viewlist.table.'.$tableName);
        $c->elements = array('header', 'cell');
        $codes[] = $c;

        $c = EcrProjectHelper::getAutoCode('admin.viewform.table.'.$tableName);
        $c->elements = array('row');
        $codes[] = $c;

        /*
         * Site
         */
        $c = EcrProjectHelper::getAutoCode('site.viewitem.div.'.$tableName);
        $c->elements = array('divrow');
        $codes[] = $c;

        /* @var EcrProjectAutocode $autoCode */
        foreach($codes as $autoCode)
        {
            foreach($autoCode->elements as $acElement)
            {
                $key = $autoCode->getKey().'.'.$acElement;
                $code = '';

                $code .= $autoCode->getCode($acElement, $table);

                $code = $autoCode->enclose($code, $key);

                $builder->replacements->addCustom($autoCode->getFormattedKey($key), $code);

                $autoCode->fields[$key] = $table->getFields();
                $autoCode->codes[$key] = $code;
                $autoCode->tables[$key] = $table;
            }

            $builder->project->addAutoCode($autoCode);
        }

        $builder->replacements->addCustom('#_ECR_ADMIN_LIST_COLSPAN_#', count($fields) + 2);

        return true;
    }
}//class
