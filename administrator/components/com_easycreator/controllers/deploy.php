<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 20-Apr-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerDeploy extends JControllerLegacy
{
    private $response = array('status' => 0, 'message' => '', 'debug' => '');

    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *                          Recognized key values include 'name', 'default_task', 'model_path', and
     * 'view_path' (this list is not meant to be comprehensive).
     *
     * @since   11.1
     */
    public function __construct($config = array())
    {
        $this->input = JFactory::getApplication()->input;

        $this->response = new stdClass;
        $this->response->status = 0;
        $this->response->message = '';
        $this->response->debug = '';

        parent::__construct($config);
    }

    /**
     * Standard display method.
     *
     * @param bool       $cachable  If true, the view output will be cached
     * @param array|bool $urlparams An array of safe url parameters and their variable types,
     *                              for valid values see {@link JFilterInput::clean()}.
     *
     * @return \JController|void
     */
    public function display($cachable = false, $urlparams = false)
    {
        JFactory::getApplication()->input->set('view', 'deploy');

        parent::display($cachable, $urlparams);
    }

    /**
     * @throws Exception
     */
    public function getPackageList()
    {
        ob_start();

        try
        {
            $downloads = EcrDeployer::getInstance()->getPackageList();

            $deployTarget = JFactory::getApplication()->input->get('deployTarget');

            $html = array();

            if( ! count($downloads))
            {
                $html[] = '<div class="warning">'.jgettext('No downloads found').'</div>';
            }
            else
            {
                $html[] = '<ul class="packageList">';

                /* @var EcrGithubResponseDownloadsGet $download */
                foreach($downloads as $download)
                {
                    $html[] = '<li>';

                    $html[] = ($download->html_url)
                        ? '<a href="'.$download->html_url.'">'.$download->name.'</a>'
                        : $download->name;

                    $html[] = '<div class="actions">';

                    $html[] = '<a href="javascript:;" style="color: red;"'
                        .'onclick="EcrDeploy.deletePackage(\''.$deployTarget.'\', \''.$download->fileName.'\');">'
                        .jgettext('Delete')
                        .'</a>';

                    $html[] = '</div>';

                    $html[] = '</li>';
                }

                $html[] = '</ul>';
            }

            $this->response->message = implode("\n", $html);
        }
        catch(Exception $e)
        {
            $this->handleException($e);
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response->status = 1;
            $this->response->debug .= $buffer;
        }

        echo json_encode($this->response);

        jexit();
    }

    /**
     *
     */
    public function deletePackage()
    {
        ob_start();

        try
        {
            EcrDeployer::getInstance()->deletePackage();

            $this->response->message = jgettext('The package has been deleted.');
        }
        catch(Exception $e)
        {
            $this->handleException($e);
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response->status = 1;
            $this->response->debug .= $buffer;
        }

        echo json_encode($this->response);

        jexit();
    }

    /**
     *
     */
    public function deployFiles()
    {
        ob_start();

        try
        {
            EcrDeployer::getInstance()->deployFiles();

            $this->response->message = jgettext('The files have been deployed.');
        }
        catch(Exception $e)
        {
            $this->handleException($e);
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response->status = 1;
            $this->response->debug .= $buffer;
        }

        sleep(1);

        echo json_encode($this->response);

        jexit();
    }

    /**
     *
     */
    public function deployPackages()
    {
        ob_start();

        try
        {
            $count = EcrDeployer::getInstance()->deployPackage();

            $this->response->message = sprintf(
                jngettext(
                    'The file has been deployed.'
                    , '%d files have been deployed.'
                    , $count)
                , $count);
        }
        catch(Exception $e)
        {
            $this->handleException($e);
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response->status = 1;
            $this->response->debug .= $buffer;
        }

        echo json_encode($this->response);

        jexit();
    }

    /**
     * @param array          $list
     *
     * @return string
     */
    private function generateTree($list)
    {
        $converteds = array();

        foreach($list as $item)
        {
            $parts = explode('/', $item->path);

            array_pop($parts);

            eval('$converteds[\''.implode("']['", $parts).'\'][]=$item;');
        }

        return '<div id="syncTree">'
            .'<div class="root">JROOT</div>'
            .'<ul>'.$this->processTree($converteds).'</ul>'
            .'</div>';
    }

    /**
     * @param $list
     *
     * @return string
     */
    private function processTree($list)
    {
        static $tree = array();

        foreach($list as $k => $item)
        {
            if(is_array($item))
            {
                ksort($item);
                uksort($item, array($this, 'sort'));

                $tree [] = '<li>';

                $tree[] = '<div class="pft-directory">'.$k.'</div>';
                $tree[] = '<ul>';

                $this->processTree($item);
                $tree[] = '</ul>';
                $tree[] = '</li>';

                continue;
            }

            $f = JFile::getName($item->path);

            $tree[] = '<li><div class="file '.$item->status.'">'
                .'<input type="checkbox" value="'.$item->status.'" id="'.$item->path.'" />'
                .'<label class="pft-file '.$item->status.'" for="'.$item->path.'">'.$f.'</label>'
                .'</div></li>';
        }

        return implode("\n", $tree);
    }

    /**
     * @param $a
     * @param $b
     *
     * @return int
     */
    public function sort($a, $b)
    {
        if(is_int($a) && ! is_int($b))
        {
            return 1;
        }

        if(is_int($b) && ! is_int($a))
        {
            return - 1;
        }

        return 0;
    }

    /**
     *
     */
    public function getSyncList()
    {
        ob_start();

        try
        {
            $list = EcrDeployer::getInstance()->getSyncList();

            $html = array();

            $html[] = (count($list))
                ? self::generateTree($list)
                : '<div class="allInSync">'.jgettext('All files are synchronized').'</div>';

            $this->response->message = implode("\n", $html);
        }
        catch(Exception $e)
        {
            $this->handleException($e);
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response->status = 1;
            $this->response->debug .= $buffer;
        }

        echo json_encode($this->response);

        jexit();
    }

    /**
     *
     */
    public function syncFiles()
    {
        ob_start();

        try
        {
            EcrDeployer::getInstance()->syncFiles();

            $this->response->message = jgettext('The files have been synchronized.');
        }
        catch(Exception $e)
        {
            $this->handleException($e);
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response->status = 1;
            $this->response->debug .= $buffer;
        }

        echo json_encode($this->response);

        jexit();
    }

    /**
     * @param Exception $e
     */
    private function handleException(Exception $e)
    {
        JLog::add('| XX '.$e->getMessage(), JLog::ERROR);
        JLog::add('|___ :(', JLog::ERROR);
        JLog::add('', JLog::ERROR);

        $this->response->debug = (ECR_DEBUG) ? nl2br($e) : '';
        $this->response->message = $e->getMessage();
        $this->response->status = 1;

        sleep(1);
    }
}
