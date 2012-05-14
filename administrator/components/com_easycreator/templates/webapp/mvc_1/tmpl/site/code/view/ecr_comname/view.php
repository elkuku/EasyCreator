<?php
##*HEADER*##
class ECR_CLASS_PREFIXViewECR_UCF_COM_NAMEView extends JViewHtml
{
    protected $data;

    public function render()
    {
        $this->data = $this->model->getData();

        return parent::render();
    }
}
