<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-08
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package    EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewConfig extends JViewLegacy
{
    /**
     * @var string
     */
    protected $legacyTemplate;

    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        try
        {
            $this->form = $this->get('Form');

            $this->legacyTemplate = (version_compare(ECR_JVERSION, '3.0') < 0)
                ? '25'
                : 'default';

            parent::display($tpl);
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);
        }

        EcrHtml::formEnd();
    }
}
