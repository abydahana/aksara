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

/**
 * CodeIgniter Download Helpers
 *
 * @category    Helpers
 * @author        EllisLab Dev Team
 * @see        https://codeigniter.com/user_guide/helpers/download_helper.html
 */

// ------------------------------------------------------------------------

if (! function_exists('force_download')) {
    /**
     * Force Download
     *
     * Generates headers that force a download to happen
     *
     * @param    string    filename
     * @param    mixed    the data to be downloaded
     * @param    bool    whether to try and send the actual file MIME type
     *
     * @return void
     */
    function force_download($filename = '', $data = '', $set_mime = false)
    {
        if ('' === $filename or '' === $data) {
            return;
        } elseif (null === $data) {
            if (! @is_file($filename) or false === ($filesize = @filesize($filename))) {
                return;
            }

            $filepath = $filename;
            $filename = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
            $filename = end($filename);
        } else {
            $filesize = strlen($data);
        }

        // Set the default MIME type to send
        $mime = 'application/octet-stream';

        $x = explode('.', $filename);
        $extension = end($x);

        if (true === $set_mime) {
            if (count($x) === 1 or '' === $extension) {
                /* If we're going to detect the MIME type,
                 * we'll need a file extension.
                 */
                return;
            } elseif ('css' == $extension) {
                $mime = 'text/css';
            } elseif ('js' == $extension) {
                $mime = 'application/javascript';
            } elseif ('png' == $extension) {
                $mime = 'image/png';
            } elseif ('jpg' == $extension || 'jpeg' == $extension) {
                $mime = 'image/jpeg';
            } elseif ('gif' == $extension) {
                $mime = 'image/gif';
            } elseif ('svg' == $extension) {
                $mime = 'image/svg+xml';
            } elseif ('bmp' == $extension) {
                $mime = 'image/bmp';
            } elseif ('webp' == $extension) {
                $mime = 'image/webp';
            }
        }

        /* It was reported that browsers on Android 2.1 (and possibly older as well)
         * need to have the filename extension upper-cased in order to be able to
         * download it.
         *
         * Reference: http://digiblog.de/2011/04/19/android-and-the-download-file-headers/
         */
        if (count($x) !== 1 && preg_match('/Android\s(1|2\.[01])/', service('request')->getServer('HTTP_USER_AGENT'))) {
            $x[count($x) - 1] = strtoupper($extension);
            $filename = implode('.', $x);
        }

        if (null === $data && false === ($fp = @fopen($filepath, 'rb'))) {
            return;
        }

        // Clean output buffer
        if (ob_get_level() !== 0 && false === @ob_end_clean()) {
            @ob_clean();
        }

        // Generate the server headers
        header('Content-Type: '.$mime);
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.$filesize);
        header('Cache-Control: private, no-transform, no-store, must-revalidate');

        // If we have raw data - just dump it
        if (null !== $data) {
            exit($data);
        }

        // Flush 1MB chunks of data
        while (! feof($fp) && false !== ($data = fread($fp, 1048576))) {
            echo $data;
        }

        fclose($fp);

        exit;
    }
}
