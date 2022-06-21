<?php

namespace app\services\googledrive;

use Exception;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use GuzzleHttp\Client as GuzzleClient;

class GoogleDrive
{
    private $credentialsFileName;
    private $appName;
    private $service;
    public function __construct($credentialsFileName, $appName)
    {
        $this->credentialsFileName = $credentialsFileName;
        $this->appName = $appName;

        $client = $this->getClientServiseCredentials();

        $this->service = new Drive($client);
    }
    //debugger
    private function debug(...$msgs)
    {
        echo implode(" ", $msgs) . "\n";
    }

    // Client for oAuth2 auth
    public function getClient()
    {
        $client = new Client();
        $client->setApplicationName($this->appName);
        // $client->setScopes('https://www.googleapis.com/auth/drive.metadata.readonly');
        $client->setScopes('https://www.googleapis.com/auth/drive');
        $client->setAuthConfig($this->credentialsFileName);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = 'google_drive_token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        try {


            if ($client->isAccessTokenExpired()) {
                // Refresh the token if possible, else fetch a new one.
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                } else {
                    // Request authorization from the user.
                    $authUrl = $client->createAuthUrl();
                    printf("Open the following link in your browser:\n%s\n", $authUrl);
                    print 'Enter verification code: ';
                    $authCode = trim(fgets(STDIN));

                    // Exchange authorization code for an access token.
                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    $client->setAccessToken($accessToken);

                    // Check to see if there was an error.
                    if (array_key_exists('error', $accessToken)) {
                        throw new Exception(join(', ', $accessToken));
                    }
                }
                // Save the token to a file.
                if (!file_exists(dirname($tokenPath))) {
                    mkdir(dirname($tokenPath), 0700, true);
                }
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            }
        } catch (Exception $e) {
            // TODO(developer) - handle error appropriately
            echo 'Some error occured: ' . $e->getMessage();
        }
        return $client;
    }

    // Client for service-account auth
    public function getClientServiseCredentials()
    {
        $client = new Client();
        $client->setAuthConfig($this->credentialsFileName);
        $client->setApplicationName("MysqlBackup");
        $client->addScope(Drive::DRIVE);
        $httpClient = new GuzzleClient([
            'proxy' => 'localhost:8888', // by default, Charles runs on localhost port 8888
            'verify' => false, // otherwise HTTPS requests will fail.
        ]);
        $client->setHttpClient($httpClient);
        $this->debug("get client");
        return $client;
    }

    public function createFile($fullpath, $filename, $parent_folder_id = null,  $desc, $mimeType, $uploadType = "multipart")
    {
        $this->debug("create file:", $filename);

        $data = file_get_contents($fullpath);

        if (!$data) {
            throw new FileNotFoundException("File not found");
        }

        $file = new DriveFile();

        $file->setName($filename);
        $file->setDescription($desc);
        $file->setMimeType($mimeType);

        if ($parent_folder_id) {
            $file->setParents([$parent_folder_id]);
        }
        $result = $this->service->files->create($file, [
            'data' => $data,
            'mimeType' => $mimeType,
            'uploadType' => $uploadType
        ]);

        $file_id = null;

        if (isset($result['id']) && !empty($result['id'])) {
            $file_id = $result['id'];
        }
        $this->debug("uploaded file ID:", $file_id);
        return $file_id;
    }

    public function createFolder($folderName, $parentFolderId = null)
    {
        $this->debug("create folder:", $folderName);
        $folderList = $this->checkFolderExist($folderName);

        if (count($folderList) == 0) {
            $folder = new DriveFile();

            $folder->setName($folderName);
            $folder->setMimeType('application/vnd.google-apps.folder');

            if ($parentFolderId) {
                $folder->setParents([$parentFolderId]);
            }

            $result = $this->service->files->create($folder);

            $folder_id = null;

            if (isset($result['id']) && !empty($result['id'])) {
                $folder_id = $result['id'];
            }

            return $folder_id;
        }

        return $folderList[0]['id'];
    }
    private function printAllFiles()
    {
        $this->debug("print all folders");
        $params = [
            'q' =>  "mimeType='text/plain' and trashed=false"
        ];

        $files = $this->service->files->listFiles($params);
        $op = [];

        foreach ($files as $file) {
            $op[] = $file;
            $this->debug("drive folder:", $file->name, "|", $file->id);
        }
        return $op;
    }
    private function checkFolderExist($folderName)
    {
        $this->debug("check folder exist:", $folderName);
        $params = [
            'q' =>  "mimeType='application/vnd.google-apps.folder' and name='$folderName' and trashed=false"
        ];

        $files = $this->service->files->listFiles($params);
        $op = [];

        foreach ($files as $file) {
            $op[] = $file;
        }
        return $op;
    }
}
