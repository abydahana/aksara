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

use Aksara\Laboratory\Core;
use Aksara\Libraries\Uploader;

class Summernote extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->setPermission();
        $this->permission->mustAjax();
    }

    public function index()
    {
        return throw_exception(404, phrase('The page you requested does not exist or has already been archived.'));
    }

    public function upload()
    {
        $source = $this->request->getFile('image');

        if (! $source || ! $source->getName()) {
            return make_json([
                'status' => 'error',
                'messages' => phrase('No file uploaded.')
            ]);
        }

        if (! $source->isValid()) {
            return make_json([
                'status' => 'error',
                'messages' => (
                    $source->getError() !== UPLOAD_ERR_NO_FILE
                    ? $source->getErrorString()
                    : phrase('No file uploaded.')
                )
            ]);
        }

        $uploader = new Uploader();
        $filename = $uploader->upload($source, UPLOAD_PATH . '/summernote', 'image');

        if ($filename) {
            return make_json([
                'status' => 'success',
                'source' => get_image('summernote', $filename),
                'image' => get_image('summernote', $filename)
            ]);
        }

        return make_json([
            'status' => 'error',
            'messages' => ($uploader->getErrorString() ?: phrase('Upload Error!'))
        ]);
    }

    public function delete()
    {
        $filename = basename($this->request->getPost('source'));

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
