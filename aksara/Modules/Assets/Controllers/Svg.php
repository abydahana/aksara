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

namespace Aksara\Modules\Assets\Controllers;

use Aksara\Laboratory\Core;

class Svg extends Core
{
    private $_fill;
    private $_stroke;

    public function __construct()
    {
        parent::__construct();

        $this->_fill = $this->_validate_hex($this->request->getGet('fill'));
        $this->_stroke = $this->_validate_hex($this->request->getGet('stroke'));
    }

    public function index()
    {
        return throw_exception(404, phrase('The page you requested does not exist or already been archived.'), base_url());
    }

    public function point()
    {
        $output = '<?xml version="1.0" encoding="UTF-8"?><svg width="32" height="32" viewBox="-4 0 36 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><title>map-marker</title><g id="Vivid.JS" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="Vivid-Icons" transform="translate(-125.000000, -643.000000)"><g id="Icons" transform="translate(37.000000, 169.000000)"><g id="map-marker" transform="translate(78.000000, 468.000000)"><g transform="translate(10.000000, 6.000000)"><path d="M14,0 C21.732,0 28,5.641 28,12.6 C28,23.963 14,36 14,36 C14,36 0,24.064 0,12.6 C0,5.641 6.268,0 14,0 Z" id="Shape" fill="' . $this->_stroke . '"></path><circle id="Oval" fill="' . $this->_fill . '" fill-rule="nonzero" cx="14" cy="14" r="7"></circle></g></g></g></g></g></svg>';

        $this->response->setContentType('image/svg+xml');
        $this->response->setBody($output);
        $this->response->send();
    }

    public function polygon()
    {
        $output = '<?xml version="1.0" encoding="utf-8"?><svg version="1.1" id="Layer_1" height="32" width="32" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" fill="#ff0054" xmlns="http://www.w3.org/2000/svg"><path style="fill: ' . $this->_fill . '; stroke: ' . $this->_stroke . '; stroke-width: 4; stroke-miterlimit: 4; stroke-dasharray: none; fill-opacity: 1;" d="M 214.326 445.556 C 114.238 418.398 31.84 395.592 31.22 394.874 C 30.31 393.822 30.302 393.253 31.181 391.934 C 31.78 391.035 59.327 366.53 92.397 337.48 C 125.467 308.429 152.518 284.211 152.511 283.661 C 152.504 283.111 120.225 267.039 80.78 247.946 C 41.336 228.853 8.749 212.653 8.364 211.946 C 7.558 210.463 177.793 12.877 179.305 13.542 C 181.065 14.317 500.085 282.35 501.167 283.963 C 501.772 284.865 502.052 286.253 501.788 287.047 C 500.503 290.921 400.648 492.828 399.482 493.911 C 398.742 494.598 397.724 495.11 397.22 495.047 C 396.717 494.985 314.414 472.714 214.326 445.556 Z"/></svg>';

        $this->response->setContentType('image/svg+xml');
        $this->response->setBody($output);
        $this->response->send();
    }

    public function linestring()
    {
        $output = '<?xml version="1.0" encoding="utf-8"?><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="32" width="32" x="0px" y="0px" viewBox="0 0 22.138 22.138" style="enable-background:new 0 0 22.138 22.138;" xml:space="preserve"><g><path style="fill:' . $this->_fill . ';" d="M21.061,3.804c-0.594,0-1.076,0.482-1.076,1.077c0,0.124,0.024,0.241,0.063,0.352l-3.154,4.694 l-3.897-3.753c0.014-0.07,0.021-0.142,0.021-0.216c0-0.595-0.482-1.077-1.077-1.077c-0.594,0-1.077,0.482-1.077,1.077 c0,0.533,0.389,0.972,0.898,1.058l2.544,6.399l-2.94-1.446c0.003-0.034,0.01-0.067,0.01-0.102c0-0.595-0.482-1.077-1.077-1.077 c-0.595,0-1.077,0.482-1.077,1.077c0,0.32,0.143,0.604,0.365,0.802l-0.571,3.533c-0.269,0.052-0.5,0.205-0.657,0.418l-6.222-1.682 c-0.08-0.515-0.522-0.911-1.06-0.911C0.483,14.028,0,14.51,0,15.104s0.482,1.077,1.077,1.077c0.236,0,0.452-0.078,0.629-0.206 l6.564,1.773c0.179,0.347,0.537,0.585,0.953,0.585c0.595,0,1.077-0.481,1.077-1.076c0-0.244-0.084-0.466-0.221-0.646l0.604-3.738 l3.706,1.824c0.07,0.525,0.517,0.933,1.063,0.933c0.595,0,1.077-0.481,1.077-1.076c0-0.566-0.439-1.025-0.995-1.068l-2.141-5.384 l3.208,3.088c0.119,0.114,0.278,0.171,0.445,0.152c0.164-0.018,0.312-0.106,0.403-0.243l3.464-5.156  c0.049,0.007,0.099,0.015,0.148,0.015c0.595,0,1.077-0.482,1.077-1.077C22.138,4.286,21.656,3.804,21.061,3.804z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';

        $this->response->setContentType('image/svg+xml');
        $this->response->setBody($output);
        $this->response->send();
    }

    public function folder()
    {
        $output = '<?xml version="1.0" encoding="utf-8"?><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="80" width="80" x="0px" y="0px" viewBox="0 0 96 96" style="enable-background:new 0 0 96 96;" xml:space="preserve"><g id="XMLID_1_"><path id="XMLID_3_" style="fill:#2980B9" d="M20.9,29c-3,0-5.4,2.4-5.4,5.4v37.9c0,3,2.4,5.4,5.4,5.4h54.2c3,0,5.4-2.4,5.4-5.4V34.5 c0-3-2.4-5.4-5.4-5.4H20.9z"/><path id="XMLID_4_" style="fill:#2980B9" d="M23.6,18.2c-3,0-5.4,2.4-5.4,5.4v37.9c0,3,2.4,5.4,5.4,5.4h29.8H67h5.4c3,0,5.4-2.4,5.4-5.4 V37.2V29c0-3-2.4-5.4-5.4-5.4H67H53.4h-2.7l-8.1-5.4H23.6z"/><path id="XMLID_5_" style="fill:#BDC3C7" d="M77.8,53.4V31.8c0-3-2.4-5.4-5.4-5.4H42.6H29h-5.4c-3,0-5.4,2.4-5.4,5.4v21.7H77.8z"/><path id="XMLID_6_" style="fill:#3498DB" d="M20.9,29c-3,0-5.4,2.4-5.4,5.4v16.3v2.7v16.2c0,3,2.4,5.4,5.4,5.4h54.2c3,0,5.4-2.4,5.4-5.4 V53.4v-2.7V34.5c0-3-2.4-5.4-5.4-5.4H20.9z"/></g></svg>';

        $this->response->setContentType('image/svg+xml');
        $this->response->setBody($output);
        $this->response->send();
    }

    public function back_arrow()
    {
        $output = '<?xml version="1.0" encoding="utf-8"?><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="80" width="80" x="0px" y="0px" viewBox="0 0 368 368" style="enable-background:new 0 0 368 368;" xml:space="preserve"><path style="fill:#CCE4FF;" d="M184,8c97.2,0,176,78.8,176,176s-78.8,176-176,176S8,281.2,8,184S86.8,8,184,8z M296,208v-48c0-4.4-3.6-8-8-8H184v-48L72,184l112,80v-48h104C292.4,216,296,212.4,296,208z"/><g><path style="fill:#007AFF;" d="M184,0C82.536,0,0,82.544,0,184s82.536,184,184,184s184-82.544,184-184S285.464,0,184,0z M184,352c-92.632,0-168-75.36-168-168S91.368,16,184,16s168,75.36,168,168S276.632,352,184,352z"/><path style="fill:#007AFF;" d="M288,144h-96v-40c0-3-1.672-5.744-4.336-7.112c-2.672-1.376-5.88-1.136-8.312,0.6l-112,80C65.248,178.992,64,181.416,64,184c0,2.584,1.248,5.008,3.352,6.512l112,80C180.736,271.496,182.36,272,184,272c1.248,0,2.504-0.296,3.664-0.888C190.328,269.744,192,267,192,264v-40h96c8.824,0,16-7.176,16-16v-48C304,151.176,296.824,144,288,144z M288,208H184c-4.424,0-8,3.584-8,8v32.456L85.768,184L176,119.544V152c0,4.416,3.576,8,8,8h104V208z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';

        $this->response->setContentType('image/svg+xml');
        $this->response->setBody($output);
        $this->response->send();
    }

    private function _validate_hex($hexColor = '')
    {
        if (preg_match('/#([a-f0-9]{3}){1,2}\b/i', $hexColor)) {
            return $hexColor;
        }

        return '#ff0000';
    }
}
