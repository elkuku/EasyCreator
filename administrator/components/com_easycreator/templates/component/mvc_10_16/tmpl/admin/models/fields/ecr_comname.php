<?php
##*HEADER*##

// import the list field type
jimport('joomla.html.html.list');

/**
 * _ECR_COM_NAME_ Form Field class for the _ECR_COM_NAME_ component.
 *
 * @package _ECR_COM_NAME_
 */
class JFormField_ECR_COM_NAME_ extends JFormFieldList
{
    /**
     * The field type.
     *
     * @var string
     */
    protected $type = '_ECR_COM_NAME_';

    /**
     * Method to get a list of options for a list input.
     *
     * @return array An array of JHtml options.
     */
    protected function getOptions()
    {
        $query = $this->_db->getQuery(true);
        $query->select('a.id, a.greeting, a.catid');
        $query->select('cat.title AS category');
        $query->from('#___ECR_COM_TBL_NAME_ AS a');
        $query->leftJoin('#__categories AS cat on a.catid=cat.id');

        $db->setQuery($query);
        $messages = $db->loadObjectList();

        $options = array();

        foreach($messages as $message)
        {
            $options[] = JHtml::_('select.option'
            , $message->id
            , $message->greeting.($message->catid ? ' ('.$message->category.')' : '')
            );
        }//foreach

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }//function
}//class
