<?php

namespace Stefanvinding\Geckoboard\Dataset\Tests;

use Stefanvinding\Geckoboard\Dataset\Helper;


class HelperTest extends BaseTest
{

    const DATASET_NAME = 'test_helper_dataset';


    /**
     * Test dataset creation.
     *
     * @group Stefanvinding.geckoboard-dataset
     */
    public function test_create_dataset()
    {
        //Helper::factory(static::$config)->deleteDataset(static::DATASET_NAME);

        $schema =
        [
            ['name' => 'MyAmount', 'type' => 'Number', 'optional' => true],
            ['type' => 'String', 'name' => 'MyText'],
            ['name' => 'MyDate', 'type' => 'datetime', 'optional' => true],
            ['optional' => true, 'type' => 'money', 'currency_code' => 'DKK', 'name' => 'My Danish Krone']
        ];

        $response = Helper::factory(static::$config)->createDataset(static::DATASET_NAME, $schema, 'MyDate');

        $this->assertEquals(static::DATASET_NAME, $response->id);
    }


    /**
     * Test dataset append data.
     *
     * @depends test_create_dataset
     * @group Stefanvinding.geckoboard-dataset
     *
     * @param string    $operation  Operation type
     * @param int       $row_number Number of rows
     */
    public function test_append_dataset($operation = 'append', $row_number = 600)
    {

        $rows = [];

        $time = time();

        for ($i = 0; $i <= $row_number; $i++)
        {
            $rows[$i] =
            [
                ['name' => 'MyDate', 'type' => 'datetime', 'value' => date('Y-m-d\TH:i:s\Z', $time)],
                ['type' => 'number', 'name' => 'MyAmount', 'value' => rand(1, 10000)],
                ['name' => 'MyText', 'type' => 'String'  , 'value' => uniqid()],
                ['name' => 'My Danish Krone', 'type' => 'money', 'value' => rand(1, 10000)]
            ];

            $time += 86400;
        }

        $request = new Helper(static::$config);


        if ($operation === 'append')
            $response = $request->appendData(static::DATASET_NAME, $rows);
        else
            $response = $request->replaceData(static::DATASET_NAME, $rows);

        $this->assertTrue($response);
    }


    /**
     * Test dataset replace data.
     *
     * @depends test_append_dataset
     * @group Stefanvinding.geckoboard-dataset
     */
    public function test_replace_dataset()
    {
        $this->test_append_dataset('replace', 10);
    }



    /**
     * Test if row with casted "Money" value is pushed.
     *
     * @depends test_append_dataset
     * @group Stefanvinding.geckoboard-dataset
     */
    public function test_money_cast()
    {
        $row =
        [
            [
                ['name' => 'MyDate', 'type' => 'datetime', 'value' => '2015-01-01T00:00:00'],
                ['type' => 'number', 'name' => 'MyAmount', 'value' => 0],
                ['name' => 'MyText', 'type' => 'String'  , 'value' => uniqid()],
                ['name' => 'My Danish Krone', 'type' => 'money', 'value_float' => 15.3333333]
            ]
        ];

        $request = new Helper(static::$config);
        $response = $request->appendData(static::DATASET_NAME, $row);

        $this->assertTrue($response);
    }


    /**
     * Test delete dataset.
     *
     * @depends test_money_cast
     * @group Stefanvinding.geckoboard-dataset
     */
    public function test_delete_dataset()
    {
        $this->assertTrue(Helper::factory(static::$config)->deleteDataset(static::DATASET_NAME));
    }

}