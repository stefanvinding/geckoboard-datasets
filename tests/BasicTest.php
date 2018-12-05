<?php

namespace Stefanvinding\Geckoboard\Dataset\Tests;

use Stefanvinding\Geckoboard\Dataset\Type as DataType;
use Stefanvinding\Geckoboard\Dataset\Row as DatasetRow;


class BasicTest extends BaseTest
{

    const DATASET_NAME = 'test_dataset';


    /**
     * Request object
     *
     * @var \Stefanvinding\Geckoboard\Dataset\Request
     */
    protected static $request;


    /**
     * Instantiate a common request object for all the entire tests
     */
    public static function setUpBeforeClass()
    {
        static::$request = new \Stefanvinding\Geckoboard\Dataset\Request(static::$config);
    }


    /**
     * Test dataset creation.
     *
     * @group Stefanvinding.geckoboard-dataset
     */
    public function test_create_dataset()
    {

        $dataset = (new DatasetRow())->addTypes([
            new DataType\TypeDate('test_date'),
            new DataType\TypeString('test_string'),
            new DataType\TypeMoney('test_money', 'EUR', true)
        ]);


        $response = static::$request->createDataset(static::DATASET_NAME, $dataset);

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
            $rows[$i] =  (new DatasetRow())->addTypes([
                (new DataType\TypeDate('test_date'))->setValue(date('Y-m-d', $time)),
                (new DataType\TypeString('test_string'))->setValue(uniqid()),
                (new DataType\TypeMoney('test_money'))->setValue(rand(0, 1000))
            ]);

            $time += 86400;
        }


        if ($operation === 'append')
            $response = static::$request->appendData(static::DATASET_NAME, $rows);
        else
            $response = static::$request->replaceData(static::DATASET_NAME, $rows);

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
     * @depends test_replace_dataset
     * @group Stefanvinding.geckoboard-dataset
     */
    public function test_money_cast()
    {
        $row =
        [
            (new DatasetRow())->addTypes([
                (new DataType\TypeDate('test_date'))->setValue(date('Y-m-d')),
                (new DataType\TypeString('test_string'))->setValue(uniqid()),
                (new DataType\TypeMoney('test_money'))->setValueAsFloat(10.2)
            ])
        ];

        $response = static::$request->appendData(static::DATASET_NAME, $row);

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
        $this->assertTrue(static::$request->deleteDataset(static::DATASET_NAME));
    }

}