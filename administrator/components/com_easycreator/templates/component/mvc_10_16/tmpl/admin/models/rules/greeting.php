<?php
##*HEADER*##

//-- Import Joomla formrule library
jimport('joomla.form.formrule');

/**
 * Form Rule class for the  _ECR_COM_NAME_ Component.
 *
 * @package _ECR_COM_NAME_
 */
class JFormRuleGreeting extends JFormRule
{
    /**
     * The regular expression.
     *
     * @var string
     */
    protected $regex = '^[^0-9]+$';
}//class
