<?php

/**
 * @var string $title
 * @var object $header
 * @var array $results
 */
$status = static fn ($items) => $items ? implode(', ', array_map(static fn ($item, $label) => is_array($item) ? $item['label'] . ': ' . number_format($item['total']) : $label . ': ' . number_format($item), $items, array_keys($items))) : '-';

$sections = [
    [
        'title' => 'Content Status',
        'headers' => ['#', 'Content', 'Total', 'Status Summary'],
        'rows' => array_map(static fn ($row, $key) => [
            $key + 1,
            $row['label'],
            number_format($row['total']),
            $status($row['status'])
        ], $results['status'], array_keys($results['status']))
    ]
];

echo view('Aksara\Modules\Reports\Views\partials\simple', [
    'title' => $title,
    'header' => $header,
    'description' => phrase('Summary of all user-facing project contents.'),
    'summary' => [
        'Pages' => $results['summary']['pages'],
        'Posts' => $results['summary']['posts'],
        'Media' => $results['summary']['galleries'] + $results['summary']['videos']
    ],
    'sections' => $sections
]);
