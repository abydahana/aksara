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

namespace Aksara\Libraries;

use Aksara\Laboratory\Model;
use Picqer\Barcode\BarcodeGeneratorPNG;
use chillerlan\QRCode\QRCode;

/**
 * Miscellaneous Library
 * This class is used to generate any miscellanious features
 */
class Miscellaneous
{
    public function __construct()
    {
    }

    /**
     * QR Code generator
     *
     * @param   mixed|null $params
     */
    public function qrcode_generator($params = null)
    {
        $generator = new QRCode();

        if (! file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_qrcode' . DIRECTORY_SEPARATOR . sha1(json_encode($params)) . '.png')) {
            if (! is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_qrcode')) {
                mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_qrcode', 0755, true);
            }

            $data = $generator->render($params);

            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);

            file_put_contents(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_qrcode' . DIRECTORY_SEPARATOR . sha1(json_encode($params)) . '.png', $data);
        }

        return get_image('_qrcode', sha1(json_encode($params)) . '.png');
    }

    /**
     * Barcode generator
     *
     * @param   mixed|null $params
     */
    public function barcode_generator($params = null)
    {
        $generator = new BarcodeGeneratorPNG();

        if (! file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_barcode' . DIRECTORY_SEPARATOR . sha1(json_encode($params)) . '.png')) {
            if (! is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_barcode')) {
                mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_barcode', 0755, true);
            }

            $data = $generator->getBarcode($params, $generator::TYPE_CODE_128, 1, 60);

            file_put_contents(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_barcode' . DIRECTORY_SEPARATOR . sha1(json_encode($params)) . '.png', $data);
        }

        return get_image('_barcode', sha1(json_encode($params)) . '.png');
    }

    /**
     * Shortlink generator
     *
     * @param   mixed|null $params
     * @param   mixed|null $slug
     */
    public function shortlink_generator($params = null, $slug = null, $data = [])
    {
        if (! $params) {
            return false;
        }

        $model = new Model();

        // Hash generator
        $hash = substr(sha1(uniqid('', true)), -6);

        // Check if hash already present
        if ($model->get_where('app__shortlinks', ['hash' => $hash], 1)->row()) {
            // Hash already present, repeat generator
            $this->shortlink_generator($params);
        }

        $checker = $model->get_where('app__shortlinks', ['url' => $params], 1)->row();

        // Check if parameter already present
        if ($checker) {
            $hash = $checker->hash;
        } else {
            // No data present, insert one
            $model->insert(
                'app__shortlinks',
                [
                    'hash' => $hash,
                    'url' => $params,
                    'data' => json_encode($data)
                ]
            );
        }

        return base_url(($slug ? $slug : 'shortlink') . '/' . $hash);
    }
}
