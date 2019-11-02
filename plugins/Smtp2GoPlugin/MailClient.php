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

/**
 * This class is a client of the generic MailSender class and provides the request
 * data specific to the SMTP2GO API.
 *
 * {@inheritdoc}
 *
 * @see https://apidoc.smtp2go.com/documentation/#/POST%20/email/mime
 */
class MailClient implements \phpList\plugin\Common\IMailClient
{
    private $baseUrl;
    private $apiKey;

    public function __construct($baseUrl, $apiKey)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
    }

    public function requestBody(\PHPlistMailer $phplistmailer, $headers, $body)
    {
        $mimeMessage = rtrim($headers, "\r\n ") . "\r\n\r\n" . $body;
        $request = [
            'api_key' => $this->apiKey,
            'mime_email' => base64_encode($mimeMessage),
        ];

        return json_encode($request);
    }

    public function httpHeaders()
    {
        return [
            'Content-Type: ' . 'application/json',
        ];
    }

    public function endpoint()
    {
        return $this->baseUrl . 'email/mime';
    }

    public function verifyResponse($response)
    {
        return true;
    }
}
