<?php
/**
 * BlockBuilder
 * @author: neo.generis@gmail.com
 */
class BlockBuilder{
	
	protected $block;
	
	public function getBlock(){
		return $this->block;
	}
	
	//public function createBlock($objectClass,$oid,$objectRoot,$objectParent,$properties,$text){
	public function createBlock($objectClass,$objectParent){
	
		if(class_exists($objectClass)){
			$this->block = new $objectClass($objectParent);
		}else{
			$this->block = new Block($objectParent);
		}
		
		/*
		$this->buildObjectClass($objectClass);
		$this->buildObjectId($oid);
		$this->buildObjectRoot($objectRoot);
		$this->buildObjectParent($objectParent);
		$this->buildTextBlock($text);
		
		if($properties)
			foreach($properties as $property => $value){
				$this->buildProperty($property,$value);
			}
			
		*/
	}
	

	
	public function buildObjectClass($objectClass){
		$this->block->objectClass = $objectClass; 
	}
	public function buildObjectId($oid){
		$this->block->oid = $oid;
	}
	public function buildObjectRoot($objectRoot){
		$this->block->objectRoot = $objectRoot;
	}
	public function buildObjectParent($objectParent){
		$this->block->objectParent = & $objectParent; 	//Tengo la referencia del padre.
	}
	public function buildObjectChild($objectParent){
		$this->block->objectParent->objectChilds[] = & $objectParent; //Paso la referencia del objeto a la lista de hijos que está en el padre.
	}
	
	public function buildProperty($property,$value){
		$this->block->properties[] = $property;
		$this->block->$property = $value;
	}
	public function buildTextBlock($text){
		$this->block->block = $text;
	}
	
	public function buildMediator($mediators){
		$this->block->mediators = $mediators;
	}
}
?>
