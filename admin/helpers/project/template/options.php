<?php
/**
 * User: elkuku
 * Date: 18.05.12
 * Time: 15:50
 */

/**
 * Extension template options.
 */
abstract class EcrProjectTemplateOptions
{
    /**
     * Displays available options with input fields.
     *
     * @param EcrProjectBase $project The project
     *
     * @return string HTML
     */
    abstract public function displayOptions(EcrProjectBase $project);

    /**
     * Get the required fields.
     *
     * @return array Required fields.
     */
    abstract public function getRequireds();

    /**
     * Process custom options.
     *
     * @param EcrProjectBuilder $builder The Builder class.
     *
     * @return boolean True on sucess.
     */
    abstract public function processOptions(EcrProjectBuilder $builder);
}
