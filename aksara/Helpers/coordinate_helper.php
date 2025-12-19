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

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

if (! function_exists('geojson2png')) {
    /**
     * Convert GeoJSON data to a Google Static Maps URL
     */
    function geojson2png(string $geojson = '[]', string $stroke_color = '#ff0000', string $fill_color = '#ff0000', int $width = 600, int $height = 600): string
    {
        $data = json_decode($geojson);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return '';
        }

        $paths = [];
        $markers = [];

        // Helper to format hex for Google API (removes # and prepends 0x)
        $clean_stroke = '0x' . ltrim($stroke_color, '#');
        $clean_fill = '0x' . ltrim($fill_color, '#');

        if (isset($data->features) && is_array($data->features)) {
            foreach ($data->features as $feature) {
                $type = $feature->geometry->type ?? '';
                $coords = $feature->geometry->coordinates ?? [];

                if (in_array($type, ['LineString', 'MultiLineString', 'Polygon', 'MultiPolygon'])) {
                    $prefix = ('Polygon' === $type || 'MultiPolygon' === $type)
                        ? "path=color:{$clean_stroke}|weight:1|fillcolor:{$clean_fill}"
                        : "path=color:{$clean_stroke}99|weight:1";

                    $points = [];
                    // Flatten coordinates regardless of nesting depth
                    $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($coords));
                    $temp_coords = [];
                    foreach ($iterator as $v) {
                        $temp_coords[] = $v;
                        if (count($temp_coords) === 2) {
                            // GeoJSON is [lng, lat], Google Static Maps is [lat, lng]
                            $points[] = $temp_coords[1] . ',' . $temp_coords[0];
                            $temp_coords = [];
                        }
                    }

                    if (! empty($points)) {
                        $paths[] = $prefix . '|' . implode('|', $points);
                    }
                } elseif (in_array($type, ['Point', 'MultiPoint'])) {
                    $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($coords));
                    $temp_coords = [];
                    foreach ($iterator as $v) {
                        $temp_coords[] = $v;
                        if (count($temp_coords) === 2) {
                            $markers[] = $temp_coords[1] . ',' . $temp_coords[0];
                            $temp_coords = [];
                        }
                    }
                }
            }
        } elseif (isset($data->lat, $data->lng)) {
            $markers[] = $data->lat . ',' . $data->lng;
        }

        // Build URL
        $params = [
            'key' => get_setting('openlayers_search_key'),
            'size' => $width . 'x' . $height,
            'sensor' => 'false'
        ];

        $url = 'https://maps.googleapis.com/maps/api/staticmap?' . http_build_query($params);

        if (! empty($markers)) {
            $url .= '&markers=scale:1|' . implode('|', $markers);
        }

        if (! empty($paths)) {
            foreach ($paths as $path) {
                $url .= '&' . $path;
            }
        }

        return $url;
    }
}
