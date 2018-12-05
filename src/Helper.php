<?php


namespace Stefanvinding\Geckoboard\Dataset;

use GuzzleHttp\Client as HttpClient;

use Exception;
use ReflectionClass;


class Helper
{

    protected $request;


    /**
     * Helper constructor.
     *
     * @param array $config
     * @param HttpClient|null $client
     */
    public function __construct(Array $config, HttpClient $client = null)
    {
        $this->request = new Request($config, $client);
    }


    /**
     * Factory method.
     *
     * @param array $config
     * @param HttpClient|null $client
     * @return static
     */
    public static function factory(Array $config, HttpClient $client = null)
    {
        return new static($config, $client);
    }


    /**
     * Create a new dataset.
     *
     * @param string $dataset_name
     * @param array $schema
     * @param array $unique_by
     * @return array
     * @throws Exception
     */
    public function createDataset($dataset_name, Array $schema, $unique_by = [])
    {

        $row = new Row();

        foreach ($schema as $type)
        {
            if (empty($type['name']) || empty($type['type']))
                throw new Exception('Name or type is missing');

            $reflection = new ReflectionClass('Stefanvinding\Geckoboard\Dataset\Type\Type' . ucfirst($type['type']));
            $params = $reflection->getConstructor()->getParameters();

            $to_pass = [];

            foreach ($params as $param)
            {
                $param_name = $param->getName();

                if (isset($type[$param_name]))
                    $to_pass[$param_name] = $type[$param_name];
            }

            $row->addType($reflection->newInstanceArgs($to_pass));
        }

        return $this->request->createDataset($dataset_name, $row, $unique_by);
    }


    /**
     * Delete a dataset.
     *
     * @param string $dataset_name
     * @return bool
     */
    public function deleteDataset($dataset_name)
    {
        return $this->request->deleteDataset($dataset_name);
    }


    /**
     * Append data.
     *
     * @param string $dataset_name
     * @param array $records
     * @param array $delete_by
     * @return bool|mixed
     */
    public function appendData($dataset_name, Array $records, $delete_by = [])
    {
        return $this->cuData($dataset_name, $records, 'append', $delete_by);
    }


    /**
     * Replace data.
     *
     * @param $dataset_name
     * @param array $records
     * @return bool|mixed
     */
    public function replaceData($dataset_name, Array $records)
    {
        return $this->cuData($dataset_name, $records, 'replace');
    }


    /**
     * Perform a Create (Append) and Update operation.
     *
     * @param string $dataset_name
     * @param array $records
     * @param string $operation
     * @param array $delete_by
     * @return bool|mixed
     * @throws Exception
     */
    protected function cuData($dataset_name, Array $records, $operation = 'append', $delete_by = [])
    {

        $drecords = [];

        foreach ($records as $fields)
        {
            $drow = new Row();

            foreach ($fields as $field)
            {

                if (empty($field['name']) || empty($field['type']))
                    throw new Exception('Name or type is missing');

                $reflection = new ReflectionClass('Stefanvinding\Geckoboard\Dataset\Type\Type' . ucfirst($field['type']));

                // Check if value is provided
                if (!isset($field['value']) && (!isset($field['value_float']) || !$reflection->hasMethod('setValueAsFloat')))
                {
                    throw new Exception('Value is missing');
                }

                /**
                 * @var \Stefanvinding\Geckoboard\Dataset\Type\Type $type
                 */
                $type = $reflection->newInstance($field['name']);

                if (isset($field['value_float']))
                    $type->setValueAsFloat($field['value_float']);
                else
                    $type->setValue($field['value']);

                $drow->addType($type);

            }

            $drecords[] = $drow;

        }

        if ($operation === 'append')
            return $this->request->appendData($dataset_name, $drecords, $delete_by);
        else
            return $this->request->replaceData($dataset_name, $drecords);

    }

}