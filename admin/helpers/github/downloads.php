<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * GitHub downloads class.
 *
 * @link http://developer.github.com/v3/repos/downloads/
 */
class EcrGithubDownloads extends JGithubObject
{
    /**
     * Method to list downloads.
     *
     * @param   string   $user   The name of the owner of the GitHub repository.
     * @param   string   $repo   The name of the GitHub repository.
     * @param   integer  $page   The page number from which to get items.
     * @param   integer  $limit  The number of items on a page.
     *
     * @throws DomainException
     *
     * @return  array of EcrGithubResponseDownloadsGet objects.
     *
     * @since    11.3
     */
    public function getList($user, $repo, $page = 0, $limit = 0)
    {
        // Build the request path.
        $path = '/repos/'.$user.'/'.$repo.'/downloads';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path, $page, $limit));

        // Validate the response code.
        if(200 != $response->code)
        {
            // Decode the error response and throw an exception.
            $error = json_decode($response->body);

            throw new DomainException($error->message, $response->code);
        }

        return json_decode($response->body);
    }

    /**
     * Add a download.
     *
     * @param   string   $user   The name of the owner of the GitHub repository.
     * @param   string   $repo   The name of the GitHub repository.
     * @param string     $path
     * @param string     $description
     *
     * @throws Exception
     * @throws DomainException
     * @return mixed
     *
     */
    public function add($user, $repo, $path, $description = '')
    {
        /*
         * First part: Create the download resource
         */

        // Build the request data.
        $fileName = JFile::getName($path);

        $data = json_encode(
            array(
                'name' => $fileName,
                'size' => filesize($path),
                'description' => $description,
            )
        );

        // Build the request path.
        $repoPath = '/repos/'.$user.'/'.$repo.'/downloads';

        // Send the request.
        $response = $this->client->post($this->fetchUrl($repoPath), $data);

        // Validate the response code.
        if(201 != $response->code)
        {
            // Decode the error response and throw an exception.
            $error = json_decode($response->body);

            throw new DomainException($error->message, $response->code);
        }

        /*
         * Second part: Upload the file
         *
         * For the second part we use plain curl - JHttp seems to add some unnecessary stuff...
         */

        $respData = json_decode($response->body);

        if( ! $respData)
            throw new Exception('Invalid response');

        $data = array(
            'key' => $respData->path,
            'acl' => $respData->acl,
            'success_action_status' => 201,
            'Filename' => $respData->name,
            'AWSAccessKeyId' => $respData->accesskeyid,
            'Policy' => $respData->policy,
            'Signature' => $respData->signature,
            'Content-Type' => $respData->mime_type,
            'file' => '@'.$path,
        );

        $ch = curl_init();

        $curlOptions = array(
            CURLOPT_URL => $respData->s3_url
        , CURLOPT_POST => true
        , CURLOPT_POSTFIELDS => $data
        , CURLOPT_RETURNTRANSFER => true
        , CURLOPT_HEADER, true
        );

        curl_setopt_array($ch, $curlOptions);

        $result = curl_exec($ch);

        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if(201 != $responseCode)
        {
            throw new DomainException($result, $responseCode);
        }

        /*
        $this->options->set('api.url', $respData->s3_url);

        // Unset credentials
        $this->options->set('api.username', '');
        $this->options->set('api.password', '');

        $headers = array(
            //          'Expires' => time() + 300,
        );

        $response = $this->client->post($this->fetchUrl(''), $data, $headers);

        // Validate the response code.
        if(201 != $response->code)
        {
            // Decode the error response and throw an exception.
            throw new DomainException($response->body, $response->code);
        }

        return json_decode($response->body);
        */
        return $this;
    }

    /**
     * Delete a download.
     *
     * @param   string   $user  The name of the owner of the GitHub repository.
     * @param   string   $repo  The name of the GitHub repository.
     * @param   int      $id    The id of the download to delete.
     *
     * @return mixed
     *
     * @throws DomainException
     */
    public function delete($user, $repo, $id)
    {
        // Build the request path.
        $path = '/repos/'.$user.'/'.$repo.'/downloads/'.$id;

        // Send the request.
        $response = $this->client->delete($this->fetchUrl($path));

        // Validate the response code.
        if(204 != $response->code)
        {
            // Decode the error response and throw an exception.
            $error = json_decode($response->body);

            throw new DomainException($error->message, $response->code);
        }

        return json_decode($response->body);
    }
}
