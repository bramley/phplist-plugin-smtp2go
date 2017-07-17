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

    /**
     * Constructor.
     *
     * @param string $apiKey  the API key to use
     * @param string $baseUrl the SMTP2GO API base url
     */
    public function __construct($apiKey, $baseUrl)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
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
        static $curl = null;

        if ($curl === null) {
            $headers = [
                'Content-Type: ' . 'application/json',
            ];
            $endpoint = $this->baseUrl . '/email/mime';
            $curl = curl_init($endpoint);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_USERAGENT, NAME . ' (phpList version ' . VERSION . ', http://www.phplist.com/)');
            curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        }
        $request = [
            'api_key' => $this->apiKey,
            'mime_email' => $mimeMessage,
        ];
        $request = json_encode($request);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        $curlResult = curl_exec($curl);

        if ($curlResult === false) {
            $result = [
                'status' => false,
                'response' => '',
                'error' => curl_error($curl),
            ];
            curl_close($curl);
            $curl = null;
        } else {
            $result = [
                'status' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
                'response' => $curlResult,
                'error' => '',
            ];
        }

        return $result;
    }
}
