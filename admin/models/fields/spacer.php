<?php

/**
 * Extended  to provide g11n translations.
 *
 *
 */
class JFormFieldSpacer extends EcrFormField
{
    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    protected	$type = 'Spacer';

    protected function getInput()
    {
        return '';
    }

    protected function getLabel()
    {
        return '<div style="clear: both;"></div>'
        .'<div align="center" style="background-color: #E5FF99; font-size: 1.2em;'
        .' border-radius: 10px; margin-top: 0.4em;">'
        .jgettext($this->value)
        .'</div>';
    }
    protected function getTitle()
    {
        return $this->getLabel();
    }
}
