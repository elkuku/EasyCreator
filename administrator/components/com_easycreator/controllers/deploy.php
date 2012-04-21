<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 20-Apr-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

jimport('joomla.application.component.controller');

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerDeploy extends JController
{
    private $response = array('status' => 0, 'message' => '', 'debug' => '');

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
        $ecr_project = JRequest::getCmd('ecr_project');

        if(! $ecr_project)
        {
            //-- NO PROJECT SELECTED - ABORT
            EcrHtml::easyFormEnd();

            return;
        }

        JRequest::setVar('view', 'deploy');

        parent::display($cachable, $urlparams);
    }

    /**
     *
     */
    public function getFtpDownloads()
    {
        ob_start();

        try
        {
            $downloads = EcrDeployer::getDownloads();

            $html = array();

            $html[] = '<ul>';

            /* @var EcrGithubResponseDownloadsGet $download */
            foreach($downloads as $download)
            {
                $download = JArrayHelper::toObject($download);
                $html[] = '<li>';
                $html[] = '<a href="'.$download->name.'">'.$download->name.'</a>';
                $html[] = '<a href="javascript:;" onclick="EcrZiper.deleteDownload(\'ftp\', \''.$download->name.'\');">'
                    .jgettext('Delete')
                    .'</a>';
                $html[] = '</li>';
            }

            $html[] = '</ul>';

            $this->response['message'] = implode("\n", $html);
        }
        catch(Exception $e)
        {
            JLog::add($e->getMessage(), JLog::ERROR);

            $this->response['debug'] = (ECR_DEBUG) ? nl2br($e) : '';
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = 1;
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response['status'] = 1;
            $this->response['debug'] .= $buffer;
        }

        echo json_encode($this->response);
    }

    /**
     *
     */
    public function getGitHubDownloads()
    {
        ob_start();

        try
        {
            $downloads = EcrDeployer::getDownloads();

            $html = array();

            $html[] = '<ul>';

            /* @var EcrGithubResponseDownloadsGet $download */
            foreach($downloads as $download)
            {
                $html[] = '<li>';
                $html[] = '<a href="'.$download->html_url.'">'.$download->name.'</a>';
                $html[] = '<a href="javascript:;" onclick="EcrZiper.deleteDownload(\'github\', \''.$download->id.'\');">'
                    .jgettext('Delete')
                    .'</a>';
                $html[] = '</li>';
            }

            $html[] = '</ul>';

            $this->response['message'] = implode("\n", $html);
        }
        catch(Exception $e)
        {
            JLog::add($e->getMessage(), JLog::ERROR);

            $this->response['debug'] = (ECR_DEBUG) ? nl2br($e) : '';
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = 1;
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response['status'] = 1;
            $this->response['debug'] .= $buffer;
        }

        echo json_encode($this->response);
    }

    /**
     *
     */
    public function deployFiles()
    {
        ob_start();

        try
        {
            EcrDeployer::deployFiles();

            $this->response['message'] = jgettext('The files have been deployed.');
        }
        catch(Exception $e)
        {
            JLog::add($e->getMessage(), JLog::ERROR);

            $this->response['debug'] = (ECR_DEBUG) ? nl2br($e) : '';
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = 1;
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response['status'] = 1;
            $this->response['debug'] .= $buffer;
        }

        sleep(1);

        echo json_encode($this->response);
    }

    /**
     *
     */
    public function deployPackages()
    {
        jimport('joomla.filesystem.file');

        ob_start();

        try
        {
            EcrDeployer::deployPackage();

            $this->response['message'] = jgettext('The files have been deployed.');
        }
        catch(Exception $e)
        {
            JLog::add($e->getMessage(), JLog::ERROR);

            $this->response['debug'] = (ECR_DEBUG) ? nl2br($e) : '';
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = 1;
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response['status'] = 1;
            $this->response['debug'] .= $buffer;
        }

        echo json_encode($this->response);
    }

    /**
     * @param $list
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

        return '<div id="syncTree">'.$this->processTree($converteds).'</div>';
    }

    /**
     * @param $list
     * @return string
     */
    private function processTree($list)
    {
        static $tree = array();

        static $level = 0;

        foreach($list as $k => $item)
        {
            if(is_array($item))
            {
                ksort($item);
                uksort($item, array($this, 'sort'));

                $tree[] = '<div class="pft-directory">'.str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level).$k.'</div>';

                $level ++;

                $this->processTree($item);

                $level --;

                continue;
            }

            $f = JFile::getName($item->path);
            $status = ($item->exists) ? ' changed' : ' new';

            $tree[] = '<div class="file'.$status.'">'.str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level)
                .'<input type="checkbox" value="'.$item->path.'" id="'.$item->path.'" />'
                .'<label class="pft-file'.$status.'" for="'.$item->path.'">'.$f.'</label>'
                .'</div>';
        }

        return implode("\n", $tree);
    }

    /**
     * @param $a
     * @param $b
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
            return -1;
        }
    }

    /**
     *
     */
    public function getSyncList()
    {
        ob_start();

        try
        {
            $project = EcrProjectHelper::getProject();

            $list = EcrDeployer::getSyncList($project);

            $html = array();

            if(count($list))
            {
                $html[] = self::generateTree($list);

//$html[] = '<pre>'.print_r($llll, 1).'</pre>';
                /*

                $html[] = '<ul class="syncList">';

                foreach($list as $item)
                {
                    $status = ($item->exists) ? 'changed' : 'new';

                    $html[] = '<li class="'.$status.'">';
                    $html[] = '<input type="checkbox" name="file[]" id="'.$item->path.'" value="'.$item->path.'" />';
                    $html[] = '<label for="'.$item->path.'">'.$item->path.'</label>';
                    $html[] = '</li>';
                }

                $html[] = '</ul>';
                */
            }
            else
            {
                $html[] = '<div class="allInSync">'.jgettext('All files are synchronized').'</div>';
            }

            $this->response['message'] = implode("\n", $html);
        }
        catch(Exception $e)
        {
            JLog::add($e->getMessage(), JLog::ERROR);

            $this->response['debug'] = (ECR_DEBUG) ? nl2br($e) : '';
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = 1;
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response['status'] = 1;
            $this->response['debug'] .= $buffer;
        }

        echo json_encode($this->response);

    }

    public function syncFiles()
    {
        jimport('joomla.filesystem.file');

        ob_start();

        try
        {
            EcrDeployer::syncFiles();

            $this->response['message'] = jgettext('The files have been synchronized.');
        }
        catch(Exception $e)
        {
            JLog::add($e->getMessage(), JLog::ERROR);

            sleep(1);

            $this->response['debug'] = (ECR_DEBUG) ? nl2br($e) : '';
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = 1;
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response['status'] = 1;
            $this->response['debug'] .= $buffer;
        }

        echo json_encode($this->response);
    }

    public function pollLog()
    {
        $path = JFactory::getConfig()->get('log_path').'/ecr_deploy.php';

        if(JFile::exists($path))
        {
            $s = JFile::read($path);

            $s .= "\n".'Time '.date('H:i:s');

            $this->response['message'] = $s;
        }

        echo json_encode($this->response);
    }

}
