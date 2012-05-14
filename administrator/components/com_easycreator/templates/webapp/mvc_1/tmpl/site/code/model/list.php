<?php
##*HEADER*##
/**
 * _ECR_COM_NAME_ list model.
 *
 * @package     _ECR_COM_NAME_
 * @subpackage  Model
 */
class ECR_CLASS_PREFIXModelList extends JModelBase
{
    /**
     * Get the data.
     *
     * @return mixed
     */
    public function getData()
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->from('#___ECR_COM_TBL_NAME_')
            ->select('*');

        $db->setQuery($query);

        return $db->loadObjectList();
    }
}
