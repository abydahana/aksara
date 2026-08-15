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

namespace Modules\Home\Controllers;

use Aksara\Laboratory\Core;
use Modules\Home\Models\HomeModel;

class Home extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $model = new HomeModel();

        $this->setTitle(phrase('Welcome'))
        ->setDescription(get_setting('app_description'))
        ->setOutput([
            'statistics' => $model->getStatistics(),
            'spotlight' => $model->getSpotlight(),
            'categories' => $model->getCategories(),
            'articles' => $model->getArticles(),
            'galleries' => $model->getGalleries(),
            'peoples' => $model->getPeoples(),
            'testimonials' => $model->getTestimonials()
        ])
        ->render();
    }
}
