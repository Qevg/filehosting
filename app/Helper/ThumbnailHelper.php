<?php

namespace Filehosting\Helper;

use Filehosting\Entity\File;
use Filehosting\Exception\ThumbnailException;

/**
 * Class ThumbnailHelper
 * @package Filehosting\Helper
 */
class ThumbnailHelper
{
    /**
     * @var $supportedImageTypes
     */
    private $supportedImageTypes;

    /**
     * @var $image
     */
    private $image;

    /**
     * @var $imageType
     */
    private $imageType;

    /**
     * @var PathHelper $pathHelper
     */
    private $pathHelper;

    /**
     * @var FileSystemHelper $fileSystemHelper
     */
    private $fileSystemHelper;

    /**
     * ThumbnailHelper constructor.
     *
     * @param PathHelper $pathHelper
     */
    public function __construct(PathHelper $pathHelper, FileSystemHelper $fileSystemHelper)
    {
        $this->pathHelper = $pathHelper;
        $this->fileSystemHelper = $fileSystemHelper;

        if (imagetypes() & IMG_JPG) {
            $this->supportedImageTypes[] = 'jpeg';
        }
        if (imagetypes() & IMG_PNG) {
            $this->supportedImageTypes[] = 'png';
        }
        if (imagetypes() & IMG_GIF) {
            $this->supportedImageTypes[] = 'gif';
        }
    }

    /**
     * Generates thumbnail.
     *
     * @param File $file
     *
     * @return string relative path to thumbnail
     * @throws ThumbnailException
     */
    public function generateThumbnail(File $file): string
    {
        if (!$this->isTypeSupported($file->getFormat())) {
            throw new ThumbnailException("Image type is not supported");
        }

        $thumbnailDir = $this->pathHelper->getPathToThumbnailDirectory($file->getName());
        if (!file_exists($thumbnailDir)) {
            $this->fileSystemHelper->mkdirR($thumbnailDir);
            $this->fileSystemHelper->chmodR($thumbnailDir, dirname($this->pathHelper->getPathToThumbnailsStorage()));
        }

        $thumbnailPath = $this->pathHelper->getPathToThumbnail($file->getName());
        $this->createImage($file->getPath());
        $this->resize(515, 0);
        $this->save($thumbnailPath, IMAGETYPE_PNG);
        $this->fileSystemHelper->chmodR($thumbnailPath);
        return $this->pathHelper->getRelativePath($thumbnailPath);
    }

    /**
     * Removes thumbnail
     *
     * @param string $fileName
     */
    public function removeThumbnail(string $fileName): void
    {
        $this->fileSystemHelper->unlink($this->pathHelper->getPathToThumbnail($fileName));
    }

    /**
     * Checks if the file format is supported
     *
     * @param string $fileFormat
     *
     * @return bool
     */
    public function isTypeSupported(string $fileFormat): bool
    {
        if (in_array($fileFormat, $this->supportedImageTypes)) {
            return true;
        }
        return false;
    }

    /**
     * Creates a new image from file
     *
     * @param string $filePath
     *
     * @throws ThumbnailException
     */
    private function createImage(string $filePath): void
    {
        $this->imageType = getimagesize($filePath)[2];
        if ($this->imageType === IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filePath);
        } elseif ($this->imageType === IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filePath);
        } elseif ($this->imageType === IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filePath);
        } else {
            throw new ThumbnailException('Image type is not supported');
        }
    }

    /**
     * Resize image
     * If the width or height is 0, then saves the aspect ratio
     *
     * @param int $width
     * @param int $height
     *
     * @throws ThumbnailException
     */
    private function resize(int $width, int $height): void
    {
        if ($width === 0 && $height === 0) {
            throw new ThumbnailException('width and height can not be zero');
        } elseif ($height === 0) {
            $this->resizeToWidth($width);
        } elseif ($width === 0) {
            $this->resizeToHeight($height);
        } else {
            $newImage = imagecreatetruecolor($width, $height);
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
            $this->image = $newImage;
        }
    }

    /**
     * Saves file to $thumbnailPath with type $imageType
     *
     * @param string $thumbnatilPath the path where the thumbtail will be saved
     * @param int $imageType use predefined constants. example: IMAGETYPE_PNG
     *
     * @throws ThumbnailException
     */
    private function save(string $thumbnatilPath, int $imageType): void
    {
        if ($imageType === IMAGETYPE_JPEG) {
            imagejpeg($this->image, $thumbnatilPath);
        } elseif ($imageType === IMAGETYPE_PNG) {
            imagepng($this->image, $thumbnatilPath);
        } elseif ($imageType === IMAGETYPE_GIF) {
            imagegif($this->image, $thumbnatilPath);
        } else {
            throw new ThumbnailException('Image type is not supported');
        }
        imagedestroy($this->image);
    }

    /**
     * Resizes image according to the given width (saves the aspect ratio)
     *
     * @param int $width
     */
    private function resizeToWidth(int $width): void
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getHeight() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * Resizes image according to the given height (saves the aspect ratio)
     *
     * @param int $height
     */
    private function resizeToHeight(int $height): void
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * Get image width
     *
     * @return int
     */
    private function getWidth(): int
    {
        return imagesx($this->image);
    }

    /**
     * Get image heigth
     *
     * @return int
     */
    private function getHeight(): int
    {
        return imagesy($this->image);
    }
}
