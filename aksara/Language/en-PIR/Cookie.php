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
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
 */

return [
    'invalidExpiresTime' => 'Not valid "{0}" type for "Expires" attribute. Expected: string, integer, DateTimeInterface object.',
    'invalidExpiresValue' => 'The cookie expiration time be not valid, matey.',
    'invalidCookieName' => 'The cookie name "{0}" contains not valid characters, blast it.',
    'emptyCookieName' => 'The cookie name cannot be empty, ye scurvy dog.',
    'invalidSecurePrefix' => 'Using the "__Secure-" prefix requires settin\' the "Secure" attribute.',
    'invalidHostPrefix' => 'Using the "__Host-" prefix must be set with the "Secure" flag, must not have a "Domain" attribute, and the "Path" be set to "/".',
    'invalidSameSite' => 'The SameSite value must be None, Lax, Strict or a blank string, {0} given.',
    'invalidSameSiteNone' => 'Using the "SameSite=None" attribute requires settin\' the "Secure" attribute, arrr.',
    'invalidCookieInstance' => '"{0}" class expected cookies array to be instances of "{1}" but got "{2}" at index {3}, shiver me timbers.',
    'unknownCookieInstance' => 'Cookie object with name "{0}" and prefix "{1}" was not found in the treasure collection.',
];
