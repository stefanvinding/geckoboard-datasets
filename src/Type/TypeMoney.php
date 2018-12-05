<?php

namespace Stefanvinding\Geckoboard\Dataset\Type;


class TypeMoney extends Type
{


    /**
     * Is optional.
     *
     * @var bool
     */
    protected $is_optional;


    /**
     * Currency code (ISO-4217).
     *
     * @see https://en.wikipedia.org/wiki/ISO_4217#Active_codes
     * @var string
     */
    protected $currency_code;


    /**
     * DateType constructor.
     *
     * @param string $name
     * @param string $currency_code
     * @param bool $optional
     */
    public function __construct($name, $currency_code = 'EUR', $optional = false)
    {
        parent::__construct($name);

        $this->is_optional   = $optional;
        $this->currency_code = strtoupper($currency_code);
    }


    /**
     * Set money value (As Int)
     *
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = (int) $value;

        return $this;
    }


    /**
     * Set money value (As Float)
     *
     * @param $value
     * @param $decimals
     * @return $this
     */
    public function setValueAsFloat($value, $decimals = 2)
    {
        $this->value = (int) number_format($value, $decimals, '', '');

        return $this;
    }


    /**
     * Retrieve the type schema.
     *
     * @return \Illuminate\Support\Collection
     */
    public function schema()
    {
        $collect = parent::schema();

        $collect->put('optional', $this->is_optional);
        $collect->put('currency_code', $this->currency_code);

        return $collect;
    }

}