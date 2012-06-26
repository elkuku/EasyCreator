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
class EcrProjectActionDropbox extends EcrProjectAction
{
    protected $type = 'dropbox';

    protected $name = 'Dropbox';

    public $dboxKey = '';

    public $dboxSecret = '';

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
        $js = array();

        JFactory::getDocument()->addScriptDeclaration(implode("\n", $js));

        $html[] = $this->getLabel($cnt, 'dboxKey', jgettext('Dropbox Key'));
        $html[] = $this->getInput($cnt, 'dboxKey', $this->dboxKey);
        $html[] = '<br />';
        $html[] = $this->getLabel($cnt, 'dboxSecret', jgettext('Dropbox Secret'));
        $html[] = $this->getInput($cnt, 'dboxSecret', $this->dboxSecret);
        $html[] = '<br />';

        $html[] = $this->getLabel($cnt, '', jgettext('Abort on failure'));
        $html[] = EcrHtmlSelect::yesno('fields['.$cnt.'][abort]', $this->abort);
        $html[] = '<br />';

        $link = JRoute::_('&option=com_easycreator&controller=stuffer&task=checkdropbox'
            .'&key='.$this->dboxKey.'&seqret='.$this->dboxSecret);

        $html[] = '<strong>Note</strong> Be sure to <a target="_blank" href="'.$link.'">Register the app</a>';

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
        $logger = $ziper->logger;

        //-- Require the Dropbox bootstrap
        require JPATH_COMPONENT.'/helpers/Dropbox/bootstrap.php';

        $protocol = (false == empty($_SERVER['HTTPS'])) ? 'https' : 'http';

        $callback = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        try
        {
            //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
            $encrypter = new \Dropbox\OAuth\Storage\Encrypter('12312323423435456654457545646542');
            $storage = new \Dropbox\OAuth\Storage\Session($encrypter);
            $OAuth = new \Dropbox\OAuth\Consumer\Curl($this->dboxKey, $this->dboxSecret, $storage, $callback);
            $dropbox = new \Dropbox\API($OAuth);

            //$accountInfo = $dropbox->accountInfo();

            //$logger->log('Dropbox account info:'.print_r($accountInfo, 1));

            $files = $ziper->getCreatedFiles();

            /* @var EcrProjectZiperCreatedfile $file */
            foreach($files as $i => $file)
            {
                $logger->log(sprintf('Uploading %s to Dropbox...', $file->name));

                $response = $dropbox->putFile($file->path);

                if('200' !== $response['code'])
                {
                    $logger->log(print_r($response, 1));

                    $this->abort('Failed to upload the file to Dropbox', $ziper);
                }

                $response = $dropbox->shares($file->name);

                if('200' !== $response['code'])
                {
                    $this->abort('Can not get the Dropbox share link', $ziper);

                    $logger->log(print_r($response['code'], 1));
                }

                $link = $response['body']->url;

                $ziper->setAlternateDownloadLink($i, $link);

                $logger->log('Copy the Link: '.$link, '', JLog::WARNING);
            }

            $logger->log('Dropbox script executed succesfully.');
        }
        catch(Dropbox\Exception $e)
        {
            $this->abort(
                sprintf('Dropbox: %s', $e->getMessage())
                , $ziper);
        }
        catch(Exception $e)
        {
            $this->abort(
                sprintf('Dropbox action: %s', $e->getMessage())
                , $ziper);
        }

        return $this;
    }
}
