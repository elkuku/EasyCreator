<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Templates
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 09-May-2009
 */

/**
 * Custom options for EasyCreator extension templates.
 *
 * @package EasyCreator
 */
class TemplateOptions extends EcrProjectTemplateOptions
{
    /**
     * Displays available options with input fields.
     *
     * @param EcrProjectBase $project The project
     *
     * @return string HTML
     */
    public function displayOptions(EcrProjectBase $project)
    {
        $html = array();

        $html[] = '<label for="class_prefix">'.jgettext('Class prefix').'</label>';
        $html[] = '<input type="text" id="class_prefix" name="class_prefix" />';

        return implode(NL, $html);
    }

    /**
     * Get the required fields.
     *
     * @return array Required fields.
     */
    public function getRequireds()
    {
        return array('class_prefix');
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
        $classPrefix = JFactory::getApplication()->input->get('class_prefix');

        if( ! $classPrefix)
        {
            JFactory::getApplication()->enqueueMessage('Empty class prefix', 'error');

            return false;
        }

        $classPrefix = ucfirst(strtolower($classPrefix));

        $builder->replacements->ECR_CLASS_PREFIX = $classPrefix;

        return $this;
    }
}
