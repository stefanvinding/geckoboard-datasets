<?php

namespace Stefanvinding\Geckoboard\Dataset\Type;


abstract class Type implements \Stefanvinding\Geckoboard\Dataset\Contracts\Type
{


    /**
     * Type.
     *
     * @var string
     */
    protected $type = '';


    /**
     * Type name.
     *
     * @var string
     */
    protected $name = '';


    /**
     * Type value.
     *
     * @var mixed
     */
    protected $value;


    /**
     * Type constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        // Detect type and do some "automagical" stuff
        $type = get_class($this);
        $type = strtolower(str_replace($this->_getSuper(), '', $type));

        $this->type = $type;
        $this->name = $name;
    }


    /**
     * Retrieve the type schema.
     *
     * @return \Illuminate\Support\Collection
     */
    public function schema()
    {
        return collect([
            'type'  => $this->type,
            'name'  => $this->name
        ]);
    }


    /**
     * Get datatype value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }



    /**
     * Set a value into datatype.
     *
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }


    /**
     * Get the field name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Get super parent class.
     *
     * @return string
     */
    private function _getSuper()
    {
        $super = get_class($this);

        while ($parent = get_parent_class($super))
        {
            $super = $parent;
        }

        return $super;
    }

}