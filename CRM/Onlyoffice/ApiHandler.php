<?php
/*-------------------------------------------------------+
| SYSTOPIA OnlyOffice Integration                        |
| Copyright (C) 2018 SYSTOPIA                            |
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
class CRM_Onlyoffice_ApiHandler {

  private $baseUrl; //Guaranteed to end with a slash.
  private $token;

  /**
   * Generates base URL for API usage based on a given server domain.
   */
  public function setBaseUrl($url) {
    // TODO: Test for empty base URL setting and give out error.
    if (mb_substr($url, -1) != '/')
      $url .= '/';
    $url .= 'api/2.0/';
    $this->baseUrl = $url;
  }

  /**
   * Authenticates on server via username and password to get a token.
   */
  public function authenticate($name, $password) {
    $data = [
      'userName' => $name,
      'password' => $password
    ];
    $result = $this->makePostRequestAsJson('authentication', $data);
    $this->token = $result->response->token;

    // TODO: Test for returned status code.
    // TODO: Save token in settings and only renew it when necessary (see response->expires).
  }

  /**
   * Extracts the session cookies from the API for website wide use.
   * @return string A cookie string containing all needed session cookies.
   */
  public function getSessionCookies() {
    $headerArray = $this->getRequestHeader('capabilities');

    $headerString = implode("\n", $headerArray);

    preg_match_all('/Set-Cookie: (.*)\b/', $headerString, $rawCookies);

    $rawCookies = $rawCookies[1]; //We only need the part after "Set-Cookie".

    $cookies = [];
    foreach ($rawCookies as $rawCookie) {
      $bakedCookie = explode('; ', $rawCookie);
      $cookies[] = $bakedCookie[0]; //Actually cookie is the first entry.
    }

    $sessionCookies = implode('; ', $cookies);

    return $sessionCookies;
  }

  /**
   * List all files of the authenticated user.
   * @return array An array with multiple objects describing the files.
   */
  public function listFiles() {
    $result = $this->makeGetRequest('files/@my');

    return $result->response->files;

    // TODO: Test for returned status code.
  }

  /**
   * Makes a GET request to the API without custom data.
   * @param $method string The target method of the API.
   * @return object A JSON decoded object of the returned data.
   */
  private function makeGetRequest($method) {
    $options = array(
      'http' => array(
        'method' => 'GET',
        'header'=> "Accept: application/json\r\n" .
          'Authorization:' . $this->token . "\r\n"
      )
    );

    $url = $this->baseUrl . $method;

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return json_decode($result);

    // TODO: Test for returned status code?
  }

  /**
   * Makes a POST request to the API with JSON encoded custom data.
   * @param $method string The target method of the API.
   * @param $data string The body content for the request.
   * @return object A JSON decoded object of the returned data.
   */
  private function makePostRequestAsJson($method, $data) {
    $header = "Content-Type: application/json\r\n" .
              "Accept: application/json\r\n";

    $jsonData = json_encode($data);

    $result = $this->makeRawRequest($method, $header, $jsonData);

    return json_decode($result);

    // TODO: Test for returned status code?
  }

  private function makePostRequestAsDocx($method, $fileName, $file) {
    $header = 'Content-Disposition: inline; filename="' . $fileName . '"' . "\r\n" .
      'Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document' . "\r\n" .
      "Accept: application/json\r\n";

    $result = $this->makeRawRequest($method, $header, $file);

    return json_decode($result);

    // TODO: Test for returned status code?
  }

  /**
   * Makes a POST/DELETE request to the API with custom header and data.
   * @param $method string The target method of the API.
   * @param $header string The header for the request.
   * @param $data string The body content for the request.
   * @param $isDelete boolean If true, the request type will be DELETE instead of POST.
   * @return false|string The response body.
   */
  private function makeRawRequest($method, $header, $data, $isDelete = false) {
    $options = array(
      'http' => array(
        'method' => $isDelete ? 'DELETE' : 'POST',
        'content' => $data,
        'header'=> 'Authorization:' . $this->token . "\r\n" . $header
      )
    );

    $url = $this->baseUrl . $method;

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;

    // TODO: Test for returned status code?
  }

  private function getRequestHeader($method) {
    $options = array(
      'http' => array(
        'method' => 'GET',
        'header'=> "Accept: application/json\r\n" .
          'Authorization:' . $this->token . "\r\n"
      )
    );

    $url = $this->baseUrl . $method;

    $context  = stream_context_create($options);
    $header = get_headers($url, 0, $context);

    return $header;

    // TODO: Test for returned status code?
  }
}
