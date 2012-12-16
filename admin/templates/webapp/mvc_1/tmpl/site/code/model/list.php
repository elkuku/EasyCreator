<?php
##*HEADER*##
/**
 * ECR_COM_NAME list model.
 *
 * @package     ECR_COM_NAME
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
            ->from('#__ECR_COM_TBL_NAME')
            ->select('*');

        $db->setQuery($query);

        return $db->loadObjectList();
    }
}
