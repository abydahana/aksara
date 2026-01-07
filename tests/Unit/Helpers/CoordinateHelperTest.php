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

namespace Tests\Unit\Helpers;

use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;

class CoordinateHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (! function_exists('geojson2png')) {
            require_once APPPATH . 'Helpers/coordinate_helper.php';
        }
        // Mock get_setting if possible, or stub it if it was a service.
        // Since it's a function from Common.php calling new Model(), we might have issues if DB not set.
        // But let's see if it runs.
    }

    public function testGeojson2PngInvalid()
    {
        $this->assertEquals('', geojson2png('invalid json'));
    }

    public function testGeojson2PngPoint()
    {
        $json = json_encode([
            'lat' => -6.2,
            'lng' => 106.8
        ]);

        $url = geojson2png($json);
        $this->assertStringContainsString('maps.googleapis.com', $url);
        $this->assertStringContainsString('-6.2,106.8', $url);
    }

    public function testGeojson2PngFeatures()
    {
        $feature = [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [106.8, -6.2] // GeoJSON is Lng, Lat
                    ]
                ]
            ]
        ];

        $url = geojson2png(json_encode($feature));
        $this->assertStringContainsString('maps.googleapis.com', $url);
        // Google Static Maps expects Lat,Lng so -6.2,106.8
        $this->assertStringContainsString('-6.2,106.8', $url);
    }
}
