<?php
##*HEADER*##

/**
 * Html ECR_UCF_COM_NAME view class.
 */
class ECR_CLASS_PREFIXViewECR_UCF_COM_NAMEView extends JViewHtml
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Method to render the view.
     *
     * @return  string  The rendered view.
     *
     * @since   12.1
     * @throws  RuntimeException
     */
    public function render()
    {
        $this->data = $this->model->getData();

        return parent::render();
    }
}
