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

namespace Aksara\Modules\Xhr\Controllers;

class Summernote extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        if (! get_userdata('is_logged')) {
            redirect_to(base_url());
        }

        $this->permission->must_ajax(base_url());
    }

    public function upload()
    {
        if (! is_dir(UPLOAD_PATH . '/summernote')) {
            if (mkdir(UPLOAD_PATH . '/summernote', 0755, true)) {
                copy(UPLOAD_PATH . '/placeholder.png', UPLOAD_PATH . '/summernote/placeholder.png');
            }
        }

        $source = service('request')->getFile('image');

        if (! $source->isValid() || $source->hasMoved()) {
            return false;
        }

        $mime_type = new \Config\Mimes();
        $valid_mime = [];

        $filetype = array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED));

        foreach ($filetype as $key => $val) {
            $valid_mime[] = $mime_type->guessTypeFromExtension($val);
        }

        if (! $source->getName() || ! in_array($source->getMimeType(), $valid_mime) || $source->getSizeByUnit('kb') > (MAX_UPLOAD_SIZE * 1024) || ! is_dir(UPLOAD_PATH) || ! is_writable(UPLOAD_PATH)) {
            return make_json([
                'status' => 'error',
                'messages' => phrase('Upload Error!')
            ]);
        }

        $filename = $source->getRandomName();
        $imageinfo = getimagesize($source);
        $width = ($imageinfo[0] > IMAGE_DIMENSION ? IMAGE_DIMENSION : $imageinfo[0]);
        $height = ($imageinfo[1] > IMAGE_DIMENSION ? IMAGE_DIMENSION : $imageinfo[1]);
        $master_dimension = ($imageinfo[0] > $imageinfo[1] ? 'width' : 'height');
        $this->image = \Config\Services::image('gd');

        if ($this->image->withFile($source)->resize($width, $height, true, $master_dimension)->save(UPLOAD_PATH . '/summernote/' . $filename)) {
            return make_json([
                'status' => 'success',
                'source' => get_image('summernote', $filename),
                'image' => get_image('summernote', $filename)
            ]);
        }

        return make_json([
            'status' => 'error',
            'messages' => phrase('Upload Error!')
        ]);
    }

    public function delete()
    {
        $filename = basename(service('request')->getPost('source'));

        if (file_exists(UPLOAD_PATH . '/summernote/' . $filename)) {
            @unlink(UPLOAD_PATH . '/summernote/' . $filename);

            return make_json([
                'status' => 'success',
                'messages' => phrase('Image was successfully removed.')
            ]);
        }

        return make_json([
            'status' => 'error',
            'messages' => phrase('Image was not found.')
        ]);
    }
}
