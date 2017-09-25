<?php
/**
 * Smtp2GoPlugin plugin for phplist.
 *
 * This file is a part of Smtp2GoPlugin Plugin.
 *
 * This plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2017 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

namespace phpList\plugin\Smtp2GoPlugin;

class Connector
{
    /** @var string API key */
    private $apiKey;

    /** @var string Base URL */
    private $baseUrl;

    /** @var curl handle */
    private $curl = null;

    /** @var bool whether to verify certificate */
    private $verifyPeer;

    /**
     * Constructor.
     *
     * @param string $apiKey  the API key to use
     * @param string $baseUrl the SMTP2GO API base url
     */
    public function __construct($apiKey, $baseUrl, $verifyPeer)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
        $this->verifyPeer = $verifyPeer;
    }

    /**
     * Destructor.
     * Close the curl connection.
     */
    public function __destruct()
    {
        if ($this->curl !== null) {
            curl_close($this->curl);
        }
    }

    /**
     * Make an API call to send a complete mime email.
     *
     * @param string $mimeMessage the complete email
     *
     * @return array response structure
     */
    public function mimeEmail($mimeMessage)
    {
        if ($this->curl === null) {
            $headers = [
                'Content-Type: ' . 'application/json',
            ];
            $endpoint = $this->baseUrl . '/email/mime';
            $this->curl = curl_init($endpoint);
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($this->curl, CURLOPT_HEADER, false);
            curl_setopt($this->curl, CURLOPT_USERAGENT, NAME . ' (phpList version ' . VERSION . ', http://www.phplist.com/)');
            curl_setopt($this->curl, CURLOPT_ENCODING, 'gzip,deflate');
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $this->verifyPeer);
        }
        $request = [
            'api_key' => $this->apiKey,
            'mime_email' => base64_encode($mimeMessage),
        ];
        $request = json_encode($request);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $request);
        $curlResult = curl_exec($this->curl);

        if ($curlResult === false) {
            $result = [
                'status' => false,
                'response' => '',
                'error' => curl_error($this->curl),
            ];
            curl_close($this->curl);
            $this->curl = null;
        } else {
            $result = [
                'status' => curl_getinfo($this->curl, CURLINFO_HTTP_CODE),
                'response' => $curlResult,
                'error' => '',
            ];
        }

        return $result;
    }
}
