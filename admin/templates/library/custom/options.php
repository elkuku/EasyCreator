<?php
/**
 * @package        EasyCreator
 * @subpackage     Templates
 * @author         Nikolai Plath (elkuku)
 * @author         Created on 09-May-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Custom options for EasyCreator extension templates.
 *
 * @package     EasyCreator
 */
class TemplateOptions extends EcrProjectTemplateOptions
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
            case '2.5':
                $this->reservedNames = array('cms', 'joomla', 'phpmailer', 'phputf8', 'simplepie');
                break;

            case '3.0':
            case '3.1':
                $this->reservedNames = array('cms', 'compat', 'joomla', 'legacy', 'phpmailer', 'phputf8', 'simplepie');
                break;

            case '3.2':
	        case '3.3':
		        $this->reservedNames = array('cms', 'compat', 'fof', 'framework', 'idna_convert', 'joomla', 'legacy',
                    'phpmailer', 'phputf8', 'simplepie');
                break;

	        case '3.4':
		        $this->reservedNames = array('cms', 'compat', 'fof', 'framework', 'idna_convert', 'joomla', 'legacy',
                    'phpmailer', 'phputf8', 'simplepie', 'vendor');
                break;

            default:
                EcrHtml::message(__METHOD__.' - Unknown J! version');
                break;
        }
    }

    /**
     * Displays available options with input fields.
     *
     * @param EcrProjectBase $project
     *
     * @return string HTML
     */
    public function displayOptions(EcrProjectBase $project)
    {
        $exclude = array_merge(array('.svn', 'CVS', '.DS_Store', '__MACOSX'), $this->reservedNames);

        $libraries = JFolder::folders(JPATH_LIBRARIES, '.', false, false, $exclude);

        $html = '';

        $select = '';
        $select .= '<select onchange="$(\'ecr_folder_name\').value = this.value;">';
        $select .= '<option>Custom</option>';

        foreach($libraries as $library)
        {
            $select .= '<option>'.$library.'</option>';
        }

        $select .= '</select>';

        $html .= sprintf(jgettext('Existing libraries: %s'), $select);

        $html .= jgettext('Folder name').' : <input type="text" name="ecr_folder_name" id="ecr_folder_name" />';

        return $html;
    }

    /**
     * Get the required fields.
     *
     * @return array Required fields.
     */
    public function getRequireds()
    {
        $requireds = array('ecr_folder_name');

        return $requireds;
    }

    /**
     * Process custom options.
     *
     * @param EcrProjectBuilder $builder The Builder class.
     *
     * @return boolean True on sucess.
     */
    public function processOptions(EcrProjectBuilder $builder)
    {
        $ecr_folder_name = JFactory::getApplication()->input->get('ecr_folder_name');

        if('' == $ecr_folder_name)
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

        $builder->replacements->ECR_COM_SCOPE = ucfirst($ecr_folder_name);

        return true;
    }
}
