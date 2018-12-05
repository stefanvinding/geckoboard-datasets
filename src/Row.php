<?php

namespace Stefanvinding\Geckoboard\Dataset;



class Row
{

    /**
     * Group of types.
     *
     * @var \Stefanvinding\Geckoboard\Dataset\Type\Type[]
     */
    protected $types = [];


    /**
     * Add one or more types to group
     *
     * @param \Stefanvinding\Geckoboard\Dataset\Type\Type|\Stefanvinding\Geckoboard\Dataset\Type\Type[] $type
     * @return $this
     */
    public function addType($type)
    {
        if (!is_array($type))
            $type = [$type];

        $this->types = array_merge($this->types, $type);

        return $this;
    }


    /**
     * Alias of addType
     *
     * @param \Stefanvinding\Geckoboard\Dataset\Type\Type[] $types
     * @return $this
     */
    public function addTypes(array $types)
    {
        $this->addType($types);

        return $this;
    }


    /**
     * Get current row with values as array
     *
     * @return array
     */
    public function getRowValues()
    {
        $row = [];

        foreach ($this->types as $type)
            $row[static::normalizeFieldIndex($type->getName())] = $type->getValue();

        return $row;
    }


    /**
     * Return row/dataset schema
     *
     * @return array
     */
    public function getRowSchema()
    {
        $row = [];

        foreach ($this->types as $type)
            $row[static::normalizeFieldIndex($type->getName())] = $type->schema()->all();

        return $row;
    }


    /**
     * Normalize the field IDS.
     * According to Geckboard fields IDs should contain lowercase alphanumeric values without spaces.
     *
     * @param string $field_id
     * @return string
     */
    public static function normalizeFieldIndex($field_id)
    {
        $field_id = str_replace(' ', '_', $field_id);

        return strtolower($field_id);
    }

}