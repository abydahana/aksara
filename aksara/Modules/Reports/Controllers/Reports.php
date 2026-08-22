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

namespace Aksara\Modules\Reports\Controllers;

use Aksara\Libraries\Document;
use Aksara\Laboratory\Core;
use Aksara\Modules\Reports\Models\ReportsModel;
use CodeIgniter\HTTP\ResponseInterface;

class Reports extends Core
{
    private ReportsModel $report;
    private string $_title;
    private mixed $_output;
    private string $_template;
    private array $_params = [];
    private ?string $_dateStart;
    private ?string $_dateEnd;
    private ?int $_author;

    public function __construct()
    {
        parent::__construct();

        $this->setPermission();
        $this->setTheme('backend');
        $this->unsetMethod('create, read, update, delete, export, print, pdf');

        $this->_dateStart = service('request')->getGet('date_start');
        $this->_dateEnd = service('request')->getGet('date_end');
        $this->_author = (int) (service('request')->getGet('author') ?: service('request')->getPost('author'));

        if (service('request')->getPost('trigger') == 'dropdown' || service('request')->getPost('method') == 'ajax_select') {
            if (in_array('author', [service('request')->getPost('source'), service('request')->getPost('selector')])) {
                $authorFilter = $this->_authorFilter();

                if ($authorFilter instanceof ResponseInterface) {
                    return $authorFilter->getBody();
                }
            } elseif (in_array('category', [service('request')->getPost('source'), service('request')->getPost('selector')])) {
                $categoryFilter = $this->_categoryFilter();

                if ($categoryFilter instanceof ResponseInterface) {
                    return $categoryFilter->getBody();
                }
            }
        }

        $this->_template = 'Aksara\Modules\Reports\Views\\' . service('uri')->getSegment(2);
        $this->report = new ReportsModel();
    }

    public function index()
    {
        $this->setTitle(phrase('Reports'))
        ->setIcon('mdi mdi-chart-areaspline')
        ->setOutput('results', $this->_report())
        ->render();
    }

    public function content()
    {
        $this->_title = phrase('Content Summary Report');
        $this->_output = $this->report->content($this->_params());

        /* execute the thread */
        $this->_execute();
    }

    public function cms()
    {
        $this->_title = phrase('CMS Content Report');
        $this->_output = $this->report->cms($this->_params());

        /* execute the thread */
        $this->_execute();
    }

    public function pages()
    {
        $this->_title = phrase('Page Report');
        $this->_output = $this->report->pages($this->_params());

        /* execute the thread */
        $this->_execute();
    }

    public function blogs()
    {
        $this->_title = phrase('Blog Report');
        $this->_output = $this->report->blogs($this->_params());

        /* execute the thread */
        $this->_execute();
    }

    public function blogBook()
    {
        $this->_title = phrase('Blog Book');
        $this->_output = $this->report->blogBook($this->_params());

        $pageSize = service('request')->getGet('page_size') ?: service('request')->getPost('page_size') ?: 'folio';
        $columnMode = service('request')->getGet('column_mode') ?: service('request')->getPost('column_mode') ?: '1';
        $isLandscape = ('2' == $columnMode);

        $paperSizes = [
            'folio' => ['width' => '8.5in', 'height' => '13in'],
            'a4' => ['width' => '210mm', 'height' => '297mm'],
            'a5' => ['width' => '148mm', 'height' => '210mm'],
            'b5' => ['width' => '176mm', 'height' => '250mm'],
            'letter' => ['width' => '8.5in', 'height' => '11in'],
            'executive' => ['width' => '7.25in', 'height' => '10.5in']
        ];
        $paperSize = $paperSizes[strtolower($pageSize)] ?? $paperSizes['folio'];

        $this->_params['page-width'] = $isLandscape ? $paperSize['height'] : $paperSize['width'];
        $this->_params['page-height'] = $isLandscape ? $paperSize['width'] : $paperSize['height'];
        $this->_params['sheet-size'] = $this->_params['page-width'] . ' ' . $this->_params['page-height'];

        if ($isLandscape) {
            $this->_params['orientation'] = 'L';
            $this->_params['keepColumns'] = true;
        } else {
            $this->_params['orientation'] = 'P';
        }

        /* execute the thread */
        $this->_execute();
    }

    public function galleries()
    {
        $this->_title = phrase('Gallery Report');
        $this->_output = $this->report->galleries($this->_params());

        /* execute the thread */
        $this->_execute();
    }

    public function videos()
    {
        $this->_title = phrase('Video Report');
        $this->_output = $this->report->videos($this->_params());

        /* execute the thread */
        $this->_execute();
    }

    public function announcements()
    {
        $this->_title = phrase('Announcement Report');
        $this->_output = $this->report->announcements($this->_params());

        /* execute the thread */
        $this->_execute();
    }

    public function testimonials()
    {
        $this->_title = phrase('Testimonial Report');
        $this->_output = $this->report->testimonials($this->_params());

        /* execute the thread */
        $this->_execute();
    }

    public function peoples()
    {
        $this->_title = phrase('People Report');
        $this->_output = $this->report->peoples($this->_params());

        /* execute the thread */
        $this->_execute();
    }

    private function _execute()
    {
        if (! (isset($this->_output['results']) && $this->_output['results'])) {
            return throw_exception(404, phrase('No report can be made'), current_page('../'));
        }

        $data = [
            'title' => $this->_title,
            'results' => $this->_output['results'],
            'header' => $this->_output['header'],
            'sheetSize' => $this->_params['sheet-size'] ?? '8.5in 13in',
            'pageSize' => service('request')->getGet('page_size') ?: service('request')->getPost('page_size') ?: 'folio',
            'columnMode' => service('request')->getGet('column_mode') ?: service('request')->getPost('column_mode') ?: '1',
            'pageNumbering' => service('request')->getGet('page_numbering') ?: service('request')->getPost('page_numbering') ?: 'sheet'
        ];

        if (in_array(service('request')->getGet('method'), ['embed', 'download', 'export'])) {
            /**
             * Method document
             */
            $this->_output = view($this->_template, $data);

            $document = new Document();

            return $document->generate($this->_output, $this->_title, service('request')->getGet('method'), $this->_params);
        }

        echo view($this->_template, $data);
    }

    private function _report()
    {
        return [
            [
                'title' => phrase('Content Summary Report'),
                'description' => phrase('Summary of all project contents'),
                'icon' => 'mdi-file-chart',
                'color' => 'bg-body-secondary',
                'placement' => 'left',
                'controller' => 'content',
                'parameter' => [
                    'author' => $this->_authorFilter(),
                    'period' => $this->_period(true)
                ]
            ],
            [
                'title' => phrase('CMS Content Report'),
                'description' => phrase('Summary of CMS content totals'),
                'icon' => 'mdi-newspaper',
                'color' => 'bg-body-secondary',
                'placement' => 'left',
                'controller' => 'cms',
                'parameter' => [
                    'author' => $this->_authorFilter(),
                    'period' => $this->_period(true)
                ]
            ],
            [
                'title' => phrase('Page Report'),
                'description' => phrase('Report of website pages'),
                'icon' => 'mdi-file-document-outline',
                'color' => 'bg-body-secondary',
                'placement' => 'left',
                'controller' => 'pages',
                'parameter' => [
                    'author' => $this->_authorFilter(),
                    'period' => $this->_period(true)
                ]
            ],
            [
                'title' => phrase('Blog Report'),
                'description' => phrase('Report of blog posts'),
                'icon' => 'mdi-newspaper',
                'color' => 'bg-body-secondary',
                'placement' => 'left',
                'controller' => 'blogs',
                'parameter' => [
                    'category' => $this->_categoryFilter(),
                    'author' => $this->_authorFilter(),
                    'period' => $this->_period(true)
                ]
            ],
            [
                'title' => phrase('Blog Book Generator'),
                'description' => phrase('Generate a book from blog posts with images and table of contents'),
                'icon' => 'mdi-book-open-page-variant',
                'color' => 'bg-body-secondary',
                'placement' => 'left',
                'controller' => 'blogBook',
                'parameter' => [
                    'category' => $this->_categoryFilter(),
                    'author' => $this->_authorFilter(),
                    'page_size' => $this->_pageSizeFilter(),
                    'column_mode' => $this->_columnModeFilter('blogBook'),
                    'period' => $this->_period(true)
                ]
            ],
            [
                'title' => phrase('Gallery Report'),
                'description' => phrase('Report of galleries'),
                'icon' => 'mdi-image-multiple',
                'color' => 'bg-body-secondary',
                'placement' => 'left',
                'controller' => 'galleries',
                'parameter' => [
                    'author' => $this->_authorFilter(),
                    'period' => $this->_period(true)
                ]
            ],
            [
                'title' => phrase('Video Report'),
                'description' => phrase('Report of videos'),
                'icon' => 'mdi-video-outline',
                'color' => 'bg-body-secondary',
                'placement' => 'left',
                'controller' => 'videos',
                'parameter' => [
                    'author' => $this->_authorFilter(),
                    'period' => $this->_period(true)
                ]
            ],
            [
                'title' => phrase('Announcement Report'),
                'description' => phrase('Report of announcements'),
                'icon' => 'mdi-bullhorn-outline',
                'color' => 'bg-body-secondary',
                'placement' => 'left',
                'controller' => 'announcements',
                'parameter' => [
                    'author' => $this->_authorFilter(),
                    'period' => $this->_period(true)
                ]
            ],
            [
                'title' => phrase('Testimonial Report'),
                'description' => phrase('Report of testimonials'),
                'icon' => 'mdi-comment-text',
                'color' => 'bg-body-secondary',
                'placement' => 'left',
                'controller' => 'testimonials',
                'parameter' => [
                    'author' => $this->_authorFilter(),
                    'period' => $this->_period(true)
                ]
            ],
            [
                'title' => phrase('People Report'),
                'description' => phrase('Report of people profiles'),
                'icon' => 'mdi-account-group-outline',
                'color' => 'bg-body-secondary',
                'placement' => 'left',
                'controller' => 'peoples',
                'parameter' => [
                    'author' => $this->_authorFilter(),
                    'period' => $this->_period(true)
                ]
            ],
        ];
    }

    private function _params(): array
    {
        $author = service('request')->getGet('author') ?: service('request')->getPost('author') ?: $this->_author;
        $category = service('request')->getGet('category') ?: service('request')->getPost('category');

        return [
            'date_start' => service('request')->getGet('date_start') ?: service('request')->getPost('date_start') ?: $this->_dateStart ?: date('Y-m-01'),
            'date_end' => service('request')->getGet('date_end') ?: service('request')->getPost('date_end') ?: $this->_dateEnd ?: date('Y-m-d'),
            'author' => (! empty($author) && '0' !== $author) ? $author : null,
            'category' => (! empty($category) && '0' !== $category) ? $category : null
        ];
    }

    private function _categoryFilter()
    {
        if (service('request')->getPost('trigger') == 'dropdown' || service('request')->getPost('method') == 'ajax_select') {
            $keyword = service('request')->getPost('search');
            $page = service('request')->getPost('page') ?: 1;
            $limit = 50;
            $offset = ($page - 1) * $limit;

            $this->model->select('category_id, category_title, category_description');

            if ($keyword) {
                $this->model->groupStart()
                    ->like('category_title', $keyword)
                    ->orLike('category_description', $keyword)
                    ->groupEnd();
            }

            $categories = $this->model->where('status', 1)
                ->orderBy('category_title', 'ASC')
                ->limit($limit, $offset)
                ->get('blogs_categories')
                ->result();

            $results = [];

            if (1 == $page) {
                $results[] = [
                    'id' => '',
                    'text' => phrase('All Categories')
                ];
            }

            foreach ($categories ?: [] as $row) {
                $results[] = [
                    'id' => $row->category_id,
                    'text' => $row->category_title
                ];
            }

            $payload = [
                'results' => $results,
                'pagination' => ['more' => count($categories ?: []) >= $limit]
            ];

            if (service('request')->getPost('trigger') == 'dropdown') {
                return make_json([
                    'selector' => service('request')->getPost('selector'),
                    'suggestions' => $payload
                ]);
            }

            return make_json($payload);
        }

        $randomBytes = bin2hex(random_bytes(4));

        return '
            <div class="row">
                <div class="col-12">
                    <div class="form-group mb-3">
                        <label class="d-block text-muted" for="category_input_' . $randomBytes . '">
                            ' . phrase('Category') . '
                        </label>
                        <div class="form-group">
                            <select name="category" class="form-control form-control-sm" id="category_input_' . $randomBytes . '" data-role="select" data-href="' . current_page(null, ['category' => null, 'method' => null]) . '">
                                <option value="">' . phrase('All Categories') . '</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }

    private function _authorFilter()
    {
        if (service('request')->getPost('trigger') == 'dropdown' || service('request')->getPost('method') == 'ajax_select') {
            $keyword = service('request')->getPost('search');
            $page = service('request')->getPost('page') ?: 1;
            $limit = 50;
            $offset = ($page - 1) * $limit;

            $hasFirstName = $this->model->fieldExists('first_name', 'app_users');
            $hasLastName = $this->model->fieldExists('last_name', 'app_users');
            $hasEmail = $this->model->fieldExists('email', 'app_users');
            $hasUsername = $this->model->fieldExists('username', 'app_users');

            $select = ['user_id'];
            if ($hasUsername) {
                $select[] = 'username';
            }
            if ($hasFirstName) {
                $select[] = 'first_name';
            }
            if ($hasLastName) {
                $select[] = 'last_name';
            }
            if ($hasEmail) {
                $select[] = 'email';
            }

            $this->model->select(implode(', ', $select));

            if ($keyword) {
                $this->model->groupStart();
                if ($hasFirstName) {
                    $this->model->like('first_name', $keyword);
                }
                if ($hasLastName) {
                    $this->model->orLike('last_name', $keyword);
                }
                if ($hasUsername) {
                    $this->model->orLike('username', $keyword);
                }
                if ($hasEmail) {
                    $this->model->orLike('email', $keyword);
                }
                $this->model->groupEnd();
            }

            if ($hasFirstName) {
                $this->model->orderBy('first_name', 'ASC');
            } elseif ($hasUsername) {
                $this->model->orderBy('username', 'ASC');
            } else {
                $this->model->orderBy('user_id', 'ASC');
            }

            $users = $this->model->limit($limit, $offset)->get('app_users')->result();

            $results = [];

            if (1 == $page) {
                $results[] = [
                    'id' => '',
                    'text' => phrase('All Authors')
                ];
            }

            foreach ($users ?: [] as $row) {
                $firstName = $row->first_name ?? '';
                $lastName = $row->last_name ?? '';
                $username = $row->username ?? '';
                $name = trim($firstName . ' ' . $lastName);
                $results[] = [
                    'id' => $row->user_id,
                    'text' => ($name ?: $username) . ($username ? ' (@' . $username . ')' : null)
                ];
            }

            $payload = [
                'results' => $results,
                'pagination' => ['more' => count($users ?: []) >= $limit]
            ];

            if (service('request')->getPost('trigger') == 'dropdown') {
                return make_json([
                    'selector' => service('request')->getPost('selector'),
                    'suggestions' => $payload
                ]);
            }

            return make_json($payload);
        }

        $randomBytes = bin2hex(random_bytes(4));

        return '
            <div class="row">
                <div class="col-12">
                    <div class="form-group mb-3">
                        <label class="d-block text-muted" for="author_input_' . $randomBytes . '">
                            ' . phrase('Author') . '
                        </label>
                        <div class="form-group">
                            <select name="author" class="form-control form-control-sm" id="author_input_' . $randomBytes . '" data-role="select" data-href="' . current_page(null, ['author' => null, 'method' => null]) . '">
                                <option value="">' . phrase('All Authors') . '</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }

    private function _period($dateBeginning = false)
    {
        if (get_userdata('group_id') > 3) {
            return false;
        }

        return '
			<div class="row">
				<div class="col-6">
					<div class="form-group mb-3">
						<label class="d-block text-muted" for="start_date_input">
							' . phrase('Start Date') . '
						</label>
						<input type="date" name="date_start" class="form-control form-control-sm" value="' . ($dateBeginning ? date('Y-m-01') : date('Y-m-d')) . '" id="start_date_input" />
					</div>
				</div>
				<div class="col-6">
					<div class="form-group mb-3">
						<label class="d-block text-muted" for="end_date_input">
							' . phrase('End Date') . '
						</label>
						<input type="date" name="date_end" class="form-control form-control-sm" value="' . date('Y-m-d') . '" id="end_date_input" />
					</div>
				</div>
			</div>
		';
    }

    private function _pageSizeFilter()
    {
        $selected = service('request')->getGet('page_size') ?: service('request')->getPost('page_size') ?: 'folio';

        $sizes = [
            'folio' => 'Folio / F4 (8.5 x 13 in)',
            'a4' => 'A4 (210 x 297 mm)',
            'a5' => 'A5 - Novel / Pocket Book (148 x 210 mm)',
            'b5' => 'B5 - Textbook / Journal (176 x 250 mm)',
            'letter' => 'Letter (8.5 x 11 in)',
            'executive' => 'Executive (7.25 x 10.5 in)'
        ];

        $options = '';
        foreach ($sizes as $key => $val) {
            $options .= '<option value="' . $key . '"' . ($selected == $key ? ' selected' : '') . '>' . $val . '</option>';
        }

        $randomBytes = bin2hex(random_bytes(4));

        return '
            <div class="row">
                <div class="col-12">
                    <div class="form-group mb-3">
                        <label class="d-block text-muted" for="page_size_input_' . $randomBytes . '">
                            ' . phrase('Book Size / Paper Format') . '
                        </label>
                        <div class="form-group">
                            <select name="page_size" class="form-control form-control-sm" id="page_size_input_' . $randomBytes . '">
                                ' . $options . '
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }

    private function _columnModeFilter($reportSuffix = '')
    {
        $selected = service('request')->getGet('column_mode') ?: service('request')->getPost('column_mode') ?: '1';
        $id1 = 'column_mode_1_' . $reportSuffix;
        $id2 = 'column_mode_2_' . $reportSuffix;

        return '
            <div class="row">
                <div class="col-12">
                    <div class="form-group mb-3">
                        <label class="d-block text-muted mb-2">
                            ' . phrase('Column Layout & Orientation') . '
                        </label>
                        <div>
                            <div class="form-check form-check-inline me-3">
                                <input class="form-check-input" type="radio" name="column_mode" id="' . $id1 . '" value="1"' . ('1' == $selected ? ' checked' : '') . '>
                                <label class="form-check-label" for="' . $id1 . '">' . phrase('1 Column (Portrait)') . '</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="column_mode" id="' . $id2 . '" value="2"' . ('2' == $selected ? ' checked' : '') . '>
                                <label class="form-check-label" for="' . $id2 . '">' . phrase('2 Columns (Landscape)') . '</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }
}
