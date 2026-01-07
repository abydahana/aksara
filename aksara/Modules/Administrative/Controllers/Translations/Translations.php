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

namespace Aksara\Modules\Administrative\Controllers\Translations;

use Aksara\Laboratory\Core;
use Throwable;

class Translations extends Core
{
    private $_table = 'app__languages';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        $this->unsetUpdate('id', [1]);
        $this->unsetDelete('id', [1]);
    }

    public function index()
    {
        $this->setTitle(phrase('Translations'))
        ->setIcon('mdi mdi-translate')
        ->setDescription('
            <div class="row">
                <div class="col-12">
                    ' . phrase('Click the synchronize button to equate the phrases for each translations.') . '
                </div>
            </div>
        ')
        ->unsetColumn('id')
        ->unsetField('id')
        ->unsetView('id')
        ->setField([
            'status' => 'boolean'
        ])
        ->addToolbar('synchronize', phrase('Synchronize'), 'btn btn-info --xhr show-progress', 'mdi mdi-reload')
        ->addButton('translate', phrase('Translate'), 'btn btn-success --xhr', 'mdi mdi-comment-processing-outline', ['id' => 'id', 'code' => 'code', 'keyword' => null])
        ->setValidation([
            'language' => 'required|string|max_length[32]',
            'code' => 'required|alpha_dash|max_length[32]|unique[app__languages.code.id.' . $this->request->getGet('id') . ']',
            'locale' => 'required|string|max_length[64]',
            'status' => 'boolean'
        ])
        ->setAlias([
            'language' => phrase('Language'),
            'code' => phrase('Code'),
            'locale' => phrase('Locale'),
            'status' => phrase('Status')
        ])

        ->render($this->_table);
    }

    public function afterInsert()
    {
        /* try to add language file */
        try {
            /* check if language directory is exists */
            if (! is_dir(WRITEPATH . 'translations') && mkdir(WRITEPATH . 'translations', 0755, true)) {
                /* put content into file */
                file_put_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $this->request->getPost('code') . '.json', json_encode([]));
            } else {
                /* put content into file */
                file_put_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $this->request->getPost('code') . '.json', json_encode([]));
            }
        } catch (Throwable $e) {
            return throw_exception(500, $e->getMessage());
        }
    }

    public function afterUpdate()
    {
        /* try to update language file */
        try {
            /* check if language directory is exists */
            if (file_exists(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $this->request->getGet('code') . '.json')) {
                /* rename old file */
                rename(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $this->request->getGet('code') . '.json', WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . $this->request->getPost('code') . '.json');
            }
        } catch (Throwable $e) {
            return throw_exception(500, $e->getMessage());
        }
    }
}
