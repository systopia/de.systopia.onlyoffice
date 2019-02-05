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
 * Class for handling (cookie based) website calls to the OnlyOffice server.
 */
class CRM_Onlyoffice_WebsiteHandler {

  private $baseUrl; //Guaranteed to end with a slash.
  private $sessionCookies = '';

  /**
   * Generates base URL for API usage based on a given server domain.
   */
  public function setBaseUrl($url) {
    // TODO: Test for empty base URL setting and give out error.
    if (mb_substr($url, -1) != '/')
      $url .= '/';
    $this->baseUrl = $url;
  }

  /**
   * Sets the session cookies.
   * @param string $sessionCookies A string of all cookies separated by "; ".
   */
  public function setSessionCookies($sessionCookies) {
    // TODO: Test for incorrect or empty session cookies?
    $this->sessionCookies = $sessionCookies;
  }

  /**
   * Download a file from the server.
   * @param string $fileId
   * @return false|string The downloaded file as string.
   */
  public function downloadFile($fileId) {
    $downloadUrl = 'products/files/httphandlers/filehandler.ashx?action=download&fileid=' . $fileId;
    $fileString = $this->makeGetRequest($downloadUrl);

    return $fileString;
  }

  public function downloadFileAsPdf($fileId) {
    $downloadUrl = 'products/files/httphandlers/filehandler.ashx?action=download&outputtype=.pdf&fileid=' . $fileId;
    $fileString = $this->makeGetRequest($downloadUrl);

    return $fileString;
  }

  /**
   * Makes a GET request to the website without custom data.
   */
  private function makeGetRequest($fileUrl) {
    $options = array(
      'http' => array(
        'method' => 'GET',
        'header'=> 'Cookie:' . $this->sessionCookies . "\r\n"
      )
    );

    $url = $this->baseUrl . $fileUrl;

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;

    // TODO: Test for returned status code?
  }

}
