<?php
##*HEADER*##

/**
 * Html list view class.
 */
class ECR_CLASS_PREFIXViewListView extends JViewHtml
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
