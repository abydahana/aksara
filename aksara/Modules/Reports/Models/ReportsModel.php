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

namespace Aksara\Modules\Reports\Models;

use Aksara\Laboratory\Model;

class ReportsModel extends Model
{
    private array $statusLabels = [
        '-1' => 'Deleted',
        '0' => 'Inactive',
        '1' => 'Active',
        '2' => 'Draft',
        '3' => 'Archived'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function content($params = [])
    {
        $results = [
            'summary' => [
                'pages' => $this->safeCount('pages', $params),
                'posts' => $this->safeCount('blogs', $params),
                'galleries' => $this->safeCount('galleries', $params),
                'videos' => $this->safeCount('videos', $params)
            ],
            'cms' => [
                'pages' => $this->latestRows('pages', 'page_title', 'created_at', $params),
                'blogs' => $this->latestBlogs($params),
                'blog_categories' => $this->blogCategories($params),
                'galleries' => $this->latestRows('galleries', 'gallery_title', 'created_at', $params),
                'videos' => $this->latestRows('videos', 'title', 'created_at', $params),
                'announcements' => $this->latestRows('announcements', 'title', 'created_at', $params),
                'testimonials' => $this->latestRows('testimonials', 'testimonial_title', 'created_at', $params),
                'peoples' => $this->people($params)
            ],
            'status' => $this->statusOverview(null, $params)
        ];

        return $this->output($results, $params);
    }

    public function cms($params = [])
    {
        return $this->output([
            'summary' => [
                'pages' => $this->safeCount('pages', $params),
                'posts' => $this->safeCount('blogs', $params),
                'blog_categories' => $this->safeCount('blogs_categories', $params),
                'galleries' => $this->safeCount('galleries', $params),
                'videos' => $this->safeCount('videos', $params),
                'announcements' => $this->safeCount('announcements', $params),
                'testimonials' => $this->safeCount('testimonials', $params),
                'peoples' => $this->safeCount('peoples', $params)
            ],
            'pages' => $this->latestRows('pages', 'page_title', 'created_at', $params),
            'blogs' => $this->latestBlogs($params),
            'blog_categories' => $this->blogCategories($params),
            'galleries' => $this->latestRows('galleries', 'gallery_title', 'created_at', $params),
            'videos' => $this->latestRows('videos', 'title', 'created_at', $params),
            'announcements' => $this->latestRows('announcements', 'title', 'created_at', $params),
            'testimonials' => $this->latestRows('testimonials', 'testimonial_title', 'created_at', $params),
            'peoples' => $this->people($params),
            'status' => $this->statusOverview(['pages', 'blogs', 'galleries', 'videos', 'announcements', 'testimonials', 'peoples'], $params)
        ], $params);
    }

    public function pages($params = [])
    {
        return $this->output([
            'summary' => [
                'pages' => $this->safeCount('pages', $params)
            ],
            'latest' => $this->latestRows('pages', 'page_title', 'created_at', $params),
            'status' => $this->groupCount('pages', 'status', $params)
        ], $params);
    }

    public function blogs($params = [])
    {
        return $this->output([
            'summary' => [
                'posts' => $this->safeCount('blogs', $params),
                'categories' => $this->safeCount('blogs_categories', $params)
            ],
            'categories' => $this->blogCategories($params),
            'latest' => $this->latestBlogs($params),
            'status' => $this->groupCount('blogs', 'status', $params)
        ], $params);
    }

    public function blogBook($params = [])
    {
        return $this->output([
            'summary' => [
                'posts' => $this->safeCount('blogs', $params),
                'categories' => $this->safeCount('blogs_categories', $params)
            ],
            'posts' => $this->blogBookPosts($params),
            'categories' => $this->blogCategories($params)
        ], $params);
    }

    public function galleries($params = [])
    {
        return $this->output([
            'summary' => [
                'galleries' => $this->safeCount('galleries', $params),
                'featured' => $this->countWhere('galleries', 'featured', 1, $params)
            ],
            'latest' => $this->latestRows('galleries', 'gallery_title', 'created_at', $params),
            'status' => $this->groupCount('galleries', 'status', $params)
        ], $params);
    }

    public function videos($params = [])
    {
        return $this->output([
            'summary' => [
                'videos' => $this->safeCount('videos', $params),
                'featured' => $this->countWhere('videos', 'featured', 1, $params)
            ],
            'latest' => $this->latestRows('videos', 'title', 'created_at', $params),
            'status' => $this->groupCount('videos', 'status', $params)
        ], $params);
    }

    public function announcements($params = [])
    {
        return $this->output([
            'summary' => [
                'announcements' => $this->safeCount('announcements', $params)
            ],
            'latest' => $this->latestRows('announcements', 'title', 'created_at', $params),
            'status' => $this->groupCount('announcements', 'status', $params)
        ], $params);
    }

    public function testimonials($params = [])
    {
        return $this->output([
            'summary' => [
                'testimonials' => $this->safeCount('testimonials', $params)
            ],
            'latest' => $this->latestRows('testimonials', 'testimonial_title', 'created_at', $params),
            'status' => $this->groupCount('testimonials', 'status', $params)
        ], $params);
    }

    public function peoples($params = [])
    {
        return $this->output([
            'summary' => [
                'peoples' => $this->safeCount('peoples', $params)
            ],
            'items' => $this->people($params),
            'status' => $this->groupCount('peoples', 'status', $params)
        ], $params);
    }

    private function latestBlogs($params = []): array
    {
        if (! $this->tableExists('blogs')) {
            return [];
        }

        $hasCategories = $this->tableExists('blogs_categories');
        $category = $hasCategories ? 'c.category_title AS category' : 'NULL AS category';

        $this->select("b.post_id AS id, b.post_title AS title, b.post_excerpt AS description, b.status, b.created_at AS timestamp, {$category}", false)
            ->from('blogs b');

        if ($hasCategories) {
            $this->join('blogs_categories c', 'c.category_id = b.post_category', 'left');
        }

        $this->applyPeriod('blogs', $params, 'b');

        return $this->orderBy('b.created_at', 'DESC')
            ->limit(20)
            ->get()
            ->result();
    }

    private function blogCategories($params = []): array
    {
        if (! $this->tableExists('blogs_categories')) {
            return [];
        }

        $hasBlogs = $this->tableExists('blogs');
        $postCount = $hasBlogs ? 'COUNT(b.post_id) AS post_count' : '0 AS post_count';

        $this->select("
            c.category_id AS id,
            c.category_title AS title,
            c.category_description AS description,
            c.status,
            {$postCount}
        ", false)
            ->from('blogs_categories c');

        if ($hasBlogs) {
            $this->join('blogs b', 'b.post_category = c.category_id', 'left');
        }

        $this->applyPeriod('blogs', $params, 'b');

        return $this->groupBy('c.category_id, c.category_title, c.category_description, c.status')
            ->orderBy('c.category_title', 'ASC')
            ->get()
            ->result();
    }

    private function people($params = []): array
    {
        if (! $this->tableExists('peoples')) {
            return [];
        }

        $hasFirstName = $this->fieldExists('first_name', 'peoples');
        $hasLastName = $this->fieldExists('last_name', 'peoples');
        $titleSelect = 'people_id AS title';
        if ($hasFirstName && $hasLastName) {
            $titleSelect = 'CONCAT(first_name, " ", last_name) AS title';
        } elseif ($hasFirstName) {
            $titleSelect = 'first_name AS title';
        } elseif ($this->fieldExists('title', 'peoples')) {
            $titleSelect = 'title';
        }

        $positionSelect = $this->fieldExists('position', 'peoples') ? 'position AS description' : 'NULL AS description';
        $statusSelect = $this->fieldExists('status', 'peoples') ? 'status' : '1 AS status';

        $this->select("
            people_id AS id,
            {$titleSelect},
            {$positionSelect},
            {$statusSelect}
        ", false)
            ->from('peoples');

        $this->applyPeriod('peoples', $params);

        return $this->orderBy('people_id', 'DESC')
            ->get()
            ->result();
    }

    private function blogBookPosts($params = []): array
    {
        if (! $this->tableExists('blogs')) {
            return [];
        }

        $hasFirstName = $this->fieldExists('first_name', 'app_users');
        $hasLastName = $this->fieldExists('last_name', 'app_users');
        $hasUsername = $this->fieldExists('username', 'app_users');
        $userSelect = '';
        if ($hasFirstName) {
            $userSelect .= ', u.first_name';
        }
        if ($hasLastName) {
            $userSelect .= ', u.last_name';
        }
        if ($hasUsername) {
            $userSelect .= ', u.username';
        }

        $this->select('
            b.post_id AS id,
            b.post_title AS title,
            b.post_excerpt AS excerpt,
            b.post_content AS content,
            b.post_tags AS tags,
            b.featured_image,
            b.created_at,
            b.updated_at,
            b.status,
            c.category_title AS category,
            c.category_slug
            ' . $userSelect)
            ->from('blogs b')
            ->join('blogs_categories c', 'c.category_id = b.post_category', 'left')
            ->join('app_users u', 'u.user_id = b.created_by', 'left');

        $this->applyPeriod('blogs', $params, 'b');

        if (! empty($params['category'])) {
            $this->where('b.post_category', (int) $params['category']);
        }

        return $this->orderBy('c.category_title', 'ASC')
            ->orderBy('b.created_at', 'ASC')
            ->orderBy('b.post_title', 'ASC')
            ->get()
            ->result();
    }

    private function latestRows(string $table, string $title, string $timestamp, $params = []): array
    {
        if (! ($this->tableExists($table) && $this->fieldExists($title, $table))) {
            return [];
        }

        $id = $this->primaryKey($table);
        $timeSelect = $this->fieldExists($timestamp, $table) ? "{$timestamp} AS timestamp" : 'NULL AS timestamp';
        $description = $this->descriptionColumn($table);
        $descriptionSelect = $description ? "{$description} AS description" : 'NULL AS description';
        $statusSelect = $this->fieldExists('status', $table) ? 'status' : 'NULL AS status';
        $order = $this->fieldExists($timestamp, $table) ? "{$timestamp} DESC" : "{$id} DESC";

        $this->select("
            {$id} AS id,
            {$title} AS title,
            {$descriptionSelect},
            {$statusSelect},
            {$timeSelect}
        ", false)
            ->from($table);

        $this->applyPeriod($table, $params);

        return $this->orderBy($order, '', false)
            ->limit(20)
            ->get()
            ->result();
    }

    private function statusOverview(?array $only = null, $params = []): array
    {
        $tables = [
            'pages' => 'Pages',
            'blogs' => 'Blogs',
            'galleries' => 'Galleries',
            'videos' => 'Videos',
            'announcements' => 'Announcements',
            'testimonials' => 'Testimonials',
            'peoples' => 'People'
        ];

        $overview = [];

        foreach ($tables as $table => $label) {
            if ($only && ! in_array($table, $only, true)) {
                continue;
            }

            $overview[] = [
                'table' => $table,
                'label' => $label,
                'total' => $this->safeCount($table, $params),
                'status' => $this->groupCount($table, 'status', $params)
            ];
        }

        return $overview;
    }

    private function output(array $results, $params = []): array
    {
        $period = $this->normalizePeriod($params);

        return [
            'header' => (object) [
                'generated_at' => date('Y-m-d H:i:s'),
                'date_start' => $period['date_start'],
                'date_end' => $period['date_end'],
                'author' => $this->authorName($params),
                'category' => $this->categoryName($params)
            ],
            'results' => $results
        ];
    }

    private function safeCount(string $table, $params = []): int
    {
        if (! $this->tableExists($table)) {
            return 0;
        }

        $this->applyPeriod($table, $params);

        return (int) $this->countAllResults($table);
    }

    private function countWhere(string $table, string $column, mixed $value, $params = []): int
    {
        if (! ($this->tableExists($table) && $this->fieldExists($column, $table))) {
            return 0;
        }

        $this->where($column, $value);
        $this->applyPeriod($table, $params);

        return (int) $this->countAllResults($table);
    }

    private function applyPeriod(string $table, $params = [], ?string $alias = null): void
    {
        $prefix = $alias ? $alias . '.' : '';

        if ($this->fieldExists('created_at', $table)) {
            $period = $this->normalizePeriod($params);

            $this->where($prefix . 'created_at >=', $period['date_start'] . ' 00:00:00');
            $this->where($prefix . 'created_at <=', $period['date_end'] . ' 23:59:59');
        }

        if ($this->fieldExists('created_by', $table) && $this->normalizeAuthor($params)) {
            $this->where($prefix . 'created_by', $this->normalizeAuthor($params));
        }
    }

    private function normalizePeriod($params = []): array
    {
        $dateStart = $params['date_start'] ?? date('Y-m-01');
        $dateEnd = $params['date_end'] ?? date('Y-m-d');

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $dateStart)) {
            $dateStart = date('Y-m-01');
        }

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $dateEnd)) {
            $dateEnd = date('Y-m-d');
        }

        if (strtotime($dateStart) > strtotime($dateEnd)) {
            [$dateStart, $dateEnd] = [$dateEnd, $dateStart];
        }

        return [
            'date_start' => $dateStart,
            'date_end' => $dateEnd
        ];
    }

    private function normalizeAuthor($params = []): mixed
    {
        $author = $params['author'] ?? null;

        return (! empty($author) && '0' !== $author) ? $author : null;
    }

    private function authorName($params = []): ?string
    {
        $author = $this->normalizeAuthor($params);

        if (! $author || ! $this->tableExists('app_users')) {
            return null;
        }

        $select = ['user_id'];
        if ($this->fieldExists('username', 'app_users')) {
            $select[] = 'username';
        }
        if ($this->fieldExists('first_name', 'app_users')) {
            $select[] = 'first_name';
        }
        if ($this->fieldExists('last_name', 'app_users')) {
            $select[] = 'last_name';
        }

        $where = is_numeric($author) ? ['user_id' => (int) $author] : ['username' => $author];

        $user = $this->table('app_users')
            ->select(implode(', ', $select))
            ->getWhere('', $where, 1)
            ->row();

        if (! $user) {
            return null;
        }

        $firstName = $user->first_name ?? '';
        $lastName = $user->last_name ?? '';
        $username = $user->username ?? '';
        $name = trim($firstName . ' ' . $lastName);

        return $name ?: ($username ?: null);
    }

    private function categoryName($params = []): ?string
    {
        $category = ! empty($params['category']) ? $params['category'] : null;

        if (! $category || ! $this->tableExists('blogs_categories')) {
            return null;
        }

        $where = is_numeric($category) ? ['category_id' => (int) $category] : ['category_slug' => $category];

        $row = $this->table('blogs_categories')
            ->select('category_title')
            ->getWhere('', $where, 1)
            ->row();

        return $row ? $row->category_title : (! is_numeric($category) ? $category : null);
    }

    private function groupCount(string $table, string $column, $params = []): array
    {
        if (! ($this->tableExists($table) && $this->fieldExists($column, $table))) {
            return [];
        }

        $this->select("{$column} AS value, COUNT(*) AS total", false)
            ->from($table);

        $this->applyPeriod($table, $params);

        $rows = $this->groupBy($column)
            ->get()
            ->result();

        $result = [];
        foreach ($rows as $row) {
            $key = (string) ($row->value ?? '0');
            $label = $this->statusLabels[$key] ?? $key;
            $result[] = [
                'value' => $key,
                'label' => $label,
                'total' => (int) ($row->total ?? 0)
            ];
        }

        return $result;
    }

    private function primaryKey(string $table): string
    {
        foreach (['id', 'page_id', 'post_id', 'category_id', 'gallery_id', 'video_id', 'announcement_id', 'testimonial_id', 'people_id', 'district_id', 'village_id'] as $key) {
            if ($this->fieldExists($key, $table)) {
                return $key;
            }
        }

        return 'id';
    }

    private function descriptionColumn(string $table): ?string
    {
        foreach (['description', 'page_description', 'post_excerpt', 'gallery_description', 'testimonial_content', 'content'] as $column) {
            if ($this->fieldExists($column, $table)) {
                return $column;
            }
        }

        return null;
    }
}
