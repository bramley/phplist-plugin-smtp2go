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

    /** @var phpList\plugin\Common\MailSender sender instance */
    private $mailSender = null;

    /*
     *  Inherited variables
     */
    public $name = 'SMTP2GO Plugin';
    public $authors = 'Duncan Cameron';
    public $description = 'Use SMTP2GO to send emails';
    public $documentationUrl = 'https://resources.phplist.com/plugin/smtp2go';
    public $settings = [
        'smtp2go_api_key' => [
            'value' => '',
            'description' => 'API key',
            'type' => 'text',
            'allowempty' => false,
            'category' => 'SMTP2GO',
        ],
        'smtp2go_api_baseurl' => [
            'value' => 'https://api.smtp2go.com/v3/',
            'description' => 'API base URL',
            'type' => 'text',
            'allowempty' => false,
            'category' => 'SMTP2GO',
        ],
        'smtp2go_verify_cert' => [
            'value' => true,
            'description' => 'Whether to verify the SMTP2GO certificate',
            'type' => 'boolean',
            'allowempty' => true,
            'category' => 'SMTP2GO',
        ],
        'smtp2go_multi' => [
            'value' => false,
            'description' => 'Whether to use multi-curl to send emails concurrently',
            'type' => 'boolean',
            'allowempty' => true,
            'category' => 'SMTP2GO',
        ],
        'smtp2go_multi_limit' => [
            'value' => 4,
            'min' => 2,
            'max' => 32,
            'description' => 'The maximum number of emails to send concurrently when using multi-curl, (between 2 and 32)',
            'type' => 'integer',
            'allowempty' => false,
            'category' => 'SMTP2GO',
        ],
        'smtp2go_multi_log' => [
            'value' => false,
            'description' => 'Whether to create a log file showing all multi-curl transfers',
            'type' => 'boolean',
            'allowempty' => true,
            'category' => 'SMTP2GO',
        ],
        'smtp2go_curl_verbose' => [
            'value' => false,
            'description' => 'Whether to generate verbose curl output (use only for debugging)',
            'type' => 'boolean',
            'allowempty' => true,
            'category' => 'SMTP2GO',
        ],
    ];

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
        global $emailsenderplugin, $plugins;

        return array(
            'PHP version 5.4.0 or greater' => version_compare(PHP_VERSION, '5.4') > 0,
            'phpList version 3.3.1 or greater' => version_compare(getConfig('version'), '3.3.1') >= 0,
            'No other plugin to send emails can be enabled' => empty($emailsenderplugin) || get_class($emailsenderplugin) == __CLASS__,
            'curl extension installed' => extension_loaded('curl'),
            'Common Plugin version 3.7.0 or later installed' => (
                phpListPlugin::isEnabled('CommonPlugin') && version_compare($plugins['CommonPlugin']->version, '3.7.0') >= 0
            ),
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
    public function send(PHPlistMailer $phplistmailer, $messageheader, $messagebody)
    {
        if ($this->mailSender === null) {
            $client = new phpList\plugin\Smtp2GoPlugin\MailClient(
                getConfig('smtp2go_api_baseurl'),
                getConfig('smtp2go_api_key')
            );

            $this->mailSender = new phpList\plugin\Common\MailSender(
                $client,
                (bool) getConfig('smtp2go_multi'),
                getConfig('smtp2go_multi_limit'),
                (bool) getConfig('smtp2go_multi_log'),
                (bool) getConfig('smtp2go_curl_verbose'),
                (bool) getConfig('smtp2go_verify_cert')
            );
        }

        return $this->mailSender->send($phplistmailer, $messageheader, $messagebody);
    }

    /**
     * This hook is called within the processqueue shutdown() function.
     *
     * For command line processqueue phplist exits in its shutdown function
     * therefore need to explicitly call the mailsender shutdown method.
     */
    public function processSendStats($sent = 0, $invalid = 0, $failed_sent = 0, $unconfirmed = 0, $counters = array())
    {
        $this->mailSender->shutdown();
    }
}
