<?php
##*HEADER*##

/**
 * Html log view class.
 */
class ECR_CLASS_PREFIXViewLogView extends JViewHtml
{
    /**
     * @var string
     */
    protected $log = '';

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
        $path = APP_PATH_DATA.'/log.php';

        $this->log = (file_exists($path))
            ? file_get_contents($path)
            : 'No log file found.';

        return parent::render();
    }
}
