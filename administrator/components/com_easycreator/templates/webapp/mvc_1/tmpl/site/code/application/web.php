<?php
##*HEADER*##

/**
 * A JApplicationWeb application.
 *
 * @package  ECR_COM_NAME
 */
class ECR_CLASS_PREFIXApplicationWeb extends JApplicationWeb
{
    /**
     * The "task".
     *
     * @var string
     */
    protected $do = '';

    /**
     * A database object for the application to use.
     *
     * @var    JDatabaseDriver
     */
    protected $db;

    private $messages = array();

    /**
     * @param        $message
     * @param string $type
     */
    public function addMessage($message, $type = 'info')
    {
        if(false == array_key_exists($type, $this->messages))
            $this->messages[$type] = array();

        $this->messages[$type][] = $message;
    }

    /**
     * Render messages from the internal message queue.
     *
     * @return string
     */
    private function renderMessages()
    {
        $html = array();

        foreach($this->messages as $type => $messages)
        {
            $html[] = '<div class="alert alert-'.$type.'">';
            $html[] = '<ul>';

            foreach($messages as $message)
            {
                $html[] = '<li>'.$message.'</li>';
            }

            $html[] = '</ul>';
            $html[] = '</div>';
        }

        return implode("\n", $html);
    }

    /**
     * Overrides the parent doExecute method to run the web application.
     *
     * This method should include your custom code that runs the application.
     *
     * @throws RuntimeException
     * @return  void
     */
    protected function doExecute()
    {
        ob_start();

        try
        {
            // Load the database object if necessary.
            if(empty($this->db))
                $this->loadDatabase();

            $this->do = $this->input->get('do', 'default');

            $this->fetchController()
                ->execute();

            $output = $this->fetchView($this->fetchModel())
                ->render();
        }
        catch(Exception $e)
        {
            $output = '<div class="alert alert-error"> '.$e->getMessage().'</div> ';

            JLog::add($e->getMessage(), JLog::ERROR);
        }

        $debugOutput = ob_get_clean();

        if('get' == $this->do)
        {
            // This is a JSON output

            echo $output;

            return;
        }

        ob_start();

        include APP_PATH_TEMPLATE.'/default.php';

        $html = ob_get_clean();

        $html = str_replace(' <!--ApplicationOutput-->', $output, $html);
        $html = str_replace('<!--ApplicationMessage-->', $this->renderMessages(), $html);
        $html = str_replace('<!--ApplicationDebug-->', $debugOutput, $html);

        $this->appendBody($html);
    }

    /**
     * Fetch the configuration data for the application.
     *
     * @param string $file
     * @param string $class
     *
     * @throws RuntimeException
     * @internal param $targetApplication
     *
     * @return \ECR_CLASS_PREFIXConfig|mixed
     */
    public function fetchConfigurationData($file = '', $class = 'ECR_CLASS_PREFIXConfig')
    {
        // Ensure that required path constants are defined.
        defined('JPATH_CONFIGURATION') || define('JPATH_CONFIGURATION', realpath(dirname(JPATH_BASE).'/config'));

        // Set the configuration file path for the application.
        $file = (file_exists(JPATH_CONFIGURATION.'/configuration.php'))
            ? JPATH_CONFIGURATION.'/configuration.php'
            // Default to the distribution configuration.
            : JPATH_CONFIGURATION.'/configuration.dist.php';

        if(false == is_readable($file))
            throw new RuntimeException('Configuration file does not exist or is unreadable.', 1);

        include_once $file;

        return new $class;
    }

    /**
     * Method to get a controller object based on the command line input.
     *
     * @return  JControllerBase
     *
     * @since   1.0
     * @throws  InvalidArgumentException
     */
    protected function fetchController()
    {
        $base = 'ECR_CLASS_PREFIXController';

        $sub = strtolower($this->do);

        $className = $base.ucfirst($sub);

        // If the requested controller exists let's use it.
        if(class_exists($className))
        {
            return new $className($this->input, $this);
        }

        // Nothing found. Panic.
        throw new InvalidArgumentException('Controller not found: '.$sub, 400);
    }

    /**
     * Method to get a controller object based on the command line input.
     *
     * @return  JControllerBase
     *
     * @since   1.0
     * @throws  InvalidArgumentException
     */
    protected function fetchModel()
    {
        $base = 'ECR_CLASS_PREFIXModel';

        $sub = $this->input->get('view') ? : strtolower($this->do);

        $className = $base.ucfirst($sub);

        // If the requested controller exists let's use it.
        if(class_exists($className))
            return new $className;

        // Nothing found. Return the default model.
        return new ECR_CLASS_PREFIXModelDefault(new JRegistry);
    }

    /**
     * @param JModelBase $model
     *
     * @return JViewHtml
     */
    protected function fetchView(JModelBase $model)
    {
        $name = $this->input->get('view') ? : $this->do;

        $className = 'ECR_CLASS_PREFIXView'.ucfirst($name).'View';

        if( ! class_exists($className))
        {
            $className = 'ECR_CLASS_PREFIXViewDefaultView';

            $layouts = new SplPriorityQueue;
            $layouts->insert(JPATH_BASE.'/view/default/tmpl', 0);
        }
        else
        {
            $layouts = new SplPriorityQueue;
            $layouts->insert(JPATH_BASE.'/view/'.$name.'/tmpl', 0);
        }

        return new $className($model, $layouts);
    }

    /**
     * @return object
     */
    public function getConfig()
    {
        return $this->config->toObject();
    }

    /**
     * Method to create a database driver for the Web application.
     *
     * @return void
     *
     * @since 1.0
     */
    protected function loadDatabase()
    {
        $database = ('sqlite' == $this->get('db_driver'))
            ? APP_PATH_DATA.'/'.$this->get('db_name')
            : $this->get('db_name');

        $this->db = JDatabaseDriver::getInstance(
            array(
                'driver' => $this->get('db_driver'),
                'host' => $this->get('db_host'),
                'user' => $this->get('db_user'),
                'password' => $this->get('db_pass'),
                'database' => $database,
                'prefix' => $this->get('db_prefix')
            )
        );

        // Select the database.
        if('sqlite' != $this->get('db_driver'))
            $this->db->select($this->get('db_name'));

        // Set the debug flag.
        $this->db->setDebug($this->get('debug'));

        // Set the database to our static cache.
        JFactory::$database = $this->db;
    }
}
