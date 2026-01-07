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

namespace Aksara\Laboratory\Services;

use Config\Mimes;
use Config\Services;
use CodeIgniter\Files\FileSizeUnit;
use Aksara\Laboratory\Model;
use DateTime;
use Throwable;

class Validation
{
    private $_uploadedFiles = [];

    private $_uploadError;

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
        // Normalize parameter separators: Replace commas with dots, then split by dot.
        // This is necessary to separate table.field.where_field.where_value parameters.
        $params = explode('.', str_replace(',', '.', $params));

        if ($params) {
            $model = new Model();

            if (isset($_ENV['DBDriver'])) {
                // Check if cross-database connection is configured via environment variables.
                // If set, apply the configuration to the model instance.
                $model->databaseConfig($_ENV);
            }

            // Slice parameters starting from index 2 to get the WHERE conditions (key-value pairs).
            $sliced = array_slice($params, 2, sizeof($params));
            $where = [];

            // Iterate over the sliced parameters to build an associative array of WHERE conditions.
            foreach ($sliced as $key => $val) {
                // Ensure we only process key names (which are at even indices relative to $sliced).
                if ($key % 2 === 0) {
                    // Assign the key (field name) its corresponding value (at the next index).
                    $where[$val] = (isset($sliced[$key + 1]) ? $sliced[$key + 1] : '');
                }
            }

            // Initialize a counter to track the position of the WHERE condition (used to apply '!=' to the first condition).
            $num = 0;

            // Apply the extracted WHERE conditions to the model query.
            foreach ($where as $key => $val) {
                // Condition check 1: Skip if the WHERE value is effectively empty (not set or not numeric 0).
                if (! $val && ! is_numeric($val)) {
                    // Change the loop number before skipping.
                    $num++;

                    // Value is empty, continue next loops
                    continue;
                }

                // Condition check 2: Determine if this is the first condition being applied.
                if (! $num) {
                    // First condition: Assume this is typically the primary key exclusion (e.g., ID != current_ID).
                    // Apply a 'NOT EQUAL' condition (e.g., where('id !=', 5)).
                    $model->where($key . ' != ', $val);
                } else {
                    // Subsequent conditions: Apply a standard 'EQUAL' condition (e.g., where('user_id', 10)).
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
        // Accept null, 0, 1, '0', '1'
        if (null === $value || 0 === $value || 1 === $value || '0' === $value || '1' === $value) {
            return true;
        }

        return false;
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
     * Check if field is valid year (between 1970 and 2100).
     *
     * @param int|string $value The value to check
     * @return bool True if valid year, false otherwise
     */
    public function valid_year(int|string $value = null): bool
    {
        $validYear = range(1970, 2100);

        if (! in_array((int) $value, $validYear, true)) {
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
        if (null === $value || ! preg_match('/#([a-f0-9]{3}){1,2}\b/i', $value)) {
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

        // Typically the suffix used for carousel or future addition
        if (isset($_FILES[$field]['name']['background'])) {
            $suffix = '.background';
        } else {
            $suffix = null;
        }

        $files = $request->getFile($field . $suffix) ?? $request->getFileMultiple($field . $suffix);

        if (is_array($files)) {
            foreach ($files as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $_key => $_val) {
                        // Typically using nested input like field[foo][bar]
                        $this->_doUpload($field . $suffix . '.' . $key . '.' . $_key, $field, $type, $key, $_key);
                    }
                } else {
                    // Typically using nested input like field[foo]
                    $this->_doUpload($field . $suffix . '.' . $key, $field, $type, $key);
                }
            }
        } else {
            $this->_doUpload($field . $suffix, $field, $type);
        }

        if ($this->_uploadError) {
            // Validation error
            $validation->setError($field, $this->_uploadError);
        } elseif (! $this->_uploadedFiles) {
            // Find required
            $rules = $validation->getRules();

            if (isset($rules[$field]['rules']) && in_array('required', $rules[$field]['rules'], true)) {
                // Field is required
                $validation->setError($field, phrase('Please choose the file to upload'));
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
        $uploadPath = get_userdata('_set_upload_path');

        if (! $uploadPath) {
            $uploadPath = strtolower(substr(strstr($router->controllerName(), '\Controllers\\'), strlen('\Controllers\\')));
            $uploadPath = array_pad(explode('\\', $uploadPath), 2, null);
            $uploadPath = $uploadPath[1] ?? $uploadPath[0];
        }

        $source = $request->getFile($filename);
        $mimeType = new Mimes();
        $validMime = [];

        if (! $source || ! $source->isValid() || ! $source->getName()) {
            // No file are selected
            return false;
        }

        if ('image' == $type) {
            // The selected file is image format
            $filetype = array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED));

            foreach ($filetype as $key => $val) {
                $validMime[] = $mimeType->guessTypeFromExtension($val);
            }
        } else {
            // The selected file is non-image format
            $filetype = array_map('trim', explode(',', DOCUMENT_FORMAT_ALLOWED));

            foreach ($filetype as $key => $val) {
                $validMime[] = $mimeType->guessTypeFromExtension($val);
            }
        }

        if (! in_array($source->getMimeType(), $validMime, true)) {
            // Mime is invalid
            $this->_upload_error = phrase('The selected file format is not allowed to upload');

            return false;
        } elseif ((float) $source->getSizeByBinaryUnit(FileSizeUnit::MB) > MAX_UPLOAD_SIZE) {
            // Size is exceeded the maximum allocation
            $this->_upload_error = phrase('The selected file size exceeds the maximum allocation');

            return false;
        } elseif (! is_dir(UPLOAD_PATH) || ! is_writable(UPLOAD_PATH)) {
            // Upload directory is unwritable
            $this->_upload_error = phrase('The upload folder is not writable');

            return false;
        }

        if (! is_dir(UPLOAD_PATH . '/' . $uploadPath)) {
            // Attempt to new directory
            try {
                mkdir(UPLOAD_PATH . '/' . $uploadPath, 0755, true);
                copy(UPLOAD_PATH . '/placeholder.png', UPLOAD_PATH . '/' . $uploadPath . '/placeholder.png');
            } catch (Throwable $e) {
                $this->_upload_error = $e->getMessage();

                return false;
            }
        }

        if (! is_dir(UPLOAD_PATH . '/' . $uploadPath . '/thumbs')) {
            // Attempt to new directory
            try {
                mkdir(UPLOAD_PATH . '/' . $uploadPath . '/thumbs', 0755, true);
                copy(UPLOAD_PATH . '/placeholder_thumb.png', UPLOAD_PATH . '/' . $uploadPath . '/thumbs/placeholder.png');
            } catch (Throwable $e) {
                $this->_upload_error = $e->getMessage();

                return false;
            }
        }

        if (! is_dir(UPLOAD_PATH . '/' . $uploadPath . '/icons')) {
            // Attempt to new directory
            try {
                mkdir(UPLOAD_PATH . '/' . $uploadPath . '/icons', 0755, true);
                copy(UPLOAD_PATH . '/placeholder_icon.png', UPLOAD_PATH . '/' . $uploadPath . '/icons/placeholder.png');
            } catch (Throwable $e) {
                $this->_upload_error = $e->getMessage();

                return false;
            }
        }

        // Get encrypted filename
        $filename = $source->getRandomName();
        // Read file contents
        $fileContent = file_get_contents($source->getPathName());

        // Check for PHP tags
        if (preg_match('/<\?php/i', $fileContent)) {
            // Ensure the file is not contain exploit command
            $this->_upload_error = phrase('The file is not allowed to upload');

            return false;
        }

        if (in_array($source->getMimeType(), ['image/gif', 'image/jpeg', 'image/png'], true)) {
            // Uploaded file is image format, prepare image manipulation
            $imageinfo = getimagesize($source);
            $masterDimension = ($imageinfo[0] > $imageinfo[1] ? 'width' : 'height');
            $originalDimension = (is_numeric(IMAGE_DIMENSION) ? IMAGE_DIMENSION : 1024);
            $thumbnailDimension = (is_numeric(THUMBNAIL_DIMENSION) ? THUMBNAIL_DIMENSION : 256);
            $iconDimension = (is_numeric(ICON_DIMENSION) ? ICON_DIMENSION : 64);

            if ($source->getMimeType() != 'image/gif' && $imageinfo[0] > $originalDimension) {
                // Resize image for non-gif format
                $width = $originalDimension;
                $height = $originalDimension;

                // Load image manipulation library
                $image = Services::image('gd');

                // Resize image and move to upload directory
                $image->withFile($source)->resize($width, $height, true, $masterDimension)->save(UPLOAD_PATH . '/' . $uploadPath . '/' . $filename);
            } else {
                // Move file to upload directory
                $source->move(UPLOAD_PATH . '/' . $uploadPath, $filename);
            }

            // Create thumbnail and icon of image
            $this->_resizeImage($uploadPath, $filename, 'thumbs', $thumbnailDimension, $thumbnailDimension);
            $this->_resizeImage($uploadPath, $filename, 'icons', $iconDimension, $iconDimension);
        } else {
            // Non-image format, move directly to upload directory
            $source->move(UPLOAD_PATH . '/' . $uploadPath, $filename);
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
        $masterDimension = ($imageinfo[0] > $imageinfo[1] ? 'width' : 'height');

        try {
            // Load image manipulation library
            $image = Services::image('gd');

            // Resize image
            if ($image->withFile($source)->resize($width, $height, true, $masterDimension)->save($target)) {
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
