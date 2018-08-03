<?php

namespace Filehosting\Tests\Unit\Helper;

use Filehosting\Helper\PathHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class PathHelperTest
 * @package Filehosting\Tests\Unit\Helper
 */
class PathHelperTest extends TestCase
{
    /**
     * @var PathHelper $pathHelper
     */
    private $pathHelper;

    /**
     * @var string $exampleBasePath example base path
     */
    private $exampleBasePath = '/var/www/example';

    /**
     * @var string $exampleFileName example file name
     */
    private $exampleFileName = '2bp0y7bcg4jnbbv61';

    /**
     * @var string $exampleFilePath example file path
     */
    private $exampleFilePath = '/var/www/example/storage/2b/2bp0y7bcg4jnbbv61';

    /**
     * @var string $exampleThumbnailPath example thumbnail path
     */
    private $exampleThumbnailPath = '/var/www/example/public/thumbnails/2b/2bp0y7bcg4jnbbv61';

    protected function setUp()
    {
        $this->pathHelper = new PathHelper($this->exampleBasePath);
    }

    public function testPathToFile()
    {
        $this->assertEquals($this->pathHelper->getPathToFile($this->exampleFileName), $this->exampleFilePath);
    }

    public function testPathToThumbnail()
    {
        $this->assertEquals($this->pathHelper->getPathToThumbnail($this->exampleFileName), $this->exampleThumbnailPath);
    }

    public function testRelativePath()
    {
        $this->assertEquals($this->pathHelper->getRelativePath($this->exampleThumbnailPath), '/thumbnails/2b/2bp0y7bcg4jnbbv61');
    }

    public function testXAccelPath()
    {
        $this->assertEquals($this->pathHelper->getXAccelPath($this->exampleFilePath), '/storage/2b/2bp0y7bcg4jnbbv61');
    }
}
