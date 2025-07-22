<?php

namespace App\Service;

use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GoogleCloudStorageService
{
    private StorageClient $storageClient;

    public function __construct(StorageClient $storageClient)
    {
        $this->storageClient = $storageClient;
    }

/**
 * List files in a Google Cloud Storage bucket, optionally filtered by extension.
 *
 * @param string $bucketName The name of the bucket
 * @param string $folder The folder path (e.g., 'uploads/') to filter by
 * @param string $extension The file extension to filter by (e.g., 'jpg')
 * @param array $options Additional options (e.g., prefix, delimiter)
 * @return array List of file names
 * @throws \RuntimeException If listing fails
 */
public function listFiles(string $folder, string $extension): array
{
    try {
        $options = [];
        $bucket = $this->storageClient->bucket('gcstorage-n8n');
        // if (!$bucket->exists()) {
        //     throw new \RuntimeException(sprintf('Bucket "%s" does not exist.', $bucketName));
        // }

        // Set prefix to filter by folder if provided
        if ($folder !== null) {
            $options['prefix'] = rtrim($folder, '/') . '/';
        }

        // Set delimiter to limit results to the specified folder
        $options['delimiter'] = '/';


        $objects = $bucket->objects($options);
        $fileNames = [];

        foreach ($objects as $object) {
            $fileName = $object->name();

            // Filter by extension if specified
            if ($extension !== null && !str_ends_with(strtolower($fileName), '.' . strtolower($extension))) {
                continue;
            }

            $fileNames[] = $fileName;
        }

        return $fileNames;
    } catch (\Exception $e) {
        throw new \RuntimeException(sprintf('Failed to list files: %s', $e->getMessage()), 0, $e);
    }
}

    /**
     * Get a signed URL for accessing a file.
     *
     * @param string $bucketName The name of the bucket
     * @param string $objectName The path to the file in the bucket
     * @param \DateTimeInterface|int $expires Expiration time (DateTime or seconds from now)
     * @param array $options Additional options for the signed URL
     * @return string The signed URL
     * @throws \RuntimeException If URL generation fails
     */
    public function getSignedUrl(string $bucketName, string $objectName, $expires, array $options = []): string
    {
        try {
            $bucket = $this->storageClient->bucket($bucketName);
            $object = $bucket->object($objectName);

            if (!$object->exists()) {
                throw new NotFoundHttpException(sprintf('File "%s" does not exist in bucket "%s".', $objectName, $bucketName));
            }

            $url = $object->signedUrl($expires, $options);

            return $url;
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Failed to generate signed URL: %s', $e->getMessage()), 0, $e);
        }
    }
}
