<?php
/*
 * This class was auto-generated from the API references found at
 * https://epayments-api.developer-ingenico.com/s2sapi/v1/
 */
namespace Ingenico\Connect\Sdk\Domain\Definitions;

use Ingenico\Connect\Sdk\DataObject;
use UnexpectedValueException;

/**
 * @package Ingenico\Connect\Sdk\Domain\Definitions
 */
class PaymentProductFilter extends DataObject
{
    /**
     * @var string[]
     */
    public $groups = null;

    /**
     * @var int[]
     */
    public $products = null;

    /**
     * @param object $object
     * @return $this
     * @throws UnexpectedValueException
     */
    public function fromObject($object)
    {
        parent::fromObject($object);
        if (property_exists($object, 'groups')) {
            if (!is_array($object->groups) && !is_object($object->groups)) {
                throw new UnexpectedValueException('value \'' . print_r($object->groups, true) . '\' is not an array or object');
            }
            $this->groups = [];
            foreach ($object->groups as $groupsElementObject) {
                $this->groups[] = $groupsElementObject;
            }
        }
        if (property_exists($object, 'products')) {
            if (!is_array($object->products) && !is_object($object->products)) {
                throw new UnexpectedValueException('value \'' . print_r($object->products, true) . '\' is not an array or object');
            }
            $this->products = [];
            foreach ($object->products as $productsElementObject) {
                $this->products[] = $productsElementObject;
            }
        }
        return $this;
    }
}
