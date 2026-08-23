<?php

/**
 * @var string $title
 * @var object $header
 * @var array $results
 */
$status = static fn ($items) => $items ? implode(', ', array_map(static fn ($item, $label) => is_array($item) ? $item['label'] . ': ' . number_format($item['total']) : $label . ': ' . number_format($item), $items, array_keys($items))) : '-';
$statusText = static fn ($value) => ['-1' => 'Deleted', '0' => 'Inactive', '1' => 'Active', '2' => 'Draft', '3' => 'Archived'][(string) $value] ?? (string) $value;

$sections = [
    [
        'title' => 'People',
        'headers' => ['#', 'Name', 'Position', 'Status'],
        'rows' => array_map(static fn ($row, $key) => [
            $key + 1,
            $row->title,
            $row->description,
            $statusText($row->status)
        ], $results['items'], array_keys($results['items']))
    ]
];

echo view('Aksara\Modules\Reports\Views\partials\simple', [
    'title' => $title,
    'header' => $header,
    'description' => phrase('People profile report.'),
    'summary' => [
        'People' => $results['summary']['peoples'],
        'Status' => $status($results['status'])
    ],
    'sections' => $sections
]);
