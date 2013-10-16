<?php
/**
 *
 * ThinkUp/webapp/_lib/class.Mailer.php
 *
 * Copyright (c) 2009-2013 Gina Trapani
 *
 * LICENSE:
 *
 * This file is part of ThinkUp (http://thinkup.com).
 *
 * ThinkUp is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any
 * later version.
 *
 * ThinkUp is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with ThinkUp.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 *
 * @author Gina Trapani <ginatrapani[at]gmail[dot]com>
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2009-2013 Gina Trapani
 */
class \Mailer\Mandrill {
    /**
     * Send email from ThinkUp instalation. If you're running tests, just write the message headers and contents to
     * the file system in the data directory.
     * @param str $to A valid email address
     * @param str $subject
     * @param str $message
     */
    public static function mail($to, $subject, $message) {
        $config = Config::getInstance();

        $app_title = $config->getValue('app_title_prefix'). "ThinkUp";
        $host = self::getHost();
        $mandrill_key = $config->getValue('mandrill_key');
        $params = array(
                'key' => $mandrill_key,
                'message' => array(
                        'html' => $message,
                        'to' => $to,
                        'subject', $subject
                        'from_name' => $app_title,
                        'from_email' => "notifications@${host}",
                    ),
            );
        $params = json_encode($params);

        $ch = $this->ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mandrill-PHP/1.0.22');
        curl_setopt($ch, CURLOPT_URL, 'https://mandrillapp.com/api/1.0/messages/send');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 

        //don't send email when running tests, just write it to the filesystem for assertions
        if (Mailer::isTest()) {
            Mailer::setLastMail($params);
        } else {
            curl_exec($ch);
        }


    }
}