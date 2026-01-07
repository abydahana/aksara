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

namespace Tests\Unit\Libraries;

use CodeIgniter\Test\CIUnitTestCase;
use Aksara\Libraries\Messaging;
use Config\Services;

class FakeEmailMessaging
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

class FakeRequestMessaging
{
    public function getServer($key = null)
    {
        if ('SERVER_ADMIN' === $key) {
            return 'admin@example.com';
        }
        return null;
    }
}

class MessagingTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset Services to ensure mocks are accepted
        Services::reset(true);
        // Reset mock settings
        \Tests\Support\MockSettings::reset();
    }

    public function testSetters()
    {
        $messaging = new Messaging();
        $messaging->set_email('test@example.com')
                  ->set_phone('123456')
                  ->set_subject('Subject')
                  ->set_message('Message');

        $reflector = new \ReflectionClass($messaging);

        $emailProp = $reflector->getProperty('_recipientEmail');
        $emailProp->setAccessible(true);
        $this->assertEquals('test@example.com', $emailProp->getValue($messaging));

        $phoneProp = $reflector->getProperty('_recipientPhone');
        $phoneProp->setAccessible(true);
        $this->assertEquals('123456', $phoneProp->getValue($messaging));
    }

    public function testSendInstant()
    {
        // Setup mock settings for global get_setting
        \Tests\Support\MockSettings::set('smtp_host', 'smtp.example.com');
        \Tests\Support\MockSettings::set('smtp_username', 'user');

        // Use real encrypter to generate valid encrypted password
        // This might fail if encryption key is not set or CI config is incomplete?
        // But previously it worked. The test bootstrap defines constants.
        // We need to ensure Services::encrypter() works.
        $realEncrypter = Services::encrypter();
        $encryptedPass = base64_encode($realEncrypter->encrypt('pass'));

        \Tests\Support\MockSettings::set('smtp_password', $encryptedPass);
        \Tests\Support\MockSettings::set('smtp_port', 587);

        // Inject Fake Email Service
        Services::injectMock('email', new FakeEmailMessaging());

        // Inject Fake Request
        Services::injectMock('request', new FakeRequestMessaging());

        $messaging = new Messaging();
        $messaging->set_email('recipient@example.com')
                  ->set_subject('Test Subject')
                  ->set_message('<p>Test Body</p>');

        // Assert that no exception is thrown
        $messaging->send(true);
        $this->assertTrue(true);
    }
}
