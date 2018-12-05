<?php

namespace Stefanvinding\Geckoboard\Dataset;

use GuzzleHttp\Client as HttpClient;

use Exception;


class Request
{

    /**
     * Default endpoint
     *
     * @see https://developer.geckoboard.com/api-reference/curl
     */
    const ENDPOINT = 'https://api.geckoboard.com/datasets/';


    /**
     * Maximum number of records per request
     *
     * @see https://developer.geckoboard.com/api-reference/curl
     */
    const MAX_RECORDS_REQUEST = 500;


    /**
     * HTTP client
     *
     * @var HttpClient
     */
    protected $client;


    /**
     * Configuration
     *
     * @var mixed
     */
    protected $config;


    /**
     * Request constructor.
     *
     * @param array $config
     * @param HttpClient $client
     */
    public function __construct(Array $config, HttpClient $client = null)
    {
        $this->config = $config;

        if (empty($this->config['endpoint']))
            $this->config['endpoint'] = static::ENDPOINT;

        $this->client = $client === null ? $this->client = new HttpClient() : $client;
    }


    /**
     * Create a new dataset
     *
     * @param string $dataset_name
     * @param Row $row
     * @param string|array $unique_by
     * @return \Psr\Http\Message\StreamInterface
     * @throws Exception
     */
    public function createDataset($dataset_name, Row $row, $unique_by = null)
    {

        $endpoint = $this->config['endpoint'] . $dataset_name;

        $request = $this->client->request('PUT', $endpoint, [
            'auth'      => [$this->config['key'], ''],
            'json'      => $this->schemaBuilder($row, $unique_by)
        ]);

        $status_code  = $request->getStatusCode();
        $body         = $this->readBody($request->getBody());

        if ($status_code != 200 && $status_code != 201)
        {
            $error_msg = empty($body->error->message) ? 'Unknown error on create Geckoboard dataset ' . $dataset_name : $body->error->message;
            throw new Exception($error_msg, $status_code);
        }

        return $body;
    }


    /**
     * Delete a dataset.
     *
     * @param $dataset_name
     * @return bool
     * @throws Exception
     */
    public function deleteDataset($dataset_name)
    {
        $endpoint = $this->config['endpoint'] . $dataset_name;

        $request = $this->client->request('DELETE', $endpoint, ['auth' => [$this->config['key'], '']]);

        $status_code  = $request->getStatusCode();
        $body         = $this->readBody($request->getBody());

        if ($status_code != 200)
        {
            $error_msg = empty($body->error->message) ? 'Unknown error on delete Geckoboard dataset ' . $dataset_name : $body->error->message;
            throw new Exception($error_msg, $status_code);
        }

        return true;
    }


    /**
     * Append data to dataset.
     *
     * @param $dataset_name
     * @param array $rows
     * @param string|array $delete_by
     * @return bool|mixed
     */
    public function appendData($dataset_name, Array $rows, $delete_by = null)
    {
        return $this->postData($dataset_name, $rows, $delete_by);
    }


    /**
     * Replace data into dataset.
     *
     * @param $dataset_name
     * @param array $rows
     * @return bool|mixed
     */
    public function replaceData($dataset_name, Array $rows)
    {
        return $this->postData($dataset_name, $rows, [], 'PUT');
    }


    /**
     * Append/Replace data into the dataset
     *
     * @param $dataset_name
     * @param array $rows
     * @param string|array $delete_by
     * @param string $operation
     * @return bool
     * @throws Exception
     */
    protected function postData($dataset_name, Array $rows, $delete_by = null, $operation = 'POST')
    {

        $endpoint = $this->config['endpoint'] . $dataset_name . '/data';

        // Split rows in order to avoid the request limit
        $chunks = array_chunk($rows, static::MAX_RECORDS_REQUEST);

        foreach ($chunks as $chunk)
        {

            $request = $this->client->request($operation, $endpoint, [
                'auth'      => [$this->config['key'], ''],
                'json'      => $this->recordsBuilder($chunk, $delete_by)
            ]);

            $status_code  = $request->getStatusCode();
            $body         = $this->readBody($request->getBody());

            if ($status_code != 200)
            {
                $error_msg = empty($body->error->message) ? 'Unknown error on create Geckoboard dataset ' . $dataset_name : $body->error->message;
                throw new Exception($error_msg, $status_code);
            }
        }

        return true;

    }


    /**
     * Assemble records.
     *
     * @param Row[] $rows
     * @param string|array $delete_by
     * @return array
     */
    protected function recordsBuilder(Array $rows, $delete_by = null)
    {
        $records = ['data' => []];

        foreach ($rows as $row)
            $records['data'][] = $row->getRowValues();

        if (!empty($delete_by))
        {
            $delete_by = is_array($delete_by) ? $delete_by : [$delete_by];

            $record['delete_by'] = array_map('\Stefanvinding\Geckoboard\Dataset\Row::normalizeFieldIndex', $delete_by);
        }

        return $records;
    }


    /**
     * Assemble the schema.
     *
     * @param Row $row
     * @param string|array $unique_by
     * @return array
     */
    protected function schemaBuilder(Row $row, $unique_by = null)
    {
        $record = ['fields' => $row->getRowSchema()];

        if (!empty($unique_by))
        {
            $unique_by = is_array($unique_by) ? $unique_by : [$unique_by];

            $record['unique_by'] = array_map('\Stefanvinding\Geckoboard\Dataset\Row::normalizeFieldIndex', $unique_by);
        }

        return $record;
    }


    /**
     * Read quest body.
     *
     * @param $body
     * @return bool|mixed
     */
    protected function readBody($body)
    {
        if (empty($body))
            return false;

        $body = json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE)
            return false;

        return $body;
    }

}