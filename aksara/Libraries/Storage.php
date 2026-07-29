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

namespace Aksara\Libraries;

use Aws\S3\S3Client;
use Aksara\Laboratory\Model;
use Config\Services;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use RuntimeException;
use Throwable;

class Storage
{
    private ?object $_config = null;
    private ?S3Client $_client = null;
    private ?FilesystemOperator $_filesystem = null;

    public function __construct(array|object|null $config = null)
    {
        if ($config) {
            $this->_config = (object) $config;
            $this->_client = null;
        }
    }

    /**
     * Use a specific storage configuration.
     */
    public function setConfig(array|object $config): static
    {
        $this->_config = (object) $config;
        $this->_client = null;
        $this->_filesystem = null;

        return $this;
    }

    /**
     * Get the configured Flysystem instance.
     */
    public function filesystem(): FilesystemOperator
    {
        if ($this->_filesystem) {
            return $this->_filesystem;
        }

        $config = $this->_getConfig();
        $adapter = new AwsS3V3Adapter($this->_client(), (string) $config->bucket);

        return $this->_filesystem = new Filesystem($adapter);
    }

    /**
     * Write file contents to storage.
     */
    public function put(string $path, string $contents, array $config = []): void
    {
        $this->filesystem()->write($this->_normalizePath($path), $contents, $config);
    }

    /**
     * Write stream contents to storage.
     *
     * @param resource $contents
     */
    public function putStream(string $path, $contents, array $config = []): void
    {
        $this->filesystem()->writeStream($this->_normalizePath($path), $contents, $config);
    }

    /**
     * Read file contents from storage.
     */
    public function read(string $path): string
    {
        return $this->filesystem()->read($this->_normalizePath($path));
    }

    /**
     * Read a file stream from storage.
     *
     * @return resource
     */
    public function readStream(string $path)
    {
        return $this->filesystem()->readStream($this->_normalizePath($path));
    }

    /**
     * List storage contents.
     */
    public function listContents(string $path = '', bool $recursive = true): iterable
    {
        return $this->filesystem()->listContents($this->_normalizePath($path), $recursive);
    }

    /**
     * Delete a file from storage.
     */
    public function delete(string $path): void
    {
        $this->filesystem()->delete($this->_normalizePath($path));
    }

    /**
     * Check whether a file exists in storage.
     */
    public function exists(string $path): bool
    {
        return $this->filesystem()->fileExists($this->_normalizePath($path));
    }

    /**
     * Build a public path-style URL.
     */
    public function url(string $path): string
    {
        $config = $this->_getConfig();
        $path = $this->_normalizePath($path);
        $objectPath = $this->_objectPath($path, $config);

        if ($this->_decrypt((string) ($config->access_key ?? '')) && $this->_decrypt((string) ($config->secret_key ?? ''))) {
            $command = $this->_client()->getCommand('GetObject', [
                'Bucket' => (string) $config->bucket,
                'Key' => $objectPath
            ]);

            return (string) $this->_client()->createPresignedRequest($command, '+15 minutes')->getUri();
        }

        return $this->_endpoint($config) . '/' . trim((string) $config->bucket, '/') . '/' . $objectPath;
    }

    /**
     * Get configured S3 client.
     */
    private function _client(): S3Client
    {
        if ($this->_client) {
            return $this->_client;
        }

        $config = $this->_getConfig();
        $provider = strtolower((string) ($config->provider ?? 's3'));

        if ('disabled' === $provider) {
            throw new RuntimeException('Cloud storage is disabled.');
        }

        if (! in_array($provider, ['s3', 'minio', 'r2', 'spaces', 'wasabi'])) {
            throw new RuntimeException('Unsupported storage provider: ' . $provider);
        }

        $clientConfig = [
            'version' => 'latest',
            'region' => $config->region ?: 'us-east-1',
            'credentials' => [
                'key' => $this->_decrypt((string) $config->access_key),
                'secret' => $this->_decrypt((string) $config->secret_key),
            ],
        ];

        if (! empty($config->endpoint)) {
            $clientConfig['endpoint'] = $this->_endpointHasBucket($config)
                ? rtrim((string) $config->endpoint, '/')
                : $this->_endpoint($config);
        }

        if ($this->_endpointHasBucket($config)) {
            $clientConfig['bucket_endpoint'] = true;
        } else {
            $clientConfig['use_path_style_endpoint'] = true;
        }

        return $this->_client = new S3Client($clientConfig);
    }

    /**
     * Load active storage configuration from database.
     */
    private function _getConfig(): object
    {
        if ($this->_config) {
            return $this->_config;
        }

        $model = new Model();
        $config = $model->getWhere('app_storage', ['status' => 1], 1)->row();

        if (! $config) {
            throw new RuntimeException('No active storage configuration found.');
        }

        return $this->_config = $config;
    }

    /**
     * Normalize object path before passing it to Flysystem.
     */
    private function _normalizePath(string $path): string
    {
        $config = $this->_getConfig();
        $path = trim(str_replace('\\', '/', $path), '/');
        $bucket = trim((string) ($config->bucket ?? ''), '/');
        $prefix = $this->_prefix();

        if ($bucket && 0 === strpos($path . '/', $bucket . '/')) {
            $path = trim(substr($path, strlen($bucket)), '/');
        }

        if ($prefix && 0 === strpos($path . '/', $prefix . '/')) {
            $path = trim(substr($path, strlen($prefix)), '/');
        }

        return $path;
    }

    /**
     * Build a public object path including the configured prefix.
     */
    private function _objectPath(string $path, object $config): string
    {
        return $this->_normalizePath($path);
    }

    /**
     * Build endpoint without bucket suffix.
     */
    private function _endpoint(object $config): string
    {
        $endpoint = rtrim((string) ($config->endpoint ?? ''), '/');
        $bucket = trim((string) ($config->bucket ?? ''), '/');

        if ($bucket && preg_match('#/' . preg_quote($bucket, '#') . '$#', $endpoint)) {
            $endpoint = preg_replace('#/' . preg_quote($bucket, '#') . '$#', '', $endpoint);
        }

        return rtrim((string) $endpoint, '/');
    }

    /**
     * Determine whether endpoint already points to the configured bucket.
     */
    private function _endpointHasBucket(object $config): bool
    {
        $endpoint = rtrim((string) ($config->endpoint ?? ''), '/');
        $bucket = trim((string) ($config->bucket ?? ''), '/');

        return $bucket && (bool) preg_match('#/' . preg_quote($bucket, '#') . '$#', $endpoint);
    }

    /**
     * Cloud object paths are relative to bucket, while UPLOAD_PATH is local-only.
     */
    private function _prefix(): string
    {
        return trim(str_replace('\\', '/', defined('UPLOAD_PATH') ? UPLOAD_PATH : 'uploads'), '/');
    }

    /**
     * Decrypt encrypted form values, returning the original value when plain.
     */
    private function _decrypt(string $value): string
    {
        if (! $value) {
            return '';
        }

        try {
            return Services::encrypter()->decrypt(base64_decode($value, true));
        } catch (Throwable $e) {
            return $value;
        }
    }
}
