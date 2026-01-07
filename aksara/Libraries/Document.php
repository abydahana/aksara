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

use Config\Services;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html as WordHtml;
use PhpOffice\PhpWord\Style\Section;
use Throwable;

class Document
{
    /**
     * parameter
     *
     * @object
     */
    private $_params = [];

    public function __construct()
    {
        // Nothing to do
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
        } elseif ('docx' == strtolower($method)) {
            // Use doc generator
            // Online doc can be found in https://xxx.xx/
            return $this->_word($html, $filename, $method, $this->_params);
        } else {
            $response = Services::response();

            // Use mPDF instead
            // Online doc can be found in https://mpdf.github.io/
            $output = $this->_mpdf($html, $filename, ('embed' == $method ? 'attach' : 'download'), $this->_params);

            $response->setContentType('application/pdf');

            return $response->setBody($output)->send();
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

        // Chinese, Japan, Korean and Thai font support
        $params['autoScriptToLang'] = true; // Auto-detect script and language
        $params['autoLangToFont'] = true;   // Auto-select font based on language
        $params['cjk'] = true;              // Enable CJK font support

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

        // Check if page size is defined
        if (isset($params['page-width']) && isset($params['page-height'])) {
            // Set the page size
            $params['format'] = [preg_replace('/[^0-9.]/', '', (float) $params['page-width']) * 25.4, (float) preg_replace('/[^0-9.]/', '', $params['page-height']) * 25.4];
        }

        // Load generator
        $pdf = new Mpdf($params);

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

        // Find attachment
        preg_match_all('/<import src="(.*?)"/', $html, $attachment);

        if (isset($attachment[1]) && $attachment[1]) {
            ini_set('user_agent', 'spider');

            if (! is_dir(UPLOAD_PATH . '/tmp')) {
                // Create new directory
                mkdir(UPLOAD_PATH . '/tmp', 0755, true);
            }

            // Import attachment
            foreach ($attachment[1] as $key => $val) {
                $filename = basename($val);

                try {
                    // Copy attachment source
                    copy(str_replace(base_url(), '', $val), UPLOAD_PATH . '/tmp' . '/' . $filename);

                    // Read attachment
                    $pagecount = $pdf->SetSourceFile(UPLOAD_PATH . '/tmp' . '/' . $filename);

                    for ($i = 1; $i <= ($pagecount); $i++) {
                        $templateId = $pdf->ImportPage($i);
                        $size = $pdf->getTemplateSize($templateId);

                        $pdf->UseTemplate($templateId, 0, 0, $size['width'], $size['height'], true);

                        if ($i < $pagecount) {
                            // Add attachment to page
                            $pdf->AddPage();
                        }
                    }

                    unlink(UPLOAD_PATH . '/tmp' . '/' . $filename);
                } catch (Throwable $e) {
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
        if (empty($html) || empty($filename)) {
            throw new \InvalidArgumentException('HTML and filename are required');
        }

        libxml_use_internal_errors(true);

        // Remove special tags (htmlpagefooter, htmlpageheader)
        $html = preg_replace('/<htmlpagefooter(.*)<\/htmlpagefooter>/iUs', '', preg_replace('/<htmlpageheader(.*)<\/htmlpageheader>/iUs', '', $html));

        // Load HTML content into DOM
        $dom = new \DOMDocument();
        $dom->encoding = 'UTF-8';

        $dom->loadHTML(mb_encode_numericentity($html, [0x80, 0x10ffff, 0, 0x1fffff], 'UTF-8'));

        libxml_clear_errors();

        // Get and concatenate existing style elements
        $styles = $dom->getElementsByTagName('style');
        $css = '';

        foreach ($styles as $style) {
            $css .= $dom->saveHTML($style);
        }

        // Get and concatenate target table elements (class="table")
        $tables = $dom->getElementsByTagName('table');
        $output = '';

        foreach ($tables as $table) {
            // Filter: only process tables with class 'table'
            if ($table->getAttribute('class') !== 'table') {
                continue;
            }

            $output .= $dom->saveHTML($table);
        }

        // Check if any tables were extracted
        if (empty($output)) {
            // No tables found with the required class
            error_log('EXCEL EXPORT: No tables with class "table" found.');
            return false;
        }


        // Construct final HTML string for the PhpSpreadsheet HTML Reader
        $htmlForReader = '<!DOCTYPE html><head><meta charset="UTF-8"><title>' . $filename . '</title>' . $css . '</head><body>' . $output . '</body></html>';

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlForReader);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

        // --- EXPLICIT BORDER IMPLEMENTATION ---
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Determine the range of cells containing the imported table data
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $range = 'A1:' . $highestColumn . $highestRow;

        // 2. Define the border style array
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    // Use THIN border style
                    'borderStyle' => Border::BORDER_THIN, // Tambahkan namespace penuh di sini
                    // Use Black color
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        // 3. Apply the style to the entire data range (for borders)
        $sheet->getStyle($range)->applyFromArray($styleArray);
        // --- END BORDER IMPLEMENTATION ---

        // --- BOLD HEADER IMPLEMENTATION START ---
        // 1. Define the bold style array
        $boldStyle = [
            'font' => [
                'bold' => true,
            ],
        ];

        // 2. Determine the header range (Row 1, from A1 to the highest column)
        $headerRange = 'A1:' . $highestColumn . '1';

        // 3. Apply the bold style to the entire first row
        $sheet->getStyle($headerRange)->applyFromArray($boldStyle);
        // --- BOLD HEADER IMPLEMENTATION END ---

        // Use an Exception block to catch issues during file writing
        try {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename) . '.xlsx';

            // Set required HTTP headers for file download
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $safeFilename . '"');

            // Clear any premature output content before writing the binary file
            if (ob_get_length() > 0) {
                ob_clean();
            }

            $writer->save('php://output');

            // Terminate script execution after file is sent
            exit;
        } catch (Throwable $e) {
            // Log the critical error
            error_log('PhpSpreadsheet Write Error: ' . $e->getMessage());

            // Clean output buffer to prevent corrupted response
            if (ob_get_level() > 0) {
                ob_clean();
            }

            return false;
        }
    }

    private function _word($html = null, $filename = null, $method = 'embed', $params = [])
    {
        // --- 0. Configuration & Default Parameters ---

        // Define the core conversion ratio (1 mm = 1440 TWIP / 25.4 mm)
        // We define this locally as a fallback since Settings::MM_TO_TWIP is missing.
        $MMToTwipValue = 1440 / 25.4;

        // Set default values if not present
        if (! isset($params['page-width'])) {
            $params['page-width'] = '8.5in'; // Default to 8.5 inches
        }
        if (! isset($params['page-height'])) {
            $params['page-height'] = '13in'; // Default to 13 inches
        }
        // Margins (Assuming 10mm)
        if (! isset($params['margin-top'])) {
            $params['margin-top'] = 10;
        }
        if (! isset($params['margin-right'])) {
            $params['margin-right'] = 10;
        }
        if (! isset($params['margin-bottom'])) {
            $params['margin-bottom'] = 10;
        }
        if (! isset($params['margin-left'])) {
            $params['margin-left'] = 10;
        }

        // 1. Safety Check and HTML Preprocessing
        if (! $html) {
            return false;
        }

        $html = preg_replace('/<htmlpagefooter[^>].*?<\/htmlpagefooter>/s', '', $html);

        // 2. Initialize PhpWord
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Calibri');
        $phpWord->setDefaultFontSize(11);

        // --- APPLY SECTION STYLE (PAGESIZE & MARGINS) ---

        // Parse dimensions and convert to TWIP
        preg_match('/([0-9.]+)([a-z]+)/i', $params['page-width'], $widthMatches);
        preg_match('/([0-9.]+)([a-z]+)/i', $params['page-height'], $heightMatches);

        // Calculate TWIP values using the helper function and passing the ratio
        $pageWidthTWIP = $this->_convertToTwip($widthMatches[1] ?? 0, $widthMatches[2] ?? '', $MMToTwipValue);
        $pageHeightTWIP = $this->_convertToTwip($heightMatches[1] ?? 0, $heightMatches[2] ?? '', $MMToTwipValue);

        // Define the Section Style Array
        $sectionStyle = [
            // Page Size (Converted to TWIP)
            'pageSizeW' => $pageWidthTWIP,
            'pageSizeH' => $pageHeightTWIP,

            // Margins (Multiplying millimeter value by the calculated TWIP ratio)
            'marginTop' => $params['margin-top'] * $MMToTwipValue,
            'marginRight' => $params['margin-right'] * $MMToTwipValue,
            'marginBottom' => $params['margin-bottom'] * $MMToTwipValue,
            'marginLeft' => $params['margin-left'] * $MMToTwipValue,

            // Auto Orientation
            'orientation' => ($pageWidthTWIP > $pageHeightTWIP) ? Section::ORIENTATION_LANDSCAPE : Section::ORIENTATION_PORTRAIT,
        ];

        // Add a section with the defined style
        $section = $phpWord->addSection($sectionStyle);
        // --- END APPLY SECTION STYLE ---

        // 3. Table Style with Border
        $tableStyle = [
            'borderColor' => '000000',
            'borderSize' => 6,  // in half-points (6 = 3pt)
            'cellMargin' => 80, // padding in TWIP
        ];

        $phpWord->addTableStyle('TableWithBorder', $tableStyle);

        // 4. Import HTML Content
        WordHtml::addHtml($section, $html, false, false);

        // 5. Set Headers for DOCX Download
        $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename) . '.docx';

        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $safeFilename . '"');

        if (ob_get_length() > 0) {
            ob_clean();
        }

        // 6. Create Writer and Output File
        try {
            $writer = WordIOFactory::createWriter($phpWord, 'Word2007');
            $writer->save('php://output');

            exit;
        } catch (Throwable $e) {
            error_log('PhpWord Write Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Converts a dimension value from a unit (in, mm, cm) to TWIP.
     * @param float $value The numerical value.
     * @param string $unit The unit (in, mm, cm).
     * @param float $mmTwipRatio The calculated ratio of MM to TWIP (e.g., 1440/25.4).
     * @return int The value in TWIP.
     */
    private function _convertToTwip($value, $unit, $mmTwipRatio)
    {
        $value = (float) $value;
        $unit = strtolower($unit);

        switch ($unit) {
            case 'in': // Inches to TWIP
                return round($value * 1440);
            case 'mm': // Millimeters to TWIP (Uses the passed ratio)
                return round($value * $mmTwipRatio);
            case 'cm': // Centimeters to TWIP (1 cm = 10 mm)
                return round($value * 10 * $mmTwipRatio);
            default:
                return round($value);
        }
    }
}
