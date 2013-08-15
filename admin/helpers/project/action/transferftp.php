<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 22-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Custom action class.
 */
class EcrProjectActionTransferftp extends EcrProjectAction
{
    protected $type = 'transferftp';

    protected $name = 'Transfer FTP';

    protected $fixedEvent = 'postbuild';

    public $host = '';

    public $port = '21';

    public $user = '';

    public $pass = '';

    public $folder = '';

    public $files = '';

    public $abort = 0;

    /**
     * Get the input fields
     *
     * @param int $cnt A counter value.
     *
     * @return string
     */
    public function getFields($cnt)
    {
        $html = array();

        $html[] = $this->getLabel($cnt, 'host', jgettext('Host'));
        $html[] = $this->getInput($cnt, 'host', $this->host);
        $html[] = $this->getLabel($cnt, 'port', jgettext('Port'));
        $html[] = $this->getInput($cnt, 'port', $this->port, array('class' => 'span1'));
        $html[] = '<br />';
        $html[] = $this->getLabel($cnt, 'user', jgettext('User name'));
        $html[] = $this->getInput($cnt, 'user', $this->user);
        $html[] = '<br />';
        $html[] = $this->getLabel($cnt, 'pass', jgettext('Password'));
        $html[] = $this->getInput($cnt, 'pass', $this->pass);
        $html[] = '<br />';
        $html[] = $this->getLabel($cnt, 'folder', jgettext('Folder'));
        $html[] = $this->getInput($cnt, 'folder', $this->folder);
        $html[] = '<br />';
        $html[] = $this->getLabel($cnt, 'files', jgettext('Files'));
        $html[] = $this->getInput($cnt, 'files', $this->files);
        $html[] = '<br />';

        $html[] = $this->getLabel($cnt, '', jgettext('Abort on failure'));
        $html[] = EcrHtmlSelect::yesno('fields['.$cnt.'][abort]', $this->abort);

        return implode("\n", $html);
    }

    /**
     * Perform the action.
     *
     * @param EcrProjectZiper $ziper
     *
     * @return \EcrProjectAction
     */
    public function run(EcrProjectZiper $ziper)
    {
        $ziper->logger->log('Executing FTP transfer');

        $user = trim($this->user);
        $pass = trim($this->pass);

        if('' == $user)
        {
            $text = 'FTP User';

            $command = escapeshellcmd('DISPLAY=:0 XAUTHORITY=/home/elkuku/.Xauthority'
                .' kdialog --title "'.$text.'" --inputbox "'.$text.'"');

            $ziper->logger->log('Executing: '.$command);

            $user = shell_exec($command.' 2>&1');

            if('' == $user)
            {
                $ziper->logger->log('ERROR: No user name given', 'FTP user', JLog::ERROR);

                return $this;
            }
        }

        if('' == $pass)
        {
            $text = 'FTP Password';

            $command = escapeshellcmd('DISPLAY=:0 XAUTHORITY=/home/elkuku/.Xauthority'
                .' kdialog --title "'.$text.'" --inputbox "'.$text.'"');

            $ziper->logger->log('Executing: '.$command);

            $pass = shell_exec($command.' 2>&1');

            if('' == $pass)
            {
                $ziper->logger->log('ERROR: No password given', 'FTP user', JLog::ERROR);

                return $this;
            }
        }

        $ftp = EcrFtp::getClient($this->host, $this->port, null, $user, $pass);

        $files = explode(',', $this->files);

        $packages = $ziper->getCreatedFiles();

        foreach($files as $file)
        {
            $file = trim($file);

            $path = '';

            $fileName = '';

            switch($file)
            {
                case 'package' :
                    $path = $packages[0]->path;
                    break;

                default :
                    $ziper->logger->log('ERROR: Unknown file type: '.$file, 'FTP transfer', JLog::ERROR);
            }

            $remote = null;

            if($this->folder)
            {
                $remote = $this->folder.'/'.basename($path);
            }

            if(false == $ftp->store($path, $remote))
            {
                //@todo: deprecated JError
                $error = JError::getError();

                $ziper->logger->log('ERROR: '.$error, 'FTP transfer (JFTP)', JLog::ERROR);
            }
            else
            {
                $ziper->logger->log('File transfered: '.$fileName);
            }
        }

        return $this;
    }
}
