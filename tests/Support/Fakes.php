<?php

/**
 * This file is part of Aksara CMS, both framework and publishing
 * platform.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.txt file.
 *
 * When the signs is coming, those who don't believe at "that time"
 * have only two choices, commit suicide or become brutal.
 */

namespace Tests\Support;

class FakeSiteURI
{
    protected $baseURL;
    public function __construct($baseURL)
    {
        $this->baseURL = $baseURL;
    }
    public function baseUrl($path = '')
    {
        return $this->baseURL . $path;
    }
    public function getPath()
    {
        return '';
    }
    public function getQuery()
    {
        return '';
    }
    public function getScheme()
    {
        return 'http';
    }
    public function getUserInfo()
    {
        return '';
    }
    public function getHost()
    {
        return 'localhost';
    }
    public function getPort()
    {
        return 80;
    }
    public function getAuthority()
    {
        return 'localhost';
    }
    public function getSegments()
    {
        return [];
    }
    public function setSegments($segments)
    {
    }
}

class FakeRequest
{
    protected $uri;
    public function __construct($uri)
    {
        $this->uri = $uri;
    }
    public function getUri()
    {
        return $this->uri;
    }
    public function getGet($key = null)
    {
        return [];
    }
    public function getServer($key = null)
    {
        return null;
    }
    public function isAJAX()
    {
        return false;
    }
}

class FakeSession
{
    public function get($key = null)
    {
        return 'mock_session';
    }
    public function getFlashdata($key = null)
    {
        return [];
    }
    public function setFlashdata($key, $value)
    {
    }
}

class FakeEmail
{
    public function initialize($config)
    {
    }
    public function setFrom($from, $name = '', $returnPath = null)
    {
    }
    public function setTo($to)
    {
    }
    public function setSubject($subject)
    {
    }
    public function setMessage($body)
    {
    }
    public function send($autoClear = true)
    {
        return true;
    }
    public function printDebugger($include = ['headers', 'subject', 'body'])
    {
        return '';
    }
}
