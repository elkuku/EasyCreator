<?php defined('_JEXEC') || die('=;)');
/**
 * @author     Nikolai Plath
 * @author     Created on 28-Sep-2017
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator Controller.
 *
 * @since 0.0.25.6
 */
class EasyCreatorControllerMaintainer extends JControllerLegacy
{

    /**
     * Create a list of installed Joomla! core extensoins.
     *
     * @since 0.0.25.6
     */
    public function createJExtensionsList()
    {
        $projects = array();

        foreach (EcrProjectHelper::getProjectTypes() as $type => $project) {
            if (in_array($type, ['cliapp', 'webapp', 'package'])) {
                continue;
            }
            foreach ($project->getInstallScopes() as $scope) {
                $projects[$type][$scope] = $project->getAllProjects($scope);
            }
        }

        JFile::write(JPATH_COMPONENT_ADMINISTRATOR . '/data/jextensions/jcore-' . ECR_JVERSION . '.json', json_encode($projects));

        JFactory::getApplication()->enqueueMessage('Extension list has been created');
        return parent::display();
    }
}
