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

namespace Aksara\Libraries;

/**
 * The PDF Library
 * This class is used to generate PDF's file
 */
ini_set('pcre.backtrack_limit', 99999999);
ini_set('memory_limit', '-1');

class Document
{
    /**
     * parameter
     *
     * @object
     */
    private $_params = [];

    /**
     * default page width
     *
     * @string
     */
    private $_pageWidth = '8.5in';

    /**
     * default page height
     *
     * @string
     */
    private $_pageHeight = '13in';

    /**
     * page orientation
     *
     * @string
     */
    private $_pageOrientation = 'portrait';

    /**
     * margin top
     *
     * @int
     */
    private $_pageMarginTop = 0;

    /**
     * margin right
     *
     * @int
     */
    private $_pageMarginRight = 0;

    /**
     * margin bottom
     *
     * @int
     */
    private $_pageMarginBottom = 0;

    /**
     * margin left
     *
     * @int
     */
    private $_pageMarginLeft = 0;

    public function __construct()
    {
    }

    public function generate($html = null, $filename = null, $method = 'embed', $params = [])
    {
        // Push parameter
        $this->_params = array_merge($this->_params, $params);

        // Default page width (better use "in" a.k.a inches)
        if (! isset($this->_params['page-width'])) {
            $this->_params['page-width'] = '8.5in';
        }

        // Default page height (better use "in" a.k.a inches)
        if (! isset($this->_params['page-height'])) {
            $this->_params['page-height'] = '13in';
        }

        // Default top margin of page
        if (! isset($this->_params['margin-top'])) {
            $this->_params['margin-top'] = 10;
        }

        // Default right margin of page
        if (! isset($this->_params['margin-right'])) {
            $this->_params['margin-right'] = 10;
        }

        // Default bottom margin of page
        if (! isset($this->_params['margin-bottom'])) {
            $this->_params['margin-bottom'] = 10;
        }

        // Default left margin of page
        if (! isset($this->_params['margin-left'])) {
            $this->_params['margin-left'] = 10;
        }

        if ('export' == strtolower($method)) {
            // Use excel generator
            // Online doc can be found in https://xxx.xx/
            return $this->_excel($html, $filename, $method, $this->_params);
        } elseif ('doc' == strtolower($method)) {
            // Use doc generator
            // Online doc can be found in https://xxx.xx/
            return $this->_word($html, $filename, $method, $this->_params);
        } else {
            // Use mPDF instead
            // Online doc can be found in https://mpdf.github.io/
            $output = $this->_mpdf($html, $filename, ('embed' == $method ? 'attach' : 'download'), $this->_params);

            service('response')->setContentType('application/pdf');

            return service('response')->setBody($output)->send();
        }
    }

    public function pageSize($width = '8.5in', $height = '13in')
    {
        // Explode to get initial setup
        $widthHeight = ($width ? array_map('trim', explode(' ', $width)) : []);

        if (2 == sizeof($widthHeight)) {
            // The page size and orientation is sets with units
            $this->_params['page-width'] = $widthHeight[0];
            $this->_params['page-height'] = $widthHeight[1];
        } elseif ('landscape' == strtolower($height)) {
            // The page size and orientation is sets with initial, ex: A4, landscape
            $this->_params['page-size'] = $width;
            $this->_params['orientation'] = $height;
        } else {
            // The page size and orientation is sets with initial, ex: A4, landscape
            $this->_params['page-width'] = $width;
            $this->_params['page-height'] = $height;
        }

        return $this;
    }

    public function pageMargin($top = 0, $right = 0, $bottom = 0, $left = 0)
    {
        // Hack the retard setup
        if ($top && ! $right && ! $bottom && ! $left) {
            // Margin of the edge is equal
            $this->_params['margin-top'] = $top;
            $this->_params['margin-right'] = $top;
            $this->_params['margin-bottom'] = $top;
            $this->_params['margin-left'] = $top;
        } elseif ($top && $right && ! $bottom && ! $left) {
            // Margin-top and bottom is equal, also margin-right and left
            $this->_params['margin-top'] = $top;
            $this->_params['margin-right'] = $right;
            $this->_params['margin-bottom'] = $top;
            $this->_params['margin-left'] = $right;
        } elseif ($top && $right && $bottom && ! $left) {
            // Only left margin is equal to the right margin
            $this->_params['margin-top'] = $top;
            $this->_params['margin-right'] = $right;
            $this->_params['margin-bottom'] = $bottom;
            $this->_params['margin-left'] = $right;
        } else {
            // All edge is used custom margin
            $this->_params['margin-top'] = $top;
            $this->_params['margin-right'] = $right;
            $this->_params['margin-bottom'] = $bottom;
            $this->_params['margin-left'] = $left;
        }

        return $this;
    }

    private function _mpdf($html = null, $filename = null, $method = 'embed', $params = [])
    {
        // Rendering mode
        $params['mode'] = 'utf-8';

        // Auto top margin
        $params['setAutoTopMargin'] = 'stretch';

        // Auto bottom margin
        $params['setAutoBottomMargin'] = 'stretch';

        // Use subtitutions
        $params['showImageErrors'] = true;

        // Use subtitutions
        $params['useSubstitutions'] = false;

        // Table proportions
        $params['keep_table_proportions'] = true;

        // Auto page break enabled
        $params['autoPageBreak'] = true;

        // Temporary folder
        $params['tempDir'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mpdf';

        // Used font
        $params['default_font'] = (isset($params['default_font']) ? $params['default_font'] : 'tahoma');

        // DPI
        $params['dpi'] = 80;

        /* check if page size is defined */
        if (isset($params['page-width']) && isset($params['page-height'])) {
            // Set the page size
            $params['format'] = [preg_replace('/[^0-9.]/', '', (float) $params['page-width']) * 25.4, (float) preg_replace('/[^0-9.]/', '', $params['page-height']) * 25.4];
        }

        // Load generator
        $pdf = new \Mpdf\Mpdf($params);

        // Render output
        $pdf->SetCreator('Aby Dahana (abydahana.github.io)');
        $pdf->SetAuthor('Aby Dahana (abydahana.github.io)');

        // Add watermark
        if (isset($params['setWatermarkText'])) {
            $pdf->SetWatermarkText($params['setWatermarkText']);
            $pdf->showWatermarkText = true;
        }
        if (isset($params['setWatermarkImage'])) {
            $pdf->SetWatermarkImage($params['setWatermarkImage']);
            $pdf->showWatermarkImage = true;
        }

        $pdf->WriteHTML($html);

        /**
         * Find attachment
         */
        preg_match_all('/<import src="(.*?)"/', $html, $attachment);

        if (isset($attachment[1]) && $attachment[1]) {
            ini_set('user_agent', 'spider');

            if (! is_dir(UPLOAD_PATH . '/tmp')) {
                // Create new directory
                mkdir(UPLOAD_PATH . '/tmp', 0755, true);
            }

            /**
             * Import attachment
             */
            foreach ($attachment[1] as $key => $val) {
                //$pdf->SetImportUse(); // Only with mPDF <8.0

                $filename = basename($val);

                try {
                    $file_content = copy(str_replace(base_url(), '', $val), UPLOAD_PATH . '/tmp' . '/' . $filename);
                    $pagecount = $pdf->SetSourceFile(UPLOAD_PATH . '/tmp' . '/' . $filename);

                    for ($i = 1; $i <= ($pagecount); $i++) {
                        $template_id = $pdf->ImportPage($i);
                        $size = $pdf->getTemplateSize($template_id);

                        $pdf->UseTemplate($template_id, 0, 0, $size['width'], $size['height'], true);

                        if ($i < $pagecount) {
                            $pdf->AddPage();
                        }
                    }

                    unlink(UPLOAD_PATH . '/tmp' . '/' . $filename);
                } catch (\Throwable $e) {
                    // Debug
                }
            }
        }

        if ('attach' == $method) {
            // Attach to email
            return $pdf->Output($filename . '.pdf', 'S');
        } elseif ('download' == $method) {
            // Download results
            return $pdf->Output($filename . '.pdf', 'D');
        } else {
            // Display to browser
            return $pdf->Output($filename . '.pdf', 'I');
        }
    }

    private function _excel($html = null, $filename = null, $method = 'embed', $params = [])
    {
        if (! $html) {
            // Safe check
            return false;
        }

        libxml_use_internal_errors(true);

        // Remove special tags
        $html = preg_replace('/<htmlpagefooter(.*)<\/htmlpagefooter>/iUs', '', preg_replace('/<htmlpageheader(.*)<\/htmlpageheader>/iUs', '', $html));

        // Load dom
        $dom = new \DOMDocument();
        $dom->encoding = 'UTF-8';
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        // Get only style element
        $styles = $dom->getElementsByTagName('style');
        $css = null;

        foreach ($styles as $style) {
            $css = $dom->saveHTML($style);
        }

        // Get only table element
        $tables = $dom->getElementsByTagName('table');
        $output = null;

        foreach ($tables as $table) {
            if ($table->getAttribute('class') !== 'table') {
                continue;
            }

            $output .= $dom->saveHTML($table);
        }

        $output = '<!DOCTYPE html><head><meta charset="UTF-8"><title>' . $filename . '</title>' . $css . '</head><body>' . $output . '</body></html>';

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($output);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', $filename) . '.xlsx"');

        $writer->save('php://output');
    }

    private function _word($html = null, $filename = null, $method = 'embed', $params = [])
    {
        $html = preg_replace('/<htmlpagefooter[^>].*?<\/htmlpagefooter>/s', '', $html);

        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: application/vnd.ms-word');
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', $filename) . '.doc"');

        echo $html;
    }
}
