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

namespace Aksara\Laboratory;

use DateTime;
use Throwable;
use Config\Mimes;
use Config\Services;
use CodeIgniter\Files\FileSizeUnit;
use Aksara\Laboratory\Model;

class Validation
{
    private array $_uploadedFiles = [];

    private string $_uploadError = '';

    public function __construct()
    {
        // No initialization needed
    }

    /**
     * Check if data is already exist in the database table.
     * Similar to is_unique but more advanced with additional conditions.
     *
     * @param mixed|null $value The field value to check
     * @param string|null $params Database table and field parameters (format: table.field,where_field,where_value,...)
     * @param array<string, mixed> $data Complete data array being validated
     * @return bool True if unique, false if exists
     */
    public function unique($value = null, ?string $params = null, array $data = []): bool
    {
        // Normalize parameter separators: Support both comma and dot formats
        if (strpos($params, ',') !== false) {
            $parts = explode(',', $params);
            $tableField = explode('.', $parts[0]);
            $params = array_merge($tableField, array_slice($parts, 1));
        } else {
            $params = explode('.', $params);
        }

        if ($params) {
            $model = new Model();

            if (isset($_ENV['DBDriver'])) {
                $model->databaseConfig($_ENV);
            }

            $sliced = array_slice($params, 2, sizeof($params));
            $where = [];

            foreach ($sliced as $key => $val) {
                if ($key % 2 === 0) {
                    $where[$val] = (isset($sliced[$key + 1]) ? $sliced[$key + 1] : '');
                }
            }

            $num = 0;

            foreach ($where as $key => $val) {
                // If the value is a placeholder like (field_name), extract it securely from the request data
                if (is_string($val) && preg_match('/^\((.*)\)$/', $val, $match)) {
                    $fieldName = $match[1];
                    if (array_key_exists($fieldName, $data)) {
                        $val = $data[$fieldName];
                    } else {
                        $req = service('request');
                        $val = $req->getPost($fieldName) ?? $req->getGet($fieldName) ?? $val;
                    }
                }

                if (! $val && ! is_numeric($val)) {
                    $num++;
                    continue;
                }

                if (! $num) {
                    $model->where($key . ' != ', $val);
                } else {
                    $model->where($key, $val);
                }

                $num++;
            }

            // Final check: Select the field and execute the query with the main validation condition
            // (table.field = $value) AND the custom WHERE conditions applied above.
            // Returns TRUE if the number of resulting rows is 0 (meaning the value is unique), FALSE otherwise.
            return $model->select($params[1])->getWhere($params[0], [$params[1] => $value])->numRows() === 0;
        }

        // Returns FALSE if $params is empty (e.g., validator parameter was not properly formatted or missing).
        return false;
    }

    /**
     * Check if field is valid boolean (0 or 1).
     *
     * @param mixed|null $value The value to check
     * @return bool True if valid boolean, false otherwise
     */
    public function boolean($value = null): bool
    {
        if (null != $value && 1 != $value) {
            return false;
        }

        return true;
    }

    /**
     * Check if field is valid currency format.
     *
     * @param mixed|null $value The value to check
     * @return bool True if valid currency, false otherwise
     */
    public function currency($value = null): bool
    {
        if (! preg_match('/^\s*[$]?\s*((\d+)|(\d{1,3}(\,\d{3})+))(\.\d{2})?\s*$/', $value)) {
            return false;
        }

        return true;
    }

    /**
     * Check if field is valid date.
     *
     * @param mixed|null $value The value to check
     * @return bool True if valid date, false otherwise
     */
    public function valid_date($value = null): bool
    {
        // Convert value to standardzitation
        $value = date('Y-m-d', strtotime($value));

        $valid_date = DateTime::createFromFormat('Y-m-d', $value);

        if (! $valid_date || ($valid_date && $valid_date->format('Y-m-d') !== $value)) {
            return false;
        }

        return true;
    }

    /**
     * Check if field is valid time (HH:MM format).
     *
     * @param mixed|null $value The value to check
     * @return bool True if valid time, false otherwise
     */
    public function valid_time($value = null): bool
    {
        //Assume $value SHOULD be entered as HH:MM
        list($hh, $mm) = array_pad(explode(':', $value), 2, '00');

        if (! is_numeric($hh) || ! is_numeric($mm) || (int) $hh > 24 || (int) $mm > 59 || mktime((int) $hh, (int) $mm) === false) {
            return false;
        }

        return true;
    }

    /**
     * Check if field is valid date and time.
     *
     * @param mixed|null $value The value to check
     * @return bool True if valid datetime, false otherwise
     */
    public function valid_datetime($value = null): bool
    {
        // Convert value to standardzitation
        $value = date('Y-m-d H:i:s', strtotime($value));

        $valid_datetime = DateTime::createFromFormat('Y-m-d H:i:s', $value);

        if (! $valid_datetime || ($valid_datetime && $valid_datetime->format('Y-m-d H:i:s') !== $value)) {
            return false;
        }

        return true;
    }

    /**
     * Check if field is valid year (between 1970 and 2100).
     *
     * @param mixed|null $value The value to check
     * @return bool True if valid year, false otherwise
     */
    public function valid_year($value = null): bool
    {
        $valid_year = range(1970, 2100);

        if (! in_array($value, $valid_year)) {
            return false;
        }

        return true;
    }

    /**
     * Check if field is valid hex color code.
     *
     * @param mixed|null $value The value to check
     * @return bool True if valid hex color, false otherwise
     */
    public function valid_hex($value = null): bool
    {
        if (! preg_match('/#([a-f0-9]{3}){1,2}\b/i', $value)) {
            return false;
        }

        return true;
    }

    /**
     * Check if value exists in related database table.
     *
     * @param mixed $value The value to check
     * @param string|null $params Table and field parameters (format: table.field)
     * @return bool True if relation exists, false otherwise
     */
    public function relation_checker($value = 0, ?string $params = null): bool
    {
        $model = new Model();

        if (isset($_ENV['DBDriver'])) {
            // Cross database connection
            $model->databaseConfig($_ENV);
        }

        list($table, $field) = array_pad(explode('.', $params), 2, null);

        if (strpos($table, ' ') !== false) {
            $table = substr($table, 0, strrpos($table, ' '));
        }

        if (! $model->tableExists($table) || ! $model->fieldExists($field, $table) || ! $model->select($field)->getWhere($table, [$field => $value])->row($field)) {
            return false;
        }

        return true;
    }

    /**
     * Validate and process file uploads.
     * Handles single and multiple file uploads with image processing.
     *
     * @param mixed|null $value Not used, required for validation callback
     * @param string|null $params Field name and file type (format: field.type)
     * @return bool Always returns true, sets validation errors if needed
     */
    public function validate_upload($value = null, ?string $params = null): bool
    {
        $request = Services::request();
        $validation = Services::validation();

        list($field, $type) = array_pad(explode('.', $params), 2, null);

        $files = $request->getFile($field) ?? $request->getFileMultiple($field);

        if (is_array($files)) {
            foreach ($files as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $_key => $_val) {
                        // Typically using nested input like field[foo][bar]
                        $this->_doUpload($field . '.' . $key . '.' . $_key, $field, $type, $key, $_key);
                    }
                } else {
                    // Typically using nested input like field[foo]
                    $this->_doUpload($field . '.' . $key, $field, $type, $key);
                }
            }
        } else {
            $this->_doUpload($field, $field, $type);
        }

        if ($this->_uploadError) {
            // Validation error
            $validation->setError($field, $this->_uploadError);

            //return false;
        } elseif (! $this->_uploadedFiles) {
            // Find required
            $rules = $validation->getRules();

            if (isset($rules[$field]['rules']) && in_array('required', $rules[$field]['rules'])) {
                // Field is required
                $validation->setError($field, phrase('Please choose the file to upload'));

                //return false;
            }
        }

        /**
         * Because the property isn't accessible from its parent, put
         * the upload data collection to temporary session instead
         */
        set_userdata('_uploaded_files', $this->_uploadedFiles);

        return true;
    }

    /**
     * Execute the file upload process.
     *
     * @param string|null $filename The file input name
     * @param string|null $field The form field name
     * @param string|null $type The file type (image/document)
     * @param int|string|null $index Array index for multiple files
     * @param int|string|null $_index Nested array index
     * @return bool True if upload successful, false otherwise
     */
    private function _doUpload(?string $filename = null, ?string $field = null, ?string $type = null, $index = 0, $_index = null): bool
    {
        $request = Services::request();
        $router = Services::router();
        $upload_path = get_userdata('_set_upload_path');

        if (! $upload_path) {
            $upload_path = strtolower(substr(strstr($router->controllerName(), '\Controllers\\'), strlen('\Controllers\\')));
            $upload_path = array_pad(explode('\\', $upload_path), 2, null);
            $upload_path = $upload_path[1] ?? $upload_path[0];
        }

        $source = $request->getFile($filename);
        $mime_type = new Mimes();
        $valid_mime = [];

        if (! $source || ! $source->getName() || ! $source->isValid()) {
            if (is_object($source) && method_exists($source, 'getError') && $source->getError() && $source->getError() !== UPLOAD_ERR_NO_FILE) {
                $this->_uploadError = $this->_getUploadErrorMessage($source->getError());
            }

            return false;
        }

        if ('image' == $type) {
            // The selected file is image format
            $filetype = array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED));

            foreach ($filetype as $key => $val) {
                $valid_mime[] = $mime_type->guessTypeFromExtension($val);
            }
        } else {
            // The selected file is non-image format
            $filetype = array_map('trim', explode(',', DOCUMENT_FORMAT_ALLOWED));

            foreach ($filetype as $key => $val) {
                $valid_mime[] = $mime_type->guessTypeFromExtension($val);
            }
        }

        if (! in_array($source->getMimeType(), $valid_mime)) {
            // Mime is invalid
            $this->_uploadError = phrase('The selected file format is not allowed to upload');

            return false;
        } elseif (! in_array(strtolower($source->getExtension()), $filetype)) {
            // Extension is invalid (bypassed mime check)
            $this->_uploadError = phrase('The selected file extension is not allowed to upload');

            return false;
        } elseif ((float) $source->getSizeByMetricUnit(FileSizeUnit::MB) > MAX_UPLOAD_SIZE) {
            // Size is exceeded the maximum allocation
            $this->_uploadError = phrase('The selected file size exceeds the maximum allocation');

            return false;
        } elseif (! is_dir(UPLOAD_PATH) || ! is_writable(UPLOAD_PATH)) {
            // Upload directory is unwritable
            $this->_uploadError = phrase('The upload folder is not writable');

            return false;
        }

        if (! is_dir(UPLOAD_PATH . '/' . $upload_path)) {
            // Attempt to create new directory
            try {
                mkdir(UPLOAD_PATH . '/' . $upload_path, 0755, true);
                copy(UPLOAD_PATH . '/placeholder.png', UPLOAD_PATH . '/' . $upload_path . '/placeholder.png');
            } catch (Throwable $e) {
                $this->_uploadError = $e->getMessage();

                return false;
            }
        }

        if (! is_dir(UPLOAD_PATH . '/' . $upload_path . '/thumbs')) {
            // Attempt to create new directory
            try {
                mkdir(UPLOAD_PATH . '/' . $upload_path . '/thumbs', 0755, true);
                copy(UPLOAD_PATH . '/placeholder_thumb.png', UPLOAD_PATH . '/' . $upload_path . '/thumbs/placeholder.png');
            } catch (Throwable $e) {
                $this->_uploadError = $e->getMessage();

                return false;
            }
        }

        if (! is_dir(UPLOAD_PATH . '/' . $upload_path . '/icons')) {
            // Attempt to create new directory
            try {
                mkdir(UPLOAD_PATH . '/' . $upload_path . '/icons', 0755, true);
                copy(UPLOAD_PATH . '/placeholder_icon.png', UPLOAD_PATH . '/' . $upload_path . '/icons/placeholder.png');
            } catch (Throwable $e) {
                $this->_uploadError = $e->getMessage();

                return false;
            }
        }

        // Get encrypted filename
        $filename = $source->getRandomName();
        // Read file contents
        $fileContent = file_get_contents($source->getPathName());

        // Check for PHP tags
        if (preg_match('/<\?(?:php\s|=\s)|<script\b/i', $fileContent, $match)) {
            // Ensure the file is not contain exploit command
            $this->_uploadError = phrase('The file is not allowed to upload');

            return false;
        }

        if (in_array($source->getMimeType(), ['image/gif', 'image/jpeg', 'image/png'])) {
            // Uploaded file is image format, prepare image manipulation
            $imageinfo = getimagesize($source);

            if (! $imageinfo) {
                $this->_uploadError = phrase('The selected image file is not valid');

                return false;
            }

            $master_dimension = ($imageinfo[0] > $imageinfo[1] ? 'width' : 'height');
            $original_dimension = (is_numeric(IMAGE_DIMENSION) ? IMAGE_DIMENSION : 1024);
            $thumbnail_dimension = (is_numeric(THUMBNAIL_DIMENSION) ? THUMBNAIL_DIMENSION : 256);
            $icon_dimension = (is_numeric(ICON_DIMENSION) ? ICON_DIMENSION : 64);

            if ($source->getMimeType() != 'image/gif' && $imageinfo[0] > $original_dimension) {
                // Resize image for non-gif format
                $width = $original_dimension;
                $height = $original_dimension;

                // Load image manipulation library
                $image = Services::image('gd');

                try {
                    // Resize image and move to upload directory
                    $image->withFile($source)->resize($width, $height, true, $master_dimension)->save(UPLOAD_PATH . '/' . $upload_path . '/' . $filename);
                } catch (Throwable $e) {
                    $this->_uploadError = phrase('Unable to process the uploaded image');

                    return false;
                }
            } else {
                // Move file to upload directory
                $source->move(UPLOAD_PATH . '/' . $upload_path, $filename);
            }

            // Create thumbnail and icon of image
            $this->_resizeImage($upload_path, $filename, 'thumbs', $thumbnail_dimension, $thumbnail_dimension);
            $this->_resizeImage($upload_path, $filename, 'icons', $icon_dimension, $icon_dimension);
        } else {
            // Non-image format, move directly to upload directory
            $source->move(UPLOAD_PATH . '/' . $upload_path, $filename);
        }

        if (null !== $_index) {
            // Collect uploaded data (has sub-name)
            $this->_uploadedFiles[$field][$index][$_index] = $filename;
        } else {
            // Collect uploaded data (single name)
            $this->_uploadedFiles[$field][$index] = $filename;
        }

        return true;
    }

    /**
     * Convert PHP upload error code into localized message.
     *
     * @param int $error Upload error constant from PHP
     * @return string Localized error message
     */
    private function _getUploadErrorMessage(int $error): string
    {
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return phrase('The selected file size exceeds the maximum allocation');
            case UPLOAD_ERR_PARTIAL:
                return phrase('The file was only partially uploaded');
            case UPLOAD_ERR_NO_TMP_DIR:
                return phrase('Missing a temporary folder for upload');
            case UPLOAD_ERR_CANT_WRITE:
                return phrase('Failed to write file to disk');
            case UPLOAD_ERR_EXTENSION:
                return phrase('File upload stopped by extension');
            default:
                return phrase('The file is not allowed to upload');
        }
    }

    /**
     * Resize image to create thumbnail or icon.
     *
     * @param string $path The upload path
     * @param string $filename The original filename
     * @param string $type The image type (thumbs/icons)
     * @param int $width The target width
     * @param int $height The target height
     * @return bool True if resize successful, false otherwise
     */
    private function _resizeImage(string $path, string $filename, string $type, int $width, int $height): bool
    {
        $source = UPLOAD_PATH . '/' . $path . '/' . $filename;
        $target = UPLOAD_PATH . '/' . $path . ($type ? '/' . $type : null) . '/' . $filename;

        $imageinfo = getimagesize($source);
        $master_dimension = ($imageinfo[0] > $imageinfo[1] ? 'width' : 'height');

        try {
            // Load image manipulation library
            $image = Services::image('gd');

            // Resize image
            if ($image->withFile($source)->resize($width, $height, true, $master_dimension)->save($target)) {
                // Crop image after resized
                $image->withFile($target)
                    ->fit($width, $height, 'center')
                    ->save($target);
            }

            return true;
        } catch (Throwable $e) {
            log_message('error', 'Image resize failed: ' . $e->getMessage());

            return false;
        }
    }
}
