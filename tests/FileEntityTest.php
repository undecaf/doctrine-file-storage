<?php

use PHPUnit\Framework\TestCase;
use Entity\File;
use Entity\Persistent;

use function PHPUnit\Framework\assertSame;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';


class FileEntityTest extends TestCase {

    private const SIZES = [
        0, 42, 1222333, 11222333
    ];

    static $uploaded;
    static $entities;


    /**
     * @beforeClass
     */
    public static function prepare(): void {
        self::init();

        foreach (self::SIZES as $size) {
            $tmpName = self::randomDataFile($size);
            self::$uploaded[] = [
                'tmp_name' => $tmpName,
                'name' => $tmpName,
                'size' => filesize($tmpName),
            ];
        }
    }


    /**
     * @afterClass
     */
    public static function cleanup(): void {
        foreach (self::$uploaded as $f) {
            unlink($f['tmp_name']); 
        }
    }


    protected static function init(): void {
        self::$uploaded = [];
        self::$entities = [];
    }


    /**
     * Returns the name of a new file with the specified size
     * and some random bytes.
     */
    private static function randomDataFile(int $size): string {
        $tmpName = tempnam(sys_get_temp_dir(), '');
        $f = fopen($tmpName, 'wb');
        
        while ($size > 0) {
            $step = min(mt_rand(1, max($size/100, 1)), $size);
            fseek($f, $step-1, SEEK_END);
            fwrite($f, chr(mt_rand(0, 255)));
            $size -= $step;
        }

        fclose($f);
        return $tmpName;
    }


    /**
     * Asserts that File entities are being constructed correctly.
     */
    public function testConstructor(): void {
        $this->runConstructorTests(function($f): File {
            $entity = new File($f);

            $this->assertInstanceOf(Persistent::class, $entity);
            $this->assertSame($f['name'], $entity->getName());
            $this->assertSame($f['size'], $entity->getSize());
            $this->assertSame(mime_content_type($f['tmp_name']), $entity->getMimetype());
            $this->assertIsResource($entity->getContent());
    
            return $entity;
        });
    }


    protected function runConstructorTests($assertFunc): void {
        foreach (self::$uploaded as $f) {
            self::$entities[] = $assertFunc($f);
        }
    }


    /**
     * Asserts that File entities are being persisted correctly.
     * 
     * @depends testConstructor
     */
    public function testPersistence(): void {
        $this->runPersistenceTests(function($entity, $result): void {
            // Verify scalar properties
            $this->assertMatchesRegularExpression('/[\da-f]{8}-([\da-f]{4}-){3}[\da-f]{12}/i', $result->getUuid());
            $this->assertSame($entity->getName(), $result->getName());
            $this->assertSame($entity->getSize(), $result->getSize());
            $this->assertSame($entity->getMimetype(), $result->getMimetype());
    
            // Copy the persisted content to the file system
            $tmp = tmpfile();
            $tmpName = stream_get_meta_data($tmp)['uri'];
            stream_copy_to_stream($result->getContent(), $tmp);
            fclose($result->getContent());
            fflush($tmp);

            // Verify the content
            $this->assertFileEquals($entity->getName(), $tmpName);

            // Clean up
            fclose($tmp);
        });
    }


    /**
     * Runs the persistence tests and asserts correctness by calling
     * an assert function.
     */
    protected function runPersistenceTests($assertFunc): void {
        global $entityManager;

        try {
            // Prevent auto-commit
            $entityManager->getConnection()->beginTransaction();

            // Persist entities
            foreach (self::$entities as $entity) {
                $entityManager->persist($entity);

                // Reclaim memory
                $entityManager->flush();
                $entityManager->clear();
            }

            // Verify that the entities were persisted correctly
            foreach (self::$entities as $entity) {
                $result = $entityManager->createQueryBuilder()
                    ->select('e')
                    ->from(get_class($entity), 'e')
                    ->where('e.uuid = :uuid')
                    ->setParameter('uuid', $entity->getUuid())
                    ->getQuery()
                    ->getSingleResult();
                
                // Assert correctness
                $assertFunc($entity, $result);

                // Reclaim memory
                $entityManager->clear();
            }

        } finally {
            // Do not commit any changes
            $entityManager->getConnection()->rollBack();
        }
    }

}