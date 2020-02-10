<?php
/*-------------------------------------------------------+
| SYSTOPIA OnlyOffice Integration                        |
| Copyright (C) 2019-2020 SYSTOPIA                       |
| Author: B. Zschiedrich (zschiedrich@systopia.de)       |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*/

/*
 * Class for handling API calls to the OnlyOffice server.
 */
class CRM_Onlyoffice_ApiHandler
{
  private $baseUrl; //Guaranteed to end with a slash.
  private $token;

  public const PrivateFolderId = '@my';
  public const CommonFolderId = '@common';
  public const SharedFolderId = '@share';

  /**
   * Generates base URL for API usage based on a given server domain.
   * @param string $url The URL.
   */
  public function setBaseUrl(string $url): void
  {
    // TODO: Test for empty base URL setting and give out error.
    if (mb_substr($url, -1) != '/')
    {
      $url .= '/';
    }

    $url .= 'api/2.0/';
    $this->baseUrl = $url;
  }

  /**
   * Authenticates on server via username and password to get a token.
   * @param string $name The user name.
   * @param string $password The password for the given user name.
   */
  public function authenticate(string $name, string $password): void
  {
    $data = [
      'userName' => $name,
      'password' => $password
    ];
    $result = $this->makePostRequestAsJson('authentication', $data);
    $this->token = $result->response->token;

    // TODO: Test for returned status code.
    // TODO: Save token in settings and only renew it when necessary (see response->expires) or not working.
  }

  /**
   * Extracts the session cookies from the API for website wide use.
   * @return string A cookie string containing all needed session cookies.
   */
  public function getSessionCookies(): string
  {
    $headerArray = $this->getRequestHeader('capabilities');

    $headerString = implode("\n", $headerArray);

    preg_match_all('/Set-Cookie: (.*)\b/', $headerString, $rawCookies);

    $rawCookies = $rawCookies[1]; //We only need the part after "Set-Cookie".

    $cookies = [];
    foreach ($rawCookies as $rawCookie)
    {
      $bakedCookie = explode('; ', $rawCookie);
      $cookies[] = $bakedCookie[0]; //Actual cookie is the first entry.
    }

    $sessionCookies = implode('; ', $cookies);

    return $sessionCookies;
  }

  /**
   * List everything (files, folders, extra information) inside a given folder.
   * @param string $folderId The ID of the folder to look in.
   * @return object The response object containing all given information.
   */
  public function listAll(string $folderId): object
  {
    $result = $this->makeGetRequest('files/' . $folderId);

    return $result->response;

    // TODO: Test for returned status code.
  }

  /**
   * List all files inside a given folder.
   * @param string $folderId The ID of the files' folder.
   * @return array An array with multiple objects describing the files.
   */
  public function listFiles(string $folderId): array
  {
    $all = $this->listAll($folderId);

    return $all->files;

    // TODO: Test for returned status code.
  }

  /**
   * List all folders inside a given folder.
   * @param string $folderId The ID of the folder to look in.
   * @return array An array with multiple objects describing the folders.
   */
  public function listFolders(string $folderId): array
  {
    $all = $this->listAll($folderId);

    return $all->folders;

    // TODO: Test for returned status code.
  }

  /**
   * Get all available file information.
   * @param string $fileId The ID of the file to get the information for.
   * @return object The information.
   */
  public function getFileInformation(string $fileId): object
  {
    $result = $this->makeGetRequest('files/file/' . $fileId);

    return $result->response;

    // TODO: Test for returned status code.
  }

  /**
   * Uploads a DocX file to the user space.
   * @param string $fileName The name of the file including the file extension.
   * @param string $file The file as string to be uploaded.
   * @return object The response containing file info.
   */
  public function uploadDocx(string $fileName, string $file): object
  {
    $result = $this->makePostRequestAsDocx('files/@my/upload', $fileName, $file);

    return $result->response;

    // TODO: Test for returned status code.
  }

  /**
   * Deletes a file from the user space.
   * @param string $fileId The unique identifier for the file.
   * @return object The response containing deletion info.
   */
  public function deleteFile(string $fileId): object
  {
    $data = [
      'fileId' => $fileId,
      'deleteAfter' => false, //Don't delete after request is finished.
      'immediately' => true //Don't move to recycle bin.
    ];

    $result = $this->makeDeleteRequest('files/file/' . $fileId, $data);

    return $result;

    // TODO: Test for returned status code.
  }

  /**
   * Makes a GET request to the API without custom data.
   * @param string $method The target method of the API.
   * @return object A JSON decoded object of the returned data.
   */
  private function makeGetRequest(string $method): object
  {
    $options = [
      'http' => [
        'method' => 'GET',
        'header'=> "Accept: application/json\r\n" .
                   'Authorization:' . $this->token . "\r\n"
      ]
    ];

    $url = $this->baseUrl . $method;

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return json_decode($result);

    // TODO: Test for returned status code?
  }

  /**
   * Makes a POST request to the API with JSON encoded custom data.
   * @param string $method The target method of the API.
   * @param array $data The body content for the request.
   * @return object A JSON decoded object of the returned data.
   */
  private function makePostRequestAsJson(string $method, array $data): object
  {
    $header = "Content-Type: application/json\r\n" .
              "Accept: application/json\r\n";

    $jsonData = json_encode($data);

    $result = $this->makeRawRequest($method, $header, $jsonData);

    return json_decode($result);

    // TODO: Test for returned status code?
  }

  /**
   * Makes a POST request to the API with a DocX file as payload.
   * @param string $method The target method of the API.
   * @param string $fileName The name of the file including the file extension.
   * @param string $file The file as string to be uploaded.
   * @return object The full decoded response body as string.
   */
  private function makePostRequestAsDocx(string $method, string $fileName, string $file): object
  {
    $header = 'Content-Disposition: inline; filename="' . $fileName . '"' . "\r\n" .
      'Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document' . "\r\n" .
      "Accept: application/json\r\n";

    $result = $this->makeRawRequest($method, $header, $file);

    return json_decode($result);

    // TODO: Test for returned status code?
  }

  /**
   * Makes a DELETE request to the API with JSON encoded custom data.
   * @param string $method The target method of the API.
   * @param array $data The body content for the request.
   * @return object A JSON decoded object of the returned data.
   */
  private function makeDeleteRequest(string $method, array $data): object
  {
    $header = "Content-Type: application/json\r\n" .
      "Accept: application/json\r\n";

    $jsonData = json_encode($data);

    $result = $this->makeRawRequest($method, $header, $jsonData, true);

    return json_decode($result);

    // TODO: Test for returned status code?
  }

  /**
   * Makes a POST/DELETE request to the API with custom header and data.
   * @param string $method The target method of the API.
   * @param string $header The header for the request.
   * @param string $data The body content for the request.
   * @param bool $isDelete If true, the request type will be DELETE instead of POST.
   * @return string|false The response body.
   */
  private function makeRawRequest(string $method, string $header, string $data, bool $isDelete = false)
  {
    $options = [
      'http' => [
        'method' => $isDelete ? 'DELETE' : 'POST',
        'content' => $data,
        'header'=> 'Authorization:' . $this->token . "\r\n" . $header
      ]
    ];

    $url = $this->baseUrl . $method;

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;

    // TODO: Test for returned status code?
  }

  /**
   * Gets the response header from a GET request to the API.
   * @param string $method The target method of the API.
   * @return array The unformatted header.
   */
  private function getRequestHeader(string $method): array
  {
    $options = [
      'http' => [
        'method' => 'GET',
        'header'=> "Accept: application/json\r\n" .
          'Authorization:' . $this->token . "\r\n"
      ]
    ];

    $url = $this->baseUrl . $method;

    $context  = stream_context_create($options);
    $header = get_headers($url, 0, $context);

    return $header;

    // TODO: Test for returned status code?
  }
}
