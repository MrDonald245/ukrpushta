<?php
/**
 * Created by PhpStorm.
 * User: eugene
 * Date: 04/04/18
 * Time: 14:23
 */

abstract class EntityBase
{
    abstract function toArray();

    /**
     * EntityBase constructor.
     *
     * @param array|string|null $data Could be as an array, json, or null
     */
    abstract function __construct($data = null);

    /**
     * @param array $data
     * @return void
     */
    abstract function initWithArray($data);

    /**
     * @return array
     */
    protected function objectToArray()
    {
        $reflectionClass = new ReflectionClass(get_class($this));
        $array = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            if ($property->getValue($this) != null) {
                $array[$property->getName()] = $property->getValue($this);
            }
            $property->setAccessible(false);
        }

        return $array;
    }

    /**
     * @param array $array
     * @return void
     */
    protected function arrayToObject($array)
    {
        foreach ($array as $key => $value) {
            $this->{$key} = $value;
        }
    }
}