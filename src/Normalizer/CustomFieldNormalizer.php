<?php
namespace Drupal\custom_api\Normalizer;

use Drupal\serialization\Normalizer\FieldNormalizer;

/**
 * Converts typed data objects to arrays.
 */
class CustomFieldNormalizer extends FieldNormalizer {
  public $pre_field = null;
  public $pre_entity = null;

  public $structure = [];
/**
   * {@inheritdoc}
   */
  public function normalize($field, $format = NULL, array $context = array()) { 
    if(count($context) > 0) {
      $name_field = $field->getName();
      if(!array_key_exists($name_field, $context)) {
        return NULL;
      }
    }
    $value_field = parent::normalize($field, $format, $context);
    \Drupal::service("custom_api.entity_normalize")->processField($value_field, $field, $context);
    return $value_field; 
    /*$type = $field->getFieldDefinition()->getType();
    $name = $field->getName();
    
    $parent = $field->getParent()->getEntity();
    $id_type = ucfirst($parent->getEntityType()->id());
    $bundle = ucfirst($parent->bundle());
    
    $key = $id_type . $bundle;*/
    
   
    
    
  }
}