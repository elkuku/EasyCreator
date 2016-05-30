<?php
/**
 * @package    EasyCreator
 * @author     Nikolai Plath (elkuku) <der.el.kuku@gmail.com>
 * @created    Created on 03-Mar-11
 * @copyright  2008 elkuku
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */
defined('_JEXEC') || die('=;)');

// @Joomla!-compat 2.5
jimport('joomla.application.component.modeladmin');

/**
 * Prototype admin model.
 *
 * @since  0
 */
class EasyCreatorModelConfig extends JModelAdmin
{
	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @throws Exception
	 *
	 * @return JForm
	 */
	public function getForm($data = array(), $loadData = true)
	{
		JLoader::import('models.fields.formfield', JPATH_COMPONENT);

		// Get the form.
		$form = $this->loadForm(
			'com_easycreator.config',
			'config',
			array('control' => 'params', 'load_data' => $loadData)
		);

		if (empty($form))
		{
			throw new Exception(jgettext('Unable to load the config form'));
		}

		$form->bind(JComponentHelper::getParams('com_easycreator'));

		return $form;
	}
}
