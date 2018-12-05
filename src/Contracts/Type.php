<?php

namespace Stefanvinding\Geckoboard\Dataset\Contracts;


interface Type
{


    /**
     * Retrieve the type schema.
     *
     * @return \Illuminate\Support\Collection
     */
    public function schema();


    /**
     * Get the field name.
     *
     * @return mixed
     */
    public function getName();


    /**
     * Get type value
     *
     * @return mixed
     */
    public function getValue();


    /**
     * Set type value.
     *
     * @param $value
     * @return void
     */
    public function setValue($value);

}