<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 17.04.12
 * Time: 13:37
 * To change this template use File | Settings | File Templates.
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
     * @param string $path
     * @param string $description
     *
     * @return mixed
     *
     * @throws DomainException
     */
    public function add($user, $repo, $path, $description = '')
    {
        $content_type = '';
        // Build the request data.
        $fileName = JFile::getName($path);

        $data = json_encode(
            array(
                'name' => $fileName,
                'size' => filesize($path),
                'description' => $description,
                'content_type' => $content_type,
                'file' => '@'.$path,
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

        $respData = json_decode($response->body);

        $data = json_encode(
            array(
                'key' => $respData->path,
                'acl' => $respData->acl,
                'success_action_status' => 201,
                'Filename' => $respData->name,
                'AWSAccessKeyId' => $respData->accesskeyid,
                'Policy' => $respData-> policy,
                'Signature' => $respData->signature,
                'Content-Type' => $respData->mime_type,
                'file' => '@'.$path
            )
        );

        $this->options->set('api.url', 'https://github.s3.amazonaws.com/');

        $response = $this->client->post($this->fetchUrl($repoPath), $data);

        // Validate the response code.
        if(201 != $response->code)
        {
            // Decode the error response and throw an exception.
            $error = json_decode($response->body);

            throw new DomainException($error->message, $response->code);
        }

        return json_decode($response->body);
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
