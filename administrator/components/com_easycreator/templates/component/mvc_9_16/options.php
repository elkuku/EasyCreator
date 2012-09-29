<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Templates
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 09-May-2009
 */

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

        if(false == in_array('mysql', $project->dbTypes))
        {
            $project->dbTypes = array_merge(array('mysql'), $project->dbTypes);
        }

        $fields = array();

        $field = new EcrTableField;
        $field->name = 'catid';
        $field->label = 'Category id';
        $field->type = 'INT';
        $field->length = '11';
        $field->attributes = 'UNSIGNED';
        $field->null = 'NOT_NULL';
        $field->comment = 'Category ID';
        $fields[] = $field;

        $field = new EcrTableField;
        $field->name = 'checked_out';
        $field->label = 'Checked out';
        $field->type = 'INT';
        $field->length = '11';
        $field->attributes = 'UNSIGNED';
        $field->default = '0';
        $field->null = 'NOT_NULL';
        $fields[] = $field;

        $html = array();

        $html[] = '<script type="text/javascript">';
        $html[] = '//--Set object count to 3 - 0 is the standard field "id"';
        $html[] = '//-- 1 is "catid" and 2 is "checked_out"';
        $html[] = 'var obCount = 3;';
        $html[] = 'var obCountOrig = 3;';
        $html[] = '</script>';

        $html[] = '<h3>'.jgettext('Database support').'</h3>';
        $html[] = EcrHtmlOptions::database($project);

        $html[] = '<h3>'.jgettext('User defined table fields').'</h3>';
        $html[] = '<strong>'.sprintf(
                    jgettext('Please define the fields for the table %s that will be created for your component.')
                    , '"'.strtolower($project->name).'"'
                )
                .'</strong>';

        $html[] = EcrTableHelper::startDbEditor();

        foreach($fields as $count => $field)
        {
            $html[] = EcrTableHelper::drawPredefinedRow($field, $count + 1);
        }

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
     * @param EcrProjectBuilder $builder The Builder class.
     *
     * @return boolean True on sucess.
     */
    public function processOptions(EcrProjectBuilder $builder)
    {
        $fields = JFactory::getApplication()->input->get('fields', array(), 'array');

        if(false == is_array($fields))
        {
            JFactory::getApplication()->enqueueMessage('No fields to process', 'error');

            return false;
        }

        $tableName = strtolower($builder->project->name);

        $comName = strtolower($builder->project->prefix.$builder->project->name);

        $builder->replacements->ECR_COM_TBL_NAME = $tableName;

        //-- Add the core categories table
        $table = new EcrTable('categories', true);
        $builder->project->addTable($table);

        //-- Prepare extension table
        $table = new EcrTable($tableName);

        for($i = 0; $i < count($fields); $i ++)
        {
            //@todo move out
            if($fields[$i]['name'] == 'catid')
            {
                $fields[$i]['inputType'] = 'category';
                $fields[$i]['extension'] = $comName;
            }

            if($fields[$i]['name'] == 'id')
            {
                $fields[$i]['inputType'] = 'hidden';
            }

            if($fields[$i]['name'] == 'checked_out')
            {
                $fields[$i]['display'] = false;
            }

            $field = new EcrTableField($fields[$i]);
            $table->addField($field);
        }

        $relation = new EcrTableRelation;
        $relation->type = 'LEFT JOIN';
        $relation->field = 'catid';
        $relation->onTable = 'categories';
        $relation->onField = 'id';

        $alias = new EcrTableRelationalias;
        $alias->alias = 'category';
        $alias->aliasField = 'title';

        $relation->addAlias($alias);

        $table->addRelation($relation);

        $builder->project->addTable($table);

        $codes = array();

        /*
         * Admin
         */
        $c = EcrProjectHelper::getAutoCode('admin.sql.insert.'.$tableName);
        $c->elements = array('field');
        $codes[] = $c;

        $c = EcrProjectHelper::getAutoCode('admin.models.model.'.$tableName);
        $c->elements = array('buildquery16');
        $codes[] = $c;

        $c = EcrProjectHelper::getAutoCode('admin.forms.edit.'.$tableName);
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

        $c = EcrProjectHelper::getAutoCode('site.viewcategory.table.'.$tableName);
        $c->elements = array('header', 'cell');
        $codes[] = $c;

        /* @var EcrProjectAutocode $autoCode */
        foreach($codes as $autoCode)
        {
            foreach($autoCode->elements as $acElement)
            {
                $key = $autoCode->getKey().'.'.$acElement;

                $code = $autoCode->getCode($acElement, $table);
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
}
