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
    'sourceImageRequired' => 'Ye must specify a source portrait in yer preferences, matey.',
    'gdRequired' => 'The GD portrait library be required to use this feature.',
    'gdRequiredForProps' => 'Yer ship must support the GD portrait library in order to determine the portrait properties.',
    'gifNotSupported' => 'GIF portraits are often not supported due to Privateer licenses. Ye may have to use JPG or PNG portraits instead.',
    'jpgNotSupported' => 'JPG portraits be not supported, arrr.',
    'pngNotSupported' => 'PNG portraits be not supported.',
    'webpNotSupported' => 'WEBP portraits be not supported.',
    'fileNotSupported' => 'The supplied scroll be not a supported portrait type.',
    'unsupportedImageCreate' => 'Yer ship does not support the required functionality to process this type of portrait.',
    'jpgOrPngRequired' => 'The portrait resize protocol specified in yer preferences only works with JPEG or PNG portrait types.',
    'rotateUnsupported' => 'Portrait rotation does not appear to be supported by yer ship.',
    'imageProcessFailed' => 'Portrait processing failed. Please verify that yer ship supports the chosen protocol and that the path to yer portrait library be correct.',
    'rotationAngleRequired' => 'An angle of rotation be required to rotate the portrait, ye scallywag.',
    'invalidPath' => 'The path to the portrait be not correct.',
    'copyFailed' => 'The portrait copy routine failed, blast it.',
    'missingFont' => 'Unable to find a font to use.',
    'saveFailed' => 'Unable to save the portrait. Please make sure the portrait and scroll directory be writable.',
    'invalidDirection' => 'Flip direction can be only "vertical" or "horizontal". Given: "{0}"',
    'exifNotSupported' => 'Readin\' EXIF data be not supported by this PHP installation.',

    // @deprecated
    'libPathInvalid' => 'The path to yer portrait library be not correct. Please set the correct path in yer portrait preferences. "{0}"',
];
