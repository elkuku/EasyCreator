<?php
##*HEADER*##

//-- Import the class JModelList
jimport('joomla.application.component.modellist');

/**
 * _ECR_COM_NAME__ECR_LIST_POSTFIX_ Model.
 *
 * @package _ECR_COM_NAME_
 * @subpackage Models
 */
class _ECR_COM_NAME_Model_ECR_COM_NAME__ECR_LIST_POSTFIX_ extends JModelList
{
    /**
     * Method to build an SQL query to load the list data.
     * Funktion um einen SQL Query zu erstellen der die Daten für die Liste läd.
     *
     * @return string SQL query
     */
    protected function getListQuery()
    {
        // Ein Datenbankobjekt beziehen.
        $db = JFactory::getDBO();

        // Ein neues (leeres) Queryobjekt beziehen.
        $query = $db->getQuery(true);

        /*admin.models.model._ECR_COM_TBL_NAME_.buildquery16*/

        return $query;
    }//function
}//class
