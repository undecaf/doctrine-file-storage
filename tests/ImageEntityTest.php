<?php

use PHPUnit\Framework\TestCase;
use Entity\Image;
use Entity\File;


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';


class ImageEntityTest extends FileEntityTest {

    /** Test image dimensions */
    private const TEST_IMG_DIMS = [
        [ 16, 16 ], [ 1024, 768 ], [ 4000, 3000 ]
    ];

    /** Maximum dimensions of rescaled images */
    private const MAX_DIMS = [ 100, 200, 400, 800 ];


    /**
     * @beforeClass
     */
    public static function prepare(): void {
        FileEntityTest::init();

        foreach (self::TEST_IMG_DIMS as $dim) {
            foreach (self::randomImageFiles($dim[0], $dim[1]) as $tmpName) {
                FileEntityTest::$uploaded[] = [
                    'tmp_name' => $tmpName,
                    'name' => basename($tmpName),
                    'size' => filesize($tmpName),
                    'width' => $dim[0],
                    'height' => $dim[1],
                ];
            }
        }
    }


    /**
     * Returns an array of new JPEG, PNG and GIF files each of which contains
     * an image with randomly distributed and randomly coloured pixels.
     */
    protected static function randomImageFiles(int $width, int $height): array {
        $img = imagecreatetruecolor($width, $height);
    
        for ($i = 0; $i < $width*$height/8; $i++) {
            imagesetpixel(
                $img, mt_rand(0, $width-1), mt_rand(0, $height-1), 
                imagecolorallocate($img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)));
        }

        $imgFiles = [];

        $imgFiles[] = $imgFile = tempnam(sys_get_temp_dir(), 'jpg');
        imagejpeg($img, $imgFile);

        $imgFiles[] = $imgFile = tempnam(sys_get_temp_dir(), 'png');
        imagepng($img, $imgFile);

        $imgFiles[] = $imgFile = tempnam(sys_get_temp_dir(), 'gif');
        imagegif($img, $imgFile);

        imagedestroy($img);
        return $imgFiles;
    }


    /**
     * Asserts that images are being constructed correctly.
     */
    public function testConstructor(): void {
        $this->runConstructorTests(function($f): Image {
            $entity = new Image($f);
            $this->assertInstanceOf(File::class, $entity);
            return $entity;
        });
    }


    /**
     * Asserts that images are being scaled correctly.
     * 
     * @depends testConstructor
     */
    public function testScaling(): void {
        foreach (self::$uploaded as $f) {
            $mimetype = mime_content_type($f['tmp_name']);

            foreach (self::MAX_DIMS as $max_dim) {
                $entity = new Image($f, $max_dim);

                $this->assertTrue($entity->getWidth() === $max_dim || $entity->getHeight() === $max_dim);
                $this->assertLessThanOrEqual($max_dim, $entity->getWidth());
                $this->assertLessThanOrEqual($max_dim, $entity->getHeight());
                $this->assertSame($mimetype, $entity->getMimetype());

                self::$entities[] = $entity;
            }
        };
    }


    /**
     * Asserts that Image entities are being persisted correctly.
     * 
     * @depends testConstructor
     */
    public function testPersistence(): void {
        $this->runPersistenceTests(function($entity, $result): void {
            $this->assertSame($entity->getWidth(), $result->getWidth());
            $this->assertSame($entity->getHeight(), $result->getHeight());
        });
    }

}
