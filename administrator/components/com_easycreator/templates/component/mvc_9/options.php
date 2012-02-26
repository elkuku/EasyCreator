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
class EasyTemplateOptions
{
    /**
     * Displays available options with input fields.
     *
     * @param EcrProject $project The project
     *
     * @return string HTML
     */
    public function displayOptions(EcrProject $project)
    {
        ecrScript('dbtables');
        ecrStylesheet('dbtables');

        $html = '';
        $html .= '
<script>
	//--Set object count to 3 - 0 is the standard field "id"
	//-- 1 is "catid" and 2 is "checked_out"
	var obCount = 3;
	var obCountOrig = 3;
</script>
';
        $html .= '<strong>'.jgettext('User defined table fields').'</strong>';
        $html .= '<br /><strong>'
        .sprintf(jgettext('Please define the fields for the table %s that will be created for your component.')
        , $project->name).'</strong>';

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

        $html .= EcrTableHelper::startDbEditor();

        foreach($fields as $count => $field)
        {
            $html .= EcrTableHelper::drawPredefinedRow($field, $count + 1);
        }//foreach

        $html .= EcrTableHelper::endDbEditor();

        return $html;
    }//function

    /**
     * Get the required fields.
     *
     * @return array Required fields.
     */
    public function getRequireds()
    {
        return array();
    }//function

    /**
     * Process custom options.
     *
     * @param EcrBuilder $builder The Builder class.
     *
     * @return boolean True on sucess.
     */
    public function processOptions(EcrBuilder $builder)
    {
        $fields = JRequest::getVar('fields');

        if( ! is_array($fields))
        {
            JFactory::getApplication()->enqueueMessage('No fields to process', 'error');

            return false;
        }

        $tableName = strtolower($builder->project->name);

        $builder->addSubstitute('_ECR_COM_TBL_NAME_', $tableName);

        //-- Add the core categories table
        $table = new EcrTable('categories', true);
        $builder->project->addTable($table);

        //-- Prepare extension table
        $table = new EcrTable($tableName);

        $tableFields = array();

        for($i = 0; $i < count($fields); $i ++)
        {
            //@todo move out
            if($fields[$i]['name'] == 'catid')
            {
                $fields[$i]['inputType'] = 'category';
            }

            $field = new EcrTableField($fields[$i]);
            $table->addField($field);
        }//for

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
        $c->elements = array('buildquery');
        $codes[] = $c;

        $c = EcrProjectHelper::getAutoCode('admin.tableclass.classvar.'.$tableName);
        $c->elements = array('var');
        $c->options['varscope'] =($builder->project->phpVersion == '4') ? 'var' : 'protected';
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

        foreach($codes as $autoCode)
        {
            foreach($autoCode->elements as $acElement)
            {
                $key = $autoCode->getKey().'.'.$acElement;
                $code = '';

                $code .= $autoCode->getCode($acElement, $table);

                $code = $autoCode->enclose($code, $key);

                $builder->addSubstitute($autoCode->getFormattedKey($key), $code);

                $autoCode->fields[$key] = $table->getFields();
                $autoCode->codes[$key] = $code;
                $autoCode->tables[$key] = $table;
            }//foreach

            $builder->project->addAutoCode($autoCode);
        }//foreach

        $builder->addSubstitute('#_ECR_ADMIN_LIST_COLSPAN_#', count($fields) + 2);

        return true;
    }//function
}//class
