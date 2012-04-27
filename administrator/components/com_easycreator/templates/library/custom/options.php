<?php
/**
 * @package        EasyCreator
 * @subpackage    Templates
 * @author        Nikolai Plath (elkuku)
 * @author        Created on 09-May-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Custom options for EasyCreator extension templates.
 *
 * @package     EasyCreator
 */
class EasyTemplateOptions
{
    private $reservedNames = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        //-- @Joomla!-version-check
        switch(ECR_JVERSION)
        {
            case '1.6':
                $this->reservedNames = array('joomla', 'bitfolge', 'phpmailer', 'phputf8', 'simplepie');
                break;

            case '1.7':
                $this->reservedNames = array('joomla', 'phpmailer', 'phputf8', 'simplepie');
                break;

            case '2.5':
                $this->reservedNames = array('cms', 'joomla', 'phpmailer', 'phputf8', 'simplepie');
                break;

            default:
                EcrHtml::displayMessage(__METHOD__.' - Unknown J! version');
                break;
        }//switch
    }//function
    /**
    * Displays available options with input fields.
    *
    * @return string HTML
    */
    public function displayOptions()
    {
        $exclude = array_merge(array('.svn', 'CVS','.DS_Store','__MACOSX'), $this->reservedNames);

        $libraries = JFolder::folders(JPATH_LIBRARIES, '.', false, false, $exclude);

        $html = '';

        $select = '';
        $select .= '<select onchange="$(\'ecr_folder_name\').value = this.value;">';
        $select .= '<option>Custom</option>';

        foreach($libraries as $library)
        {
            $select .= '<option>'.$library.'</option>';
        }//foreach

        $select .= '</select>';

        $html .= sprintf(jgettext('Existing libraries: %s'), $select);

        $html .= jgettext('Folder name').' : <input type="text" name="ecr_folder_name" id="ecr_folder_name" />';

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
        $ecr_folder_name = JRequest::getCmd('ecr_folder_name');

        if( ! $ecr_folder_name)
        {
            JFactory::getApplication()->enqueueMessage(jgettext('No folder given'), 'error');

            return false;
        }

        if(in_array($ecr_folder_name, $this->reservedNames))
        {
            JFactory::getApplication()->enqueueMessage(
                sprintf(jgettext('%s is a reserved name'), $ecr_folder_name), 'error');

            return false;
        }

        $builder->setScope(strtolower($ecr_folder_name));

        $builder->replacements->_ECR_COM_SCOPE_ = ucfirst($ecr_folder_name);

        return true;
    }//function
}//class
