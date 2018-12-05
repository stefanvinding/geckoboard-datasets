<?php

namespace Stefanvinding\Geckoboard\Dataset\Type;


class TypeNumber extends Type
{

    /**
     * Is optional.
     *
     * @var bool
     */
    protected $is_optional;


    /**
     * DateType constructor.
     *
     * @param string $name
     * @param bool $optional
     */
    public function __construct($name, $optional = false)
    {
        parent::__construct($name);

        $this->is_optional = $optional;
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

        return $collect;
    }


}