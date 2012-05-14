<?php
/**
 * @package		EasyCreator
 * @subpackage	Templates
 * @author		Nikolai Plath (elkuku)
 * @author		Created on 09-May-2009
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
     * Displays available options with nput fields.
     *
     * @return string HTML
     */
    public function displayOptions()
    {
        $html = jgettext('Folder name').' : <input type="text" name="ecr_folder_name" id="ecr_folder_name" />';

        return $html;
    }//function

    /**
     * Get the required fields.
     *
     * @return array Required fields.
     */
    public function getRequireds()
    {
        $requireds = array('ecr_folder_name');

        return $requireds;
    }//function

    /**
     * Process custom options.
     *
     * @param EcrProjectBuilder $builder The Builder class.
     *
     * @return boolean True on sucess.
     */
    public function processOptions(EcrProjectBuilder $builder)
    {
        if( ! $ecr_folder_name = JRequest::getCmd('ecr_folder_name'))
        {
            JFactory::getApplication()->enqueueMessage(jgettext('No folder given'), 'error');

            return false;
        }

        $builder->setScope($ecr_folder_name);
        $builder->replacements->ECR_COM_SCOPE = ucfirst($ecr_folder_name);

        return true;
    }//function
}//class
