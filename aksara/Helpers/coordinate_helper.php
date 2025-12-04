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

/**
 * This file is part of Aksara CMS, both framework and publishing
 * platform.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
function geojson2png($geojson = '[]', $stroke_color = '#ff0000', $fill_color = '#ff0000', $width = 600, $height = 600)
{
    $geojson = json_decode($geojson);
    $paths = '';
    $markers = '';

    if (isset($geojson->features)) {
        foreach ($geojson->features as $key => $val) {
            $properties = [];

            if (in_array($val->geometry->type, ['LineString', 'MultiLineString'])) {
                $paths .= '&path=color:0x' . str_replace('#', '', $stroke_color) . '99|weight:1';

                foreach ($val->geometry->coordinates as $_key => $_val) {
                    if (is_array($_val)) {
                        foreach ($_val as $__key => $__val) {
                            $paths .= '|' . $__val[1] . ',' . $__val[0];
                        }
                    } else {
                        $paths .= '|' . $_val[1] . ',' . $_val[0];
                    }
                }
            } elseif (in_array($val->geometry->type, ['Polygon', 'MultiPolygon'])) {
                $paths .= '&path=color:0x' . str_replace('#', '', $stroke_color) . '|weight:1|fillcolor:0x' . str_replace('#', '', $fill_color);

                foreach ($val->geometry->coordinates as $_key => $_val) {
                    foreach ($_val as $__key => $__val) {
                        if (is_array($__val)) {
                            $paths .= '|' . $__val[1] . ',' . $__val[0];
                        }
                    }
                }
            } elseif (in_array($val->geometry->type, ['Point', 'MultiPoint'])) {
                $markers .= '&markers=scale:1';
                $markers .= '|' . $val->geometry->coordinates[1] . ',' . $val->geometry->coordinates[0];
            }
        }
    } elseif (isset($geojson->lat) && isset($geojson->lng)) {
        $markers .= '&markers=scale:1';
        $markers .= '|' . $geojson->lat . ',' . $geojson->lng;
    }

    $params = [
        'key' => get_setting('openlayers_search_key'),
        'zoom' => '',
        'size' => $width . 'x' . $height
    ];

    return 'http://maps.googleapis.com/maps/api/staticmap?' . http_build_query($params) . $markers . $paths;
}
