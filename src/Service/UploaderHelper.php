<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    /**
     * @var string
     */
    private $uploadsPath;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RequestStackContext
     */
    private $context;

    /**
     * UploaderHelper constructor.
     * @param LoggerInterface $logger
     * @param string $uploadsPath
     * @param RequestStackContext $context
     */
    public function __construct(LoggerInterface $logger, string $uploadsPath, RequestStackContext $context)
    {
        $this->uploadsPath = $uploadsPath;
        $this->logger = $logger;
        $this->context = $context;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string
     */
    public function uploadFile(UploadedFile $uploadedFile, string $followingPath): string
    {
        $destination = $this->uploadsPath . $followingPath;
        $newFileName = sha1(uniqid(mt_rand(), true)).'.'.$uploadedFile->guessExtension();
        try {
            $uploadedFile->move(
                $destination,
                $newFileName
            );
        } catch (FileException $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error("Could not upload the file");
            return false;
        }

        return $followingPath.$newFileName;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getPublicPath(string $path): string
    {
        return $this->context->getBasePath() . "/uploads/" . $path;
    }

    public function removeFile($file)
    {
        $file_name = $this->uploadsPath.$file;
        if (!file_exists($file_name) || is_dir($file_name)) {
            return false;
        }

        unlink($file_name);
        return true;
    }
}