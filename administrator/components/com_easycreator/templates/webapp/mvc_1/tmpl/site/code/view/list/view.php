<?php
##*HEADER*##
class ECR_CLASS_PREFIXViewListView extends JViewHtml
{
    protected $data;

    public function render()
    {
        $this->data = $this->model->getData();

        return parent::render();
    }
}
