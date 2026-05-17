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

namespace Aksara\Modules\XHR\Controllers;

use Config\Mimes;
use Config\Services;
use Aksara\Laboratory\Core;

class Summernote extends Core
{
    public function __construct()
    {
        parent::__construct();

        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('Access denied'), base_url());
        }

        $this->permission->mustAjax(base_url());
    }

    public function upload()
    {
        if (! is_dir(UPLOAD_PATH . '/summernote')) {
            if (mkdir(UPLOAD_PATH . '/summernote', 0755, true)) {
                copy(UPLOAD_PATH . '/placeholder.png', UPLOAD_PATH . '/summernote/placeholder.png');
            }
        }

        $source = $this->request->getFile('image');

        if (! $source->isValid() || $source->hasMoved()) {
            return false;
        }

        $mime_type = new Mimes();
        $valid_mime = [];

        $filetype = array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED));

        foreach ($filetype as $key => $val) {
            $valid_mime[] = $mime_type->guessTypeFromExtension($val);
        }

        if (! $source->getName() || $source->getSizeByMetricUnit(\CodeIgniter\Files\FileSizeUnit::MB) > MAX_UPLOAD_SIZE || ! is_dir(UPLOAD_PATH) || ! is_writable(UPLOAD_PATH)) {
            return make_json([
                'status' => 'error',
                'messages' => phrase('Upload Error!')
            ]);
        }

        $extension = strtolower($source->getClientExtension() ?: pathinfo($source->getName(), PATHINFO_EXTENSION));
        $mimeType = strtolower($source->getMimeType());
        $validExtensions = array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED));
        $validMimeTypes = array_filter($valid_mime);

        if (! in_array($extension, $validExtensions, true) || ! in_array($mimeType, $validMimeTypes, true)) {
            return make_json([
                'status' => 'error',
                'messages' => phrase('Upload Error!')
            ]);
        }

        $filename = $source->getRandomName();

        // Read file contents
        $fileContent = file_get_contents($source->getPathName());

        // Reject embedded PHP / scripts in uploaded image files
        if (preg_match('/<\?(php|=)|<script\b/i', $fileContent)) {
            return make_json([
                'status' => 'error',
                'messages' => phrase('The file is not allowed to upload')
            ]);
        }

        $imageinfo = @getimagesize($source->getPathName());
        if (! $imageinfo || ! isset($imageinfo[2]) || ! in_array(image_type_to_mime_type($imageinfo[2]), $validMimeTypes, true)) {
            return make_json([
                'status' => 'error',
                'messages' => phrase('Upload Error!')
            ]);
        }

        $width = ($imageinfo[0] > IMAGE_DIMENSION ? IMAGE_DIMENSION : $imageinfo[0]);
        $height = ($imageinfo[1] > IMAGE_DIMENSION ? IMAGE_DIMENSION : $imageinfo[1]);
        $master_dimension = ($imageinfo[0] > $imageinfo[1] ? 'width' : 'height');
        $image = Services::image('gd');

        if ($image->withFile($source)->resize($width, $height, true, $master_dimension)->save(UPLOAD_PATH . '/summernote/' . $filename)) {
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
        $filename = basename($this->request->getPost('source'));

        if (file_exists(UPLOAD_PATH . '/summernote/' . $filename)) {
            @unlink(UPLOAD_PATH . '/summernote/' . $filename);

            return make_json([
                'status' => 'success',
                'messages' => phrase('Image was successfully removed')
            ]);
        }

        return make_json([
            'status' => 'error',
            'messages' => phrase('Image was not found')
        ]);
    }
}
