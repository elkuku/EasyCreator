<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @author     Nikolai Plath
 * @author     Created on 19-Oct-2017
 * @license    WTFPL
 */

/**
 * EasyCreator Controller.
 *
 * @since 0.0.27
 */
class EasyCreatorControllerEasycreator extends JControllerLegacy
{

    /**
     * Standard display method.
     *
     * @param bool       $cachable  If true, the view output will be cached
     * @param array|bool $urlparams An array of safe url parameters and their variable types,
     *                              for valid values see {@link JFilterInput::clean()}.
     *
     * @since 0.0.27
     *
     * @return \JController|void
     */
    public function display($cachable = false, $urlparams = false)
    {
	    echo 'hi';
    }

	/**
	 * Creates language template files
	 *
	 * @since 0.0.27
	 */
	public function makelangtemplates()
	{
		Ecrg11nHelper::createTemplate('com_easycreator', 'admin');
		Ecrg11nHelper::createTemplate('com_easycreator.js', 'admin');
		Ecrg11nHelper::createTemplate('com_easycreator.config', 'admin');
	}
}
