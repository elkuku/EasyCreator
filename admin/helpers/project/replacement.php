<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers.Project
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 13-Mar-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Replacements for project templates.
 */
class EcrProjectReplacement
{
    /**
     * @var string The component name (MyComponent)
     */
    public $ECR_COM_NAME;

    /**
     * @var string The lower case component name (mycomponent)
     */
    public $ECR_LOWER_COM_NAME;

    /**
     * @var string The upper case component name (MYCOMPONENT)
     */
    public $ECR_UPPER_COM_NAME;

    /**
     * @var string The ucfirst case component name (Mycomponent)
     */
    public $ECR_UCF_COM_NAME;

    /**
     * @var string The internal component name (com_mycomponent)
     */
    public $ECR_COM_COM_NAME;

    /**
     * @var string The internal upper case component name (COM_MYCOMPONENT)
     */
    public $ECR_UPPER_COM_COM_NAME;

    /**
     * @var string The actual date
     */
    public $ECR_ACT_DATE;

    public $ECR_COM_TBL_NAME;

    public $ECR_COM_SCOPE;

    public $ECR_VERSION;

    public $ECR_DESCRIPTION;

    public $ECR_AUTHORNAME;

    public $ECR_AUTHOREMAIL;

    public $ECR_AUTHORURL;

    public $ECR_COPYRIGHT;

    public $ECR_LICENSE;

    public $ECR_LIST_POSTFIX;

    public $ECR_LOWER_LIST_POSTFIX;

    public $ECR_UPPER_LIST_POSTFIX;

    public $ECR_CLASS_PREFIX;

    public $ECR_SUBPACKAGE;

    /**
     * @var array Custom replacements. May be set with the addCustom() function.
     */
    private $customs = array();

    /**
     * @var array Custom replacements to be processed first. May be set with the addCustomPrio() function.
     */
    private $priorities = array();

    /**
     * Add a custom replacement.
     *
     * @param string $key   The key to add.
     * @param string $value The value for the key.
     *
     * @return EcrProjectReplacement
     */
    public function addCustom($key, $value)
    {
        $this->customs[$key] = $value;

        return $this;
    }

    /**
     * Add a custom replacement to be processed first.
     *
     * @param string $key   The key to add.
     * @param string $value The value for the key.
     *
     * @return EcrProjectReplacement
     */
    public function addCustomPrio($key, $value)
    {
        $this->priorities[$key] = $value;

        return $this;
    }

    /**
     * Get all replacements
     *
     * @return array
     */
    public function getReplacements()
    {
        $replacements = $this->priorities;

        $vars = get_class_vars(get_class($this));

        foreach(array_keys($vars) as $var)
        {
            if('customs' == $var || 'priorities' == $var)
                continue;

            $replacements[$var] = $this->$var;
        }

        return array_merge($replacements, $this->customs);
    }
}
