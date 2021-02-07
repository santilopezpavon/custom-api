<?php
namespace Drupal\custom_api\Normalizer;

use Drupal\serialization\Normalizer\FieldNormalizer;

/**
 * Converts typed data objects to arrays.
 */
class CustomFieldNormalizer extends FieldNormalizer {
/**
   * {@inheritdoc}
   */
  public function normalize($field, $format = NULL, array $context = array()) {    
   
    $classString = "\\Drupal\custom_api\\visualization\\NodePage";
    $teaser = $classString::existo();

    
    $value_field = parent::normalize($field, $format, $context);
    $type = $field->getFieldDefinition()->getType();
    $name = $field->getName();
    
    $parent = $field->getParent()->getEntity();
    $id_type = ucfirst($parent->getEntityType()->id());
    $bundle = ucfirst($parent->bundle());

    $key = $id_type . $bundle;

    
    if(
      array_key_exists($key, $teaser) && 
      array_key_exists($name, $teaser[$key])
      ) {  
            
      \Drupal::service("custom_api.entity_normalize")->processField($value_field);
     
    } else {
      return [];
    }





  
    //exit();
    //kint($name);
    /*if($type == 'entity_reference') {
      kint($field);
      kint($field->getName());
 // kint($value_field);
      kint($field->getParent());
    exit();
    } */
   
    //kint("campo " . $type);
    
    //return NULL;
    return $value_field; 
  }
}