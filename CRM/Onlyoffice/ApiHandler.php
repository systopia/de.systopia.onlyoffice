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

  private $baseUrl;
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
    $data = array(
      'userName' => $name,
      'password' => $password
    );
    $result = $this->postRequest('authentication', $data);
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
   * @return An array with multiple objects descriping the files.
   */
  public function files() {
    $result = $this->getRequest('files/@my');

    return $result->response->files;

    // TODO: Test for returned status code.
  }

  /**
   * Make a GET request to the API without custom data.
   * @return A JSON decoded object of the returned data.
   */
  private function getRequest($method) {
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
  }

  /**
   * Make a POST request to the API with JSON encoded custom data.
   * @return A JSON decoded object of the returned data.
   */
  private function postRequest($method, $data) {
    $options = array(
      'http' => array(
        'method' => 'POST',
        'content' => json_encode($data),
        'header'=> "Content-Type: application/json\r\n" .
          "Accept: application/json\r\n" .
          'Authorization:' . $this->token . "\r\n"
      )
    );

    $url = $this->baseUrl . $method;

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return json_decode($result);
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
