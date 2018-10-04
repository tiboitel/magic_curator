<?php
namespace App\Models;

trait JsonSerializeTrait
{
	function jsonSerialize()
	{
		$reflect = new \ReflectionClass($this);
		$props   = $reflect->getProperties(\ReflectionProperty::IS_STATIC | \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);
		
		
		$propsIterator = function() use ($props) {
			foreach ($props as $prop) {
				yield $prop->getName() => $this->{$prop->getName()};
			}
		};
		
		return iterator_to_array($propsIterator());
	}
}
