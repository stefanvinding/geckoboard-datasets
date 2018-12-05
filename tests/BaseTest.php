<?php

namespace Stefanvinding\Geckoboard\Dataset\Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{


    /**
     * Geckboard API conf (Add your key if is empty)
     *
     * @var array
     */
    protected static $config = ['key' => ''];


    /**
     * This is not a test, tt just check that API key exists.
     *
     * @group Stefanvinding.geckoboard-dataset
     */
    public function test_config()
    {
        $this->assertNotEmpty(self::$config['key'], 'API is key is required in order to execute tests. See BaseTest.php');
    }

}