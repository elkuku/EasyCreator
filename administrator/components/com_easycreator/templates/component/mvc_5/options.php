<?php
/**
 * @version $Id$
 * @package    EasyCreator
 * @subpackage Templates
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
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
     * @param EasyProject $project The project
     *
     * @return string HTML
     */
    public function displayOptions(EasyProject $project)
    {
        ecrLoadHelper('table');
        ecrScript('dbtables');

        $html = '';
        $html .= '
<script>
	//--Set object count to 1 - 0 is the standard field "id"
	var obCount = 1;
	var obCountOrig = 1;
</script>
';
        $html .= '<strong>'.jgettext('User defined table fields').'</strong>';
        $html .= '<br /><strong>'
        .sprintf(jgettext('Please define the fields for the table %s that will be created for your component.')
        , $project->name).'</strong>';

        $html .= EasyTableHelper::drawStdInsertRow();

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
     * @param EasyBuilder $easyBuilder The EasyBuilder
     *
     * @return boolean True on sucess.
     */
    public function processOptions(EasyBuilder $easyBuilder)
    {
        ecrLoadHelper('table');
        ecrLoadHelper('autocode');

        $fields = JRequest::getVar('fields');

        if( ! is_array($fields))
        {
            JError::raiseWarning(100, 'No fields to process');

            return false;
        }

        $tableName = strtolower($easyBuilder->project->name);

        $easyBuilder->addSubstitute('_ECR_COM_TBL_NAME_', $tableName);

        //-- Prepare extension table
        $table = new EasyTable($tableName);

        $tableFields = array();

        for($i = 0; $i < count($fields); $i ++)
        {
            $field = new EasyTableField($fields[$i]);
            $table->addField($field);
//            #$tableFields[] = new EasyTableField($fields[$i]);
        }//for

        $easyBuilder->project->addTable($table);

        $elements = array();
        $codes = array();

        /*
         * Admin
         */
        $c = EasyProjectHelper::getAutoCode('admin.sql.insert.'.$tableName);
        $c->elements = array('field');
        $codes[] = $c;
//        $e = new stdClass();
//        $e->autoCodeName = 'admin.sql.insert.'.$tableName;
//        $e->autoCodeElements = array('field');
//        $e->tags = array('#', '#');
//        $e->enclose = false;
//        $e->options = array();
//        $e->indent = '  ';
//        $elements[] = $e;

        $c = EasyProjectHelper::getAutoCode('admin.tableclass.classvar.'.$tableName);
        $c->elements = array('var');
        $c->options['varscope'] =($easyBuilder->project->phpVersion == '4') ? 'var' : 'protected';
        $codes[] = $c;
//        $e = new stdClass();
//        $e->autoCodeName = 'admin.tableclass.classvar.'.$tableName;
//        $e->autoCodeElements = array('var');
//        $e->tags = array('#', '#');
//        $e->enclose = true;
//        $e->options['varscope'] =($easyBuilder->project->phpVersion == '4') ? 'var' : 'protected';
//        $e->indent = '';
//        $elements[] = $e;

        $c = EasyProjectHelper::getAutoCode('admin.viewlist.table.'.$tableName);
        $c->elements = array('header', 'cell');
        $codes[] = $c;
//        $e = new stdClass();
//        $e->autoCodeName = 'admin.viewlist.table.'.$tableName;
//        $e->autoCodeElements = array('header', 'cell');
//        $e->tags = array('<!--', '-->');
//        $e->enclose = 'php';
//        $e->indent = '            ';
//        $e->options = array();
//        $elements[] = $e;

        $c = EasyProjectHelper::getAutoCode('admin.viewform.table.'.$tableName);
        $c->elements = array('row');
        $codes[] = $c;
//        $e = new stdClass();
//        $e->autoCodeName = 'admin.viewform.table.'.$tableName;
//        $e->autoCodeElements = array('row');
//        $e->tags = array('<!--', '-->');
//        $e->enclose = 'php';
//        $e->options = array();
//        $e->indent = '            ';
//        $elements[] = $e;

        /*
         * Site
         */
        $c = EasyProjectHelper::getAutoCode('site.viewitem.div.'.$tableName);
        $c->elements = array('divrow');
        $codes[] = $c;
//        $e = new stdClass();
//        $e->autoCodeName = 'site.viewitem.div.'.$tableName;
//        $e->autoCodeElements = array('divrow');
//        $e->tags = array('<!--', '-->');
//        $e->enclose = 'php';
//        $e->options = array();
//        $e->indent = '    ';
//        $elements[] = $e;

        foreach($codes as $autoCode)
        {
            foreach($autoCode->elements as $acElement)
            {
                $key = $autoCode->getKey().'.'.$acElement;
                $code = '';

                $code .= $autoCode->getCode($acElement, $table);

                $code = $autoCode->enclose($code, $key);

                $easyBuilder->addSubstitute($autoCode->getFormattedKey($key), $code);

                $autoCode->fields[$key] = $table->getFields();
                $autoCode->codes[$key] = $code;
                $autoCode->tables[$key] = $table;
            }//foreach
//            $autoCode = EasyProjectHelper::getAutoCode($element->autoCodeName);
//
//            if( ! $autoCode)
//            {
//                continue;
//            }
//
//            foreach($element->options as $opt => $value)
//            {
//                $autoCode->options[$opt] = $value;
//            }
//
//            foreach($element->autoCodeElements as $acElement)
//            {
//                $key = $element->autoCodeName.'.'.$acElement;
//                $code = '';
//
//                foreach($tableFields as $field)
//                {
//                    $code .= $autoCode->getCode($acElement, $field, $element->indent);
//                }//foreach
//
//                if($element->enclose)
//                {
//                    if($element->enclose === 'php')
//                    {
//                        $code = $autoCode->enclose($code, $key, true);
//                    }
//                    else
//                    {
//                        $code = $autoCode->enclose($code, $key);
//                    }
//                }
//
//                $easyBuilder->addSubstitute($element->tags[0].$key.$element->tags[1], $code);
//
//                $autoCode->fields[$key] = $tableFields;
//                $autoCode->codes[$key] = $code;
//            }//foreach

            $easyBuilder->project->addAutoCode($autoCode);
        }//foreach

        $easyBuilder->addSubstitute('#_ECR_ADMIN_LIST_COLSPAN_#', count($fields) + 2);

        return true;
    }//function
}//class
