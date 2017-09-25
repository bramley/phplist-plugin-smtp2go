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

/**
 * Registers the plugin with phplist.
 */
if (!interface_exists('EmailSender')) {
    return;
}
class Smtp2GoPlugin extends phplistPlugin implements EmailSender
{
    const VERSION_FILE = 'version.txt';

    /** @var SMTP2GO connector instance */
    private $connector = null;

    /*
     *  Inherited variables
     */
    public $name = 'SMTP2GO Plugin';
    public $authors = 'Duncan Cameron';
    public $description = 'Use SMTP2GO to send emails';
    public $documentationUrl = 'https://resources.phplist.com/plugin/smtp2go';
    public $settings = array(
        'smtp2go_api_key' => array(
            'value' => '',
            'description' => 'API key',
            'type' => 'text',
            'allowempty' => false,
            'category' => 'SMTP2GO',
        ),
        'smtp2go_api_baseurl' => array(
            'value' => 'https://api.smtp2go.com/v3/',
            'description' => 'API base URL',
            'type' => 'text',
            'allowempty' => false,
            'category' => 'SMTP2GO',
        ),
        'smtp2go_verify_cert' => array(
            'value' => true,
            'description' => 'Whether to verify the SMTP2GO certificate',
            'type' => 'boolean',
            'allowempty' => true,
            'category' => 'SMTP2GO',
        ),
    );

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->coderoot = dirname(__FILE__) . '/' . 'Smtp2GoPlugin' . '/';
        parent::__construct();
        $this->version = (is_file($f = $this->coderoot . self::VERSION_FILE))
            ? file_get_contents($f)
            : '';
    }

    /**
     * Provide the dependencies for enabling this plugin.
     *
     * @return array
     */
    public function dependencyCheck()
    {
        global $emailsenderplugin;

        return array(
            'PHP version 5.4.0 or greater' => version_compare(PHP_VERSION, '5.4') > 0,
            'phpList version 3.3.1 or greater' => version_compare(getConfig('version'), '3.3.1') >= 0,
            'No other plugin to send emails can be enabled' => empty($emailsenderplugin) || get_class($emailsenderplugin) == __CLASS__,
            'curl extension installed' => extension_loaded('curl'),
            'Common Plugin installed' => phpListPlugin::isEnabled('CommonPlugin'),
        );
    }

    /**
     * Send an email using the SMTP2GO API.
     *
     * @see https://apidoc.smtp2go.com/documentation/
     *
     * @param PHPlistMailer $phpmailer mailer instance
     * @param string        $headers   the message http headers
     * @param string        $body      the message body
     *
     * @return bool success/failure
     */
    public function send(PHPlistMailer $phpmailer, $headers, $body)
    {
        if ($this->connector === null) {
            $this->connector = new phpList\plugin\Smtp2GoPlugin\Connector(
                getConfig('smtp2go_api_key'),
                getConfig('smtp2go_api_baseurl'),
                (bool) getConfig('smtp2go_verify_cert')
            );
        }
        $mimeMessage = rtrim($headers, $phpmailer->LE) . $phpmailer->LE . $phpmailer->LE . $body;
        $result = $this->connector->mimeEmail($mimeMessage);
        $status = $result['status'];

        if ($status === false || $status != 200) {
            logEvent(sprintf('SMTP2GO status: %s, result: %s, curl error: %s', $status, $result['response'], $result['error']));

            return false;
        }

        return true;
    }
}
