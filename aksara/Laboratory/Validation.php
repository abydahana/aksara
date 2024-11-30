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

namespace Aksara\Laboratory;

use Aksara\Laboratory\Model;
use Config\Mimes;

class Validation
{
    private $_uploaded_files = [];

    private $_upload_error;

    public function __construct()
    {
    }

    /**
     * Check if data is already exist in the database table. It's similar to
     * is_unique but it's most advanced.
     *
     * @param   mixed|null $value
     * @param   string $params
     * @param   array $data
     */
    public function unique($value = null, $params = null, $data = []): bool
    {
        $params = explode('.', str_replace(',', '.', $params));

        if ($params) {
            $model = new Model();

            if (isset($_ENV['DBDriver'])) {
                // Cross database connection
                $model->database_config($_ENV);
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
                // Check if value not empty
                if (! $val && ! is_numeric($val)) {
                    // Change the loop number
                    $num++;

                    // Value is empty, continue next loops
                    continue;
                }

                // Check if not first loop
                if (! $num) {
                    // Where value is not in statement
                    $model->where($key . ' != ', $val);
                } else {
                    // Where value is in statement
                    $model->where($key, $val);
                }

                $num++;
            }

            return $model->select($params[1])->get_where($params[0], [$params[1] => $value])->num_rows() === 0;
        }

        return false;
    }

    /**
     * Check if field is valid boolean
     *
     * @param   mixed|null $value
     */
    public function boolean($value = null): bool
    {
        if (null != $value && 1 != $value) {
            return false;
        }

        return true;
    }

    /**
     * Check if field is valid currency
     *
     * @param mixed|null $value
     */
    public function currency($value = null): bool
    {
        if (! preg_match('/^\s*[$]?\s*((\d+)|(\d{1,3}(\,\d{3})+))(\.\d{2})?\s*$/', $value)) {
            return false;
        }

        return true;
    }

    /**
     * Check if field is valid date
     *
     * @param   mixed|null $value
     */
    public function valid_date($value = null): bool
    {
        // Convert value to standardzitation
        $value = date('Y-m-d', strtotime($value));

        $valid_date = \DateTime::createFromFormat('Y-m-d', $value);

        if (! $valid_date || ($valid_date && $valid_date->format('Y-m-d') !== $value)) {
            return false;
        }

        return true;
    }

    /**
     * Check if field is valid time
     *
     * @param   mixed|null $value
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
     * Check if field is valid date and time
     *
     * @param   mixed|null $value
     */
    public function valid_datetime($value = null): bool
    {
        // Convert value to standardzitation
        $value = date('Y-m-d H:i:s', strtotime($value));

        $valid_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $value);

        if (! $valid_datetime || ($valid_datetime && $valid_datetime->format('Y-m-d H:i:s') !== $value)) {
            return false;
        }

        return true;
    }

    /**
     * Check if field is valid year
     *
     * @param   mixed|null $value
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
     * Check if field is valid hex
     *
     * @param   mixed|null $value
     */
    public function valid_hex($value = null): bool
    {
        if (! preg_match('/#([a-f0-9]{3}){1,2}\b/i', $value)) {
            return false;
        }

        return true;
    }

    /**
     * Check relation table
     *
     * @param   mixed|null $params
     */
    public function relation_checker($value = 0, $params = null): bool
    {
        $model = new Model();

        if (isset($_ENV['DBDriver'])) {
            // Cross database connection
            $model->database_config($_ENV);
        }

        list($table, $field) = array_pad(explode('.', $params), 2, null);

        if (strpos($table, ' ') !== false) {
            $table = substr($table, 0, strrpos($table, ' '));
        }

        if (! $model->table_exists($table) || ! $model->field_exists($field, $table) || ! $model->select($field)->get_where($table, [$field => $value])->row($field)) {
            return false;
        }

        return true;
    }

    /**
     * We used to extract image in traditional way because of possibility
     * of multiple image can be uploaded with different field name
     *
     * @param   mixed|null $value
     * @param   mixed|null $params
     */
    public function validate_upload($value = null, $params = null): bool
    {
        $validation = \Config\Services::validation();

        list($field, $type) = array_pad(explode('.', $params), 2, null);

        // Typically the suffix used for carousel or future addition
        if (isset($_FILES[$field]['name']['background'])) {
            $suffix = '.background';
        } else {
            $suffix = null;
        }

        $files = service('request')->getFile($field . $suffix) ?? service('request')->getFileMultiple($field . $suffix);

        if (is_array($files)) {
            foreach ($files as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $_key => $_val) {
                        // Typically using nested input like field[foo][bar]
                        $this->_do_upload($field . $suffix . '.' . $key . '.' . $_key, $field, $type, $key, $_key);
                    }
                } else {
                    // Typically using nested input like field[foo]
                    $this->_do_upload($field . $suffix . '.' . $key, $field, $type, $key);
                }
            }
        } else {
            $this->_do_upload($field . $suffix, $field, $type);
        }

        if ($this->_upload_error) {
            // Validation error
            $validation->setError($field, $this->_upload_error);
        } elseif (! $this->_uploaded_files) {
            // Find required
            $rules = $validation->getRules();

            if (isset($rules[$field]['rules']) && in_array('required', $rules[$field]['rules'])) {
                // Field is required
                $validation->setError($field, phrase('Please choose the file to upload'));
            }
        }

        /**
         * Because the property isn't accessible from its parent, put
         * the upload data collection to temporary session instead
         */
        set_userdata('_uploaded_files', $this->_uploaded_files);

        return true;
    }

    /**
     * Execute the file upload
     *
     * @param   string|null $filename
     * @param   string|null $field
     * @param   string|null $type
     * @param   mixed|null $index
     * @param   mixed|null $_index
     */
    private function _do_upload($filename = null, $field = null, $type = null, $index = 0, $_index = null)
    {
        $upload_path = get_userdata('_set_upload_path');

        if (! $upload_path) {
            $upload_path = strtolower(substr(strstr(service('router')->controllerName(), '\Controllers\\'), strlen('\Controllers\\')));
            $upload_path = array_pad(explode('\\', $upload_path), 2, null);
            $upload_path = $upload_path[1] ?? $upload_path[0];
        }

        $source = service('request')->getFile($filename);
        $mime_type = new Mimes();
        $valid_mime = [];

        if (! $source->getName()) {
            // No file are selected
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
            $this->_upload_error = phrase('The selected file format is not allowed to upload');

            return false;
        } elseif ((float) str_replace(',', '', $source->getSizeByUnit('kb')) > (MAX_UPLOAD_SIZE * 1024)) {
            // Size is exceeded the maximum allocation
            $this->_upload_error = phrase('The selected file size exceeds the maximum allocation');

            return false;
        } elseif (! is_dir(UPLOAD_PATH) || ! is_writable(UPLOAD_PATH)) {
            // Upload directory is unwritable
            $this->_upload_error = phrase('The upload folder is not writable');

            return false;
        }

        if (! is_dir(UPLOAD_PATH . '/' . $upload_path)) {
            // Attempt to new directory
            try {
                mkdir(UPLOAD_PATH . '/' . $upload_path, 0755, true);
                copy(UPLOAD_PATH . '/placeholder.png', UPLOAD_PATH . '/' . $upload_path . '/placeholder.png');
            } catch (\Throwable $e) {
                $this->_upload_error = $e->getMessage();

                return false;
            }
        }

        if (! is_dir(UPLOAD_PATH . '/' . $upload_path . '/thumbs')) {
            // Attempt to new directory
            try {
                mkdir(UPLOAD_PATH . '/' . $upload_path . '/thumbs', 0755, true);
                copy(UPLOAD_PATH . '/placeholder_thumb.png', UPLOAD_PATH . '/' . $upload_path . '/thumbs/placeholder.png');
            } catch (\Throwable $e) {
                $this->_upload_error = $e->getMessage();

                return false;
            }
        }

        if (! is_dir(UPLOAD_PATH . '/' . $upload_path . '/icons')) {
            // Attempt to new directory
            try {
                mkdir(UPLOAD_PATH . '/' . $upload_path . '/icons', 0755, true);
                copy(UPLOAD_PATH . '/placeholder_icon.png', UPLOAD_PATH . '/' . $upload_path . '/icons/placeholder.png');
            } catch (\Throwable $e) {
                $this->_upload_error = $e->getMessage();

                return false;
            }
        }

        // Get encrypted filename
        $filename = $source->getRandomName();

        if (in_array($source->getMimeType(), ['image/gif', 'image/jpeg', 'image/png'])) {
            // Uploaded file is image format, prepare image manipulation
            $imageinfo = getimagesize($source);
            $master_dimension = ($imageinfo[0] > $imageinfo[1] ? 'width' : 'height');
            $original_dimension = (is_numeric(IMAGE_DIMENSION) ? IMAGE_DIMENSION : 1024);
            $thumbnail_dimension = (is_numeric(THUMBNAIL_DIMENSION) ? THUMBNAIL_DIMENSION : 256);
            $icon_dimension = (is_numeric(ICON_DIMENSION) ? ICON_DIMENSION : 64);

            if ($source->getMimeType() != 'image/gif' && $imageinfo[0] > $original_dimension) {
                // Resize image for non-gif format
                $width = $original_dimension;
                $height = $original_dimension;

                // Load image manipulation library
                $image = \Config\Services::image('gd');

                // Resize image and move to upload directory
                $image->withFile($source)->resize($width, $height, true, $master_dimension)->save(UPLOAD_PATH . '/' . $upload_path . '/' . $filename);
            } else {
                // Move file to upload directory
                $source->move(UPLOAD_PATH . '/' . $upload_path, $filename);
            }

            // Create thumbnail and icon of image
            $this->_resize_image($upload_path, $filename, 'thumbs', $thumbnail_dimension, $thumbnail_dimension);
            $this->_resize_image($upload_path, $filename, 'icons', $icon_dimension, $icon_dimension);
        } else {
            // Non-image format, move directly to upload directory
            $source->move(UPLOAD_PATH . '/' . $upload_path, $filename);
        }

        if (null !== $_index) {
            // Collect uploaded data (has sub-name)
            $this->_uploaded_files[$field][$index][$_index] = $filename;
        } else {
            // Collect uploaded data (single name)
            $this->_uploaded_files[$field][$index] = $filename;
        }
    }

    /**
     * Generate the thumbnail of uploaded image
     *
     * @param   string|null $path
     * @param   string|null $filename
     * @param   string|null $type
     * @param   int $width
     * @param   int $height
     */
    private function _resize_image($path = null, $filename = null, $type = null, $width = 0, $height = 0)
    {
        $source = UPLOAD_PATH . '/' . $path . '/' . $filename;
        $target = UPLOAD_PATH . '/' . $path . ($type ? '/' . $type : null) . '/' . $filename;

        $imageinfo = getimagesize($source);
        $master_dimension = ($imageinfo[0] > $imageinfo[1] ? 'width' : 'height');

        // Load image manipulation library
        $image = \Config\Services::image('gd');

        // Resize image
        if ($image->withFile($source)->resize($width, $height, true, $master_dimension)->save($target)) {
            // Crop image after resized
            $image->withFile($target)
                ->fit($width, $height, 'center')
                ->save($target);
        }
    }
}
