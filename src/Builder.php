<?php

namespace Stefanvinding\Geckoboard\Dataset;




class Builder
{

    /**
     * @var \Stefanvinding\Geckoboard\Dataset\Contracts\Type[]
     */
    protected $types = [];


    /**
     * Add a data type to the builder list
     *
     * @param $type
     */
    public function addType($type)
    {
        if (is_array($type))
            $this->types = array_merge($this->types, $type);
        else
            $this->types[] = $type;
    }

}