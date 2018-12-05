GECKOBOARD DATASET REST CLIENT
==============================

## 1. What is it?

A rest client library for PHP 5.6/7.x that allows to perform CRUD operations against the new Geckoboard Dataset API.


## 2. How it works?

### 2.1 Create / Update dataset

Using the helper:

         $schema =
         [
            ['name' => 'MyAmount'    , 'type' => 'number'  , 'optional' => true                           ],
            ['name' => 'MyText'      , 'type' => 'string'                                                 ],
            ['name' => 'MyDate'      , 'type' => 'datetime', 'optional' => true                           ],
            ['name' => 'Danish Krone', 'type' => 'money'   , 'optional' => false, 'currency_code' => 'DKK']
         ];
         
         
         // Create / Update "mydataset" and set "MyDate" as unique field
         \Stefanvinding\Geckoboard\Dataset\Helper::factory(['key' => 'YOUR API KEY HERE'])->createDataset('mydataset', $schema, 'MyDate');
         
Using low level API:
         
         $schema = (new \Stefanvinding\Geckoboard\Dataset\Row())->addTypes([
            new \Stefanvinding\Geckoboard\Dataset\Type\TypeNumber('MyAmount', true),
            new \Stefanvinding\Geckoboard\Dataset\Type\TypeString('MyText'),
            new \Stefanvinding\Geckoboard\Dataset\Type\TypeDatetime('MyDate', true),
            new \Stefanvinding\Geckoboard\Dataset\Type\TypeMoney('Danish Krone', 'DKK', true)             
         ]);
         
         (new \Stefanvinding\Geckoboard\Dataset\Request(['key' => 'YOUR API KEY HERE']))->createDataset('mydataset', $schema, 'MyDate');



### 2.2 Delete dataset
 
Using the helper:

        \Stefanvinding\Geckoboard\Dataset\Helper::factory(['key' => 'YOUR API KEY HERE'])->deleteDataset('mydataset');

Using low level API:

        (new \Stefanvinding\Geckoboard\Dataset\Request(['key' => 'YOUR API KEY HERE']))->deleteDataset('mydataset');
        

### 2.3 Append data into dataset

Using the helper:

        $records = 
        [
            [
                ['name' => 'MyDate'      , 'type' => 'datetime', 'value' => date('Y-m-d\TH:i:s\Z')],
                ['name' => 'MyAmount'    , 'type' => 'number'  , 'value' => rand(1, 10000)        ],
                ['name' => 'MyText'      , 'type' => 'String'  , 'value' => uniqid()              ],
                ['name' => 'Danish Krone', 'type' => 'money'   , 'value' => rand(1, 10000)        ]
            ],
            // Add more records ....            
        ];
              
        // Unique by "MyDate" field
        \Stefanvinding\Geckoboard\Dataset\Helper::factory(['key' => 'YOUR API KEY HERE'])->appendData('mydataset', $records, ['MyDate']);
        

Using the low level API:


        $records =
        [
            (new \Stefanvinding\Geckoboard\Dataset\Row())->addTypes([
                        (new \Stefanvinding\Geckoboard\Dataset\Type\TypeDatetime('MyDate'))->setValue(date('Y-m-d\TH:i:s\Z')),
                        (new \Stefanvinding\Geckoboard\Dataset\Type\TypeNumber('MyAmount'))->setValue(rand(1, 1000))         ,
                        (new \Stefanvinding\Geckoboard\Dataset\Type\TypeString('MyText'))->setValue(uniqid())                ,               
                        (new \Stefanvinding\Geckoboard\Dataset\Type\TypeMoney('Danish Krone'))->setValue(rand(1, 10000))
            ]),                                
            // Add more records ....            
        ];
               
        // Unique by "MyDate" field
        (new \Stefanvinding\Geckoboard\Dataset\Request(['key' => 'YOUR API KEY HERE']))->appendData('mydataset', $records, ['MyDate']);
        

### 2.4 Replace data into dataset
                
Using the helper:

        $records = 
        [
            [
                ['name' => 'MyDate'      , 'type' => 'datetime', 'value' => date('Y-m-d\TH:i:s\Z')],
                ['name' => 'MyAmount'    , 'type' => 'number'  , 'value' => rand(1, 10000)        ],
                ['name' => 'MyText'      , 'type' => 'String'  , 'value' => uniqid()              ],
                ['name' => 'Danish Krone', 'type' => 'money'   , 'value' => rand(1, 10000)        ]
            ],
            // Add more records ....            
        ];
                     
        \Stefanvinding\Geckoboard\Dataset\Helper::factory(['key' => 'YOUR API KEY HERE'])->replaceData('mydataset', $records);
        

Using the low level API:


        $records =
        [
            (new \Stefanvinding\Geckoboard\Dataset\Row())->addTypes([
                        (new \Stefanvinding\Geckoboard\Dataset\Type\TypeDatetime('MyDate'))->setValue(date('Y-m-d\TH:i:s\Z')),
                        (new \Stefanvinding\Geckoboard\Dataset\Type\TypeNumber('MyAmount'))->setValue(rand(1, 1000))         ,
                        (new \Stefanvinding\Geckoboard\Dataset\Type\TypeString('MyText'))->setValue(uniqid())                ,               
                        (new \Stefanvinding\Geckoboard\Dataset\Type\TypeMoney('Danish Krone'))->setValue(rand(1, 10000))
            ]),                                
            // Add more records ....            
        ];
               
        (new \Stefanvinding\Geckoboard\Dataset\Request(['key' => 'YOUR API KEY HERE']))->replaceData('mydataset', $records);
        
        
### 3. Test

1) Add your API key into tests/BaseTest.php
2) Run PHPUnit ("vendor/phpunit/phpunit/phpunit")