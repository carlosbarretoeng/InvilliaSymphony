<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Psr\Log\LoggerInterface;

class FileUploader
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function upload($uploadDirectory, $file, $filename)
    {
        try{
            $file->move($uploadDirectory, $filename);
        } catch (FileException $exception) {
            $this->logger->error('failed to upload file: ' . $exception->getMessage());
            throw new FileException('Failed to upload file');
        }
    }
}