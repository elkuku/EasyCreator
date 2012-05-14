<?php
##*HEADER*##
/**
 * _ECR_COM_NAME_ install Controller.
 *
 * @package     _ECR_COM_NAME_
 * @subpackage  Controller
 */
class ECR_CLASS_PREFIXControllerInstall extends JControllerBase
{
    /**
     * Method to execute the controller.
     *
     * @return  void
     *
     * @throws  RuntimeException
     */
    public function execute()
    {
        // Get the application database object.
        $db = JFactory::getDBO();

        // Get the installation database schema split into individual queries.
        switch($db->name)
        {
            case 'sqlite':
                $queries = JDatabaseDriver::splitSql(file_get_contents(dirname(JPATH_BASE)
                    .'/database/schema/sqlite/install.sql'));
                break;

            case 'mysql':
            case 'mysqli':
                $queries = JDatabaseDriver::splitSql(file_get_contents(dirname(JPATH_BASE)
                    .'/database/schema/mysql/install.sql'));
                break;

            default:
                throw new RuntimeException(sprintf('Database engine %s is not supported.', $db->name));
                break;
        }

        // Execute the installation schema queries.
        foreach($queries as $query)
        {
            if('' == trim($query))
                continue;

            $db->setQuery($query)->execute();
        }

        echo '<div class="alert alert-success">Your database has been created</div>';

        JFactory::getApplication()->input->set('view', 'list');

        JLog::add('The database has been created');
    }
}
