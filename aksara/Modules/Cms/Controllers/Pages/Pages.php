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

namespace Aksara\Modules\CMS\Controllers\Pages;

use Throwable;
use Aksara\Laboratory\Core;
use Aksara\Libraries\PageBuilder\PageBuilder;

class Pages extends Core
{
    private string $_table = 'pages';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        // Ignore query string signature
        $this->ignoreQueryString('language');
    }

    public function index()
    {
        $this->addFilter($this->_filter());

        if ($this->request->getGet('language')) {
            $this->where('language_id', $this->request->getGet('language'));
        } else {
            $this->where('language_id', get_setting('app_language') ?? 0);
        }

        // Load page builder library
        $pageBuilder = new PageBuilder();

        $this->setTitle(phrase('Pages'))
        ->setIcon('mdi mdi-file-document-outline')
        ->setButton('create', 'create', phrase('Create'), 'btn-primary --xhr', 'mdi mdi-plus')
        ->setButton('update', 'update', phrase('Update'), 'btn-secondary --xhr', 'mdi mdi-square-edit-outline', ['page_id' => 'page_id'])
        ->unsetColumn('page_id, author, page_slug, page_content, carousel_title, faq_title, carousel_id, faq_id, created_timestamp, updated_timestamp, language')
        ->unsetField('page_id, author, carousel_id, faq_id')
        ->unsetView('page_id, author, page_content, carousel_id, faq_id')
        ->columnOrder('page_title, page_description, updated, status')
        ->fieldOrder('page_title, page_description, language_id, created_timestamp, updated_timestamp, status')
        ->setField([
            'page_description' => 'textarea',
            'created_timestamp' => 'created_timestamp',
            'updated_timestamp' => 'updated_timestamp',
            'status' => 'boolean'
        ])
        ->setField('page_slug', 'slug', 'page_title')
        ->setField('page_title', 'hyperlink', 'pages', ['page_id' => 'page_id'], true)

        ->addButton('translate', phrase('Translate'), 'btn-dark --modal', 'mdi mdi-translate', ['page_id' => 'page_id'])
        ->addButton('../../pages', phrase('View Page'), 'btn-success', 'mdi mdi-eye', ['page_id' => 'page_id'], true)

        ->setRelation(
            'language_id',
            'app_languages.id',
            '{{ app_languages.language }}',
            [
                'app_languages.status' => 1
            ]
        )
        ->setValidation([
            'page_title' => 'required|max_length[255]|unique[' . $this->_table . '.page_title.page_id.' . $this->request->getGet('page_id') . ']',
            'page_slug' => 'max_length[255]|unique[' . $this->_table . '.page_slug.page_id.' . $this->request->getGet('page_id') . '.language_id.' . ($this->request->getPost('language_id') ?? $this->request->getGet('language') ?? 0) . ']',
            'language_id' => 'required',
            'status' => 'boolean'
        ])
        ->setDefault([
            'author' => get_userdata('user_id')
        ])
        ->setAlias([
            'page_title' => phrase('Title'),
            'page_description' => phrase('Description'),
            'page_slug' => phrase('Slug'),
            'created_timestamp' => phrase('Created'),
            'updated_timestamp' => phrase('Updated'),
            'language' => phrase('Language'),
            'language_id' => phrase('Language')
        ])
        ->setPlaceholder([
            'page_description' => phrase('Page summary to improve SEO')
        ])
        ->fieldPosition([
            'created_timestamp' => 2,
            'updated_timestamp' => 2,
            'status' => 2,
            'language_id' => 2,
            'language' => 2
        ])
        ->columnSize([
            1 => 'col-md-8',
            2 => 'col-md-4'
        ])
        ->setOutput([
            'builder_components' => $pageBuilder->getComponentsFlat(),
            'builder_categories' => $pageBuilder->getCategories()
        ])
        ->render($this->_table);
    }

    public function translate()
    {
        $this->setMethod('update');

        if (! $this->request->getGet('language')) {
            $current_language = $this->model->getWhere(
                $this->_table,
                [
                    'page_id' => $this->request->getGet('page_id') ?? 0
                ],
                1
            )
            ->row('language_id');

            $languages = $this->model->getWhere(
                'app_languages',
                [
                    'id !=' => $current_language,
                    'status' => 1
                ]
            )
            ->result();

            // Build language list
            $language_list = '';

            foreach ($languages as $key => $val) {
                $language_list .= '<a href="' . go_to('translate', ['language' => $val->id]) . '" class="list-group-item list-group-item-action --modal">
                    <i class="mdi mdi-translate me-2"></i> ' . $val->language . '
                </a>';
            }

            $content = '<div class="list-group list-group-flush">' . $language_list . '</div>';

            return make_json([
                'meta' => [
                    'title' => phrase('Choose Language'),
                    'icon' => 'mdi mdi-translate',
                    'popup' => true,
                    'modal_size' => 'modal-sm'
                ],
                'content' => $content,
            ]);
        }

        // Initialize page id
        $page_id = 0;

        try {
            // Get current data
            $data = $this->model->getWhere(
                $this->_table,
                [
                    'page_id' => $this->request->getGet('page_id') ?? 0
                ],
                1
            )
            ->row();

            // Check if translation already exists
            $checker = $this->model->getWhere(
                $this->_table,
                [
                    'page_slug' => $data->page_slug,
                    'language_id' => $this->request->getGet('language') ?? 0
                ],
                1
            )
            ->row();

            $page_id = $checker->page_id ?? 0;

            if (! $checker) {
                // Noop, modify data and create new translation
                unset($data->page_id);

                // Change language id
                $data->language_id = $this->request->getGet('language');

                // Insert new data
                $this->model->insert($this->_table, (array) $data);

                // Set new page id
                $page_id = $this->model->insertId();
            }
        } catch (Throwable $e) {
            return throw_exception(500, $e->getMessage());
        }

        $this->setTitle(phrase('Translate Page'))
        ->setIcon('mdi mdi-translate')
        ->unsetField('page_id, language_id, page_slug, author, carousel_id, faq_id, status, created_timestamp, updated_timestamp')
        ->setField([
            'page_description' => 'textarea',
            'page_content' => 'wysiwyg',
            'status' => 'boolean'
        ])
        ->where([
            'page_id' => $page_id
        ])
        ->setValidation([
            'page_title' => 'required|max_length[256]|unique[' . $this->_table . '.page_title.page_id.' . $this->request->getGet('page_id') . ']',
            'page_content' => 'required'
        ])
        ->setAlias([
            'page_title' => phrase('Title'),
            'page_description' => phrase('Description'),
            'page_content' => phrase('Content'),
        ])
        ->modalSize('modal-lg')
        ->render($this->_table);
    }

    /**
     * Preview page builder layout.
     */
    public function builderPreview()
    {
        $layout = $this->request->getPost('layout');

        if (! $layout) {
            return throw_exception(400, phrase('No layout data.'));
        }

        $decoded = json_decode($layout, true);
        $pb = new PageBuilder();
        $html = $pb->render($decoded);

        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Preview</title>';
        echo '<link rel="stylesheet" href="' . base_url('assets/bootstrap/css/bootstrap.min.css') . '">';
        echo '<link rel="stylesheet" href="' . base_url('assets/materialdesignicons/css/materialdesignicons.min.css') . '">';
        echo '<style>body{background:#f8f9fa}.section-padding{padding:80px 0}</style>';
        echo '</head><body>' . $html;
        echo '<script src="' . base_url('assets/bootstrap/js/bootstrap.bundle.min.js') . '"></script>';
        echo '</body></html>';
        exit;
    }

    public function builderImages()
    {
        $path = FCPATH . UPLOAD_PATH . DIRECTORY_SEPARATOR . 'pages';
        $query = $this->request->getGet('q');
        $sort = $this->request->getGet('sort') ?? 'newest';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $per_page = 12;

        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $files = array_diff(scandir($path), ['.', '..', 'index.html', '.htaccess', 'thumbs', 'icons', 'placeholder.png']);
        $images = [];

        foreach ($files as $file) {
            $filePath = $path . DIRECTORY_SEPARATOR . $file;
            if (is_file($filePath) && @is_array(getimagesize($filePath))) {
                // Search filter
                if ($query && stripos($file, $query) === false) {
                    continue;
                }

                $images[] = [
                    'name' => $file,
                    'url' => get_image('pages', $file),
                    'thumb' => get_image('pages', $file, 'thumb'),
                    'size' => filesize($filePath),
                    'time' => filemtime($filePath),
                    'formatted_size' => number_format(filesize($filePath) / 1024, 2) . ' KB',
                    'formatted_time' => date('Y-m-d H:i', filemtime($filePath))
                ];
            }
        }

        // Sorting
        usort($images, function ($a, $b) use ($sort) {
            if ('newest' === $sort) {
                return $b['time'] <=> $a['time'];
            }
            if ('oldest' === $sort) {
                return $a['time'] <=> $b['time'];
            }
            if ('name_asc' === $sort) {
                return strcasecmp($a['name'], $b['name']);
            }
            if ('name_desc' === $sort) {
                return strcasecmp($b['name'], $a['name']);
            }
            return 0;
        });

        $total = count($images);
        $images = array_slice($images, ($page - 1) * $per_page, $per_page);

        return make_json([
            'images' => $images,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page)
        ]);
    }

    /**
     * Handle image upload for builder.
     */
    public function builderUpload()
    {
        if (! $this->request->getFile('file')) {
            return make_json(['error' => phrase('No file uploaded.')]);
        }

        $file = $this->request->getFile('file');

        if (! $file->isValid()) {
            return make_json(['error' => $file->getErrorString()]);
        }

        // Security: strictly validate image
        if (! in_array($file->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])) {
            return make_json(['error' => phrase('Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.')]);
        }

        $path = FCPATH . UPLOAD_PATH . DIRECTORY_SEPARATOR . 'pages';

        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $name = $file->getRandomName();

        if ($file->move($path, $name)) {
            // Generate thumbnails and icons
            resize_image($path . DIRECTORY_SEPARATOR . $name);

            return make_json([
                'success' => true,
                'name' => $name,
                'url' => get_image('pages', $name)
            ]);
        }

        return make_json(['error' => phrase('Failed to move uploaded file.')]);
    }

    /**
     * Delete image from builder.
     */
    public function builderDelete()
    {
        $file = $this->request->getPost('file');

        if (! $file) {
            return make_json(['error' => phrase('No file specified.')]);
        }

        // Security: strictly validate filename to prevent directory traversal
        $filename = basename($file);

        if ('placeholder.png' === $filename) {
            return make_json(['error' => phrase('You cannot delete the placeholder image.')]);
        }

        $path = FCPATH . UPLOAD_PATH . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR;

        $files_to_delete = [
            $path . $filename,
            $path . 'thumbs' . DIRECTORY_SEPARATOR . $filename,
            $path . 'icons' . DIRECTORY_SEPARATOR . $filename
        ];

        $deleted = false;

        foreach ($files_to_delete as $file_path) {
            if (is_file($file_path)) {
                if (unlink($file_path)) {
                    $deleted = true;
                }
            }
        }

        if ($deleted) {
            return make_json(['success' => true]);
        }

        return make_json(['error' => phrase('Failed to delete file or file not found.')]);
    }

    public function afterUpdate()
    {
        return throw_exception(301, phrase('The page was successfully updated.'), current_page());
    }

    private function _filter()
    {
        $languages = [
            [
                'id' => 0,
                'label' => phrase('All languages')
            ]
        ];

        $languages_query = $this->model->getWhere(
            'app_languages',
            [
                'status' => 1
            ]
        )
        ->result();

        if ($languages_query) {
            foreach ($languages_query as $key => $val) {
                $languages[] = [
                    'id' => $val->id,
                    'label' => $val->language,
                    'selected' => $this->request->getGet('language') === $val->id
                ];
            }
        }

        return [
            'language' => [
                'type' => 'select',
                'label' => phrase('Language'),
                'values' => $languages
            ]
        ];
    }
}
