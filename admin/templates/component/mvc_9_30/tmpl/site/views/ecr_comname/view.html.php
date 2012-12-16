<?php
##*HEADER*##

/**
 * HTML View class for the ECR_COM_NAME Component.
 *
 * @package    ECR_COM_NAME
 * @subpackage Views
 */
class ECR_COM_NAMEViewECR_COM_NAME extends JViewLegacy
{
    /**
     * @var
     */
    protected $data;

    /**
     * ECR_COM_NAME view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $this->data = $this->get('Data');

        parent::display($tpl);
    }
}
