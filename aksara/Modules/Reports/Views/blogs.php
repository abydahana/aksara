<?php

/**
 * @var string $title
 * @var object $header
 * @var array $results
 */
$clip = static fn ($text, $length = 100) => strlen(trim(strip_tags((string) $text))) > $length ? substr(trim(strip_tags((string) $text)), 0, $length - 3) . '...' : (trim(strip_tags((string) $text)) ?: '-');
$status = static fn ($items) => $items ? implode(', ', array_map(static fn ($item, $label) => is_array($item) ? $item['label'] . ': ' . number_format($item['total']) : $label . ': ' . number_format($item), $items, array_keys($items))) : '-';
$statusText = static fn ($value) => ['-1' => 'Deleted', '0' => 'Inactive', '1' => 'Active', '2' => 'Draft', '3' => 'Archived'][(string) $value] ?? (string) $value;

$sections = [
    [
        'title' => 'Blog Categories',
        'headers' => ['#', 'Category', 'Description', 'Posts', 'Status'],
        'rows' => array_map(static fn ($row, $key) => [
            $key + 1,
            $row->title,
            $clip($row->description),
            number_format($row->post_count),
            $statusText($row->status)
        ], $results['categories'], array_keys($results['categories']))
    ],
    [
        'title' => 'Blog Posts',
        'headers' => ['#', 'Title', 'Category', 'Excerpt', 'Date', 'Status'],
        'rows' => array_map(static fn ($row, $key) => [
            $key + 1,
            $row->title,
            $row->category ?: '-',
            $clip($row->description),
            $row->timestamp ? date('d M Y H:i', strtotime($row->timestamp)) : '-',
            $statusText($row->status)
        ], $results['latest'], array_keys($results['latest']))
    ]
];

echo view('Aksara\Modules\Reports\Views\partials\simple', [
    'title' => $title,
    'header' => $header,
    'description' => phrase('Blog category and post report.'),
    'summary' => [
        'Posts' => $results['summary']['posts'],
        'Categories' => $results['summary']['categories'],
        'Status' => $status($results['status'])
    ],
    'sections' => $sections,
    'orientation' => 'landscape'
]);
