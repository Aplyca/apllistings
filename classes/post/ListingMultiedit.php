
<?php 

abstract class ListingMultiedit
{
	
	var $object;
	var $attributesIndex;
			
	protected function __construct($object) 
    {    
		$this->object = $object;	
		$this->setAttributesIndex();
    }   
	
    public function getAttributes($step)
    {
    	$attributes = array();
    	$dataMap = $this->object->dataMap();
    	$attributesIndex = $this->attributesIndexByStep($step);
    	foreach($attributesIndex as $index)
    	{
    		$attributes[$index] = $dataMap[$index];
    	}    	    
    	$attributes = $this->filterAttributesByPackage($step, $attributes);
    	return $attributes;    	    
    } 
    
    protected function filterAttributesByPackage($step, $attributes)
    {    
    	return $attributes;
    } 
    
    static function plainArray($array)
    {
    	$plainArray = array();
    	foreach($array as $item)
    	{
    		foreach($item as $subitem)
    		{
    			$plainArray[] = $subitem;
    		}    		
    	}
    	return $plainArray;
    }
    
    public function getAttributesList()
    {
   		$attributes = array();
    	$dataMap = $this->object->dataMap();    	
    	foreach($this->attributesIndex as $step => $stepAttributes )
    	{
    		$attributes[$step] = array();
    		foreach($stepAttributes as $index)
    		{
    			$attributes[$step][$index] = $dataMap[$index];	
    		}    		
    	}
    	return $attributes;  
    }
    
    public function checkAttributesCompleted()
    {
    	$editStepAttributes = $this-> getAttributesList();
    	$initialEditStep = false;
		foreach($editStepAttributes as $step => $stepAttributes)
		{
			foreach($stepAttributes as $attribute)
			{			
				if($attribute->contentClassAttributeIsRequired() == true && $attribute->hasContent() == false)
				{
					$initialEditStep = $step;
					break;
				}
			}					 
		}
		return $initialEditStep;
    }
    
 	static function instance($objectId)
    {
    	$object = eZContentObject::fetch($objectId);
    	
    	if($object instanceof eZContentObject)
    	{
    		return new ListingMultiedit($object);  		
    	}
    	else return false;    	
    }    
    
    abstract protected function setAttributesIndex();
    abstract protected function attributesIndexByStep($step);
    abstract function getTemplatePathByStep($step);
    abstract function getStepsNumber();
    abstract function checkFieldSets($step);
    abstract function getFieldSets($step);
    abstract function republishRelatedObjects($step, $postingObject);  

}

?>