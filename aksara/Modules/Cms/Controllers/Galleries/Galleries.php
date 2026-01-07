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

namespace Aksara\Modules\CMS\Controllers\Galleries;

use Aksara\Laboratory\Core;

class Galleries extends Core
{
    private $_table = 'galleries';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        $this->gridView('gallery_images', 'galleries', ['gallery_slug' => 'gallery_slug'], true);
    }

    public function index()
    {
        $this->setTitle(phrase('Galleries'))
        ->setIcon('mdi mdi-folder-multiple-image')
        ->setPrimary('gallery_id')
        ->unsetColumn('gallery_id, gallery_slug, gallery_tags, gallery_attributes, created_timestamp, updated_timestamp, featured')
        ->unsetField('gallery_id, author')
        ->unsetView('gallery_id, first_name')
        ->columnOrder('gallery_images, gallery_title, gallery_description, first_name, featured, status')
        ->fieldOrder('gallery_images, gallery_title, gallery_slug, gallery_description, gallery_attributes, gallery_tags, featured, created_timestamp, updated_timestamp, status')
        ->viewOrder('gallery_images, gallery_title, gallery_slug, gallery_description, gallery_attributes, gallery_tags, featured, created_timestamp, updated_timestamp, status')
        ->setField([
            'gallery_images' => 'images',
            'gallery_description' => 'textarea',
            'gallery_attributes' => 'attribution',
            'featured' => 'boolean',
            'created_timestamp' => 'created_timestamp',
            'updated_timestamp' => 'updated_timestamp',
            'status' => 'boolean'
        ])
        ->setField('gallery_slug', 'slug', 'gallery_title')
        ->setField('gallery_title', 'hyperlink', 'galleries', ['gallery_slug' => 'gallery_slug'], true)

        ->addButton('../../galleries', phrase('View Album'), 'btn-success', 'mdi mdi-eye', ['gallery_slug' => 'gallery_slug'], true)

        ->addClass('gallery_description', 'minimal')
        ->setRelation(
            'author',
            'app__users.user_id',
            '{{ app__users.first_name }} {{ app__users.last_name }}'
        )
        ->mergeContent('{{ first_name }} {{ last_name }}', 'Author')
        ->setValidation([
            'gallery_title' => 'required|max_length[64]|unique[' . $this->_table . '.gallery_title.gallery_id.' . $this->request->getGet('gallery_id') . ']',
            'gallery_slug' => 'max_length[64]|unique[' . $this->_table . '.gallery_slug.gallery_id.' . $this->request->getGet('gallery_id') . ']',
            'gallery_description' => 'required',
            'featured' => 'boolean',
            'status' => 'boolean'
        ])
        ->setDefault('author', get_userdata('user_id'))
        ->fieldPosition([
            'gallery_tags' => 2,
            'featured' => 2,
            'created_timestamp' => 2,
            'updated_timestamp' => 2,
            'status' => 2
        ])
        ->columnSize([
            1 => 'col-md-7',
            2 => 'col-md-5'
        ])
        ->setAlias([
            'gallery_images' => phrase('Images'),
            'gallery_title' => phrase('Title'),
            'gallery_slug' => phrase('Slug'),
            'gallery_description' => phrase('Description'),
            'gallery_attributes' => phrase('Attributes'),
            'gallery_tags' => phrase('Tags'),
            'featured' => phrase('Featured'),
            'status' => phrase('Status'),
            'created_timestamp' => phrase('Created'),
            'updated_timestamp' => phrase('Updated')
        ])
        ->setPlaceholder([
            'gallery_description' => phrase('Page summary to improve SEO'),
            'gallery_tags' => phrase('Separate with commas')
        ])

        ->modalSize('modal-lg')

        ->render($this->_table);
    }
}
