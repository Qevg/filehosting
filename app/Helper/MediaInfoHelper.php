<?php

namespace Filehosting\Helper;

/**
 * Class MediaInfoHelper
 * @package Filehosting\Helper
 */
class MediaInfoHelper
{
    /**
     * @var \getID3 $getId3
     */
    private $getId3;

    /**
     * MediaInfoHelper constructor.
     *
     * @param \getID3 $getId3
     */
    public function __construct(\getID3 $getId3)
    {
        $this->getId3 = $getId3;
    }

    /**
     * Analyzes the file using getId3
     *
     * @param string $fileName
     * @param string $type
     *
     * @return null|string
     */
    public function analyze(string $fileName, string $type)
    {
        $fileInfo = $this->getId3->analyze($fileName);
        if ($type === 'image') {
            if (isset($fileInfo['video']['dataformat'])) {
                $mediaInfo['dataformat'] = $fileInfo['video']['dataformat'];
            }
            if (isset($fileInfo['video']['resolution_x']) && isset($fileInfo['video']['resolution_y'])) {
                $mediaInfo['resolution'] = $fileInfo['video']['resolution_x'] . 'x' . $fileInfo['video']['resolution_y'];
            }
            if (isset($fileInfo['video']['bits_per_sample'])) {
                $mediaInfo['bits_per_sample'] = $fileInfo['video']['bits_per_sample'];
            }
        } elseif ($type === 'audio') {
            if (isset($fileInfo['audio']['dataformat'])) {
                $mediaInfo['dataformat'] = $fileInfo['audio']['dataformat'];
            }
            if (isset($fileInfo['audio']['bitrate'])) {
                $mediaInfo['bitrate'] = $fileInfo['audio']['bitrate'];
            }
            if (isset($fileInfo['audio']['bitrate_mode'])) {
                $mediaInfo['bitrate_mode'] = $fileInfo['audio']['bitrate_mode'];
            }
            if (isset($fileInfo['audio']['channelmode'])) {
                $mediaInfo['channelmode'] = $fileInfo['audio']['channelmode'];
            }
            if (isset($fileInfo['audio']['channels'])) {
                $mediaInfo['channels'] = $fileInfo['audio']['channels'];
            }
            if (isset($fileInfo['audio']['codec'])) {
                $mediaInfo['codec'] = $fileInfo['audio']['codec'];
            }
            if (isset($fileInfo['audio']['sample_rate'])) {
                $mediaInfo['sample_rate'] = $fileInfo['audio']['sample_rate'];
            }
        } elseif ($type === 'video') {
            if (isset($fileInfo['audio']['dataformat'])) {
                $mediaInfo['dataformat'] = $fileInfo['audio']['dataformat'];
            }
            if (isset($fileInfo['video']['resolution_x']) && isset($fileInfo['video']['resolution_y'])) {
                $mediaInfo['resolution'] = $fileInfo['video']['resolution_x'] . 'x' . $fileInfo['video']['resolution_y'];
            }
            if (isset($fileInfo['bitrate'])) {
                $mediaInfo['bitrate'] = round($fileInfo['bitrate']);
            }
            if (isset($fileInfo['video']['codec'])) {
                $mediaInfo['codec'] = $fileInfo['video']['codec'];
            }
        }
        return !empty($mediaInfo) ? json_encode($mediaInfo) : null;
    }
}
