<?php
##*HEADER*##

//-- Import the class JModelList
jimport('joomla.application.component.modellist');

/**
 * ECR_COM_NAMEECR_LIST_POSTFIX Model.
 *
 * @package ECR_COM_NAME
 * @subpackage Models
 */
class ECR_COM_NAMEModelECR_COM_NAMEECR_LIST_POSTFIX extends JModelList
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

        /*admin.models.model.ECR_COM_TBL_NAME.buildquery16*/

        return $query;
    }
}
