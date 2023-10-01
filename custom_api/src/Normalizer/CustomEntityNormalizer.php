<?php
namespace Drupal\custom_api\Normalizer;

use Drupal\serialization\Normalizer\ContentEntityNormalizer;

/**
 * Converts typed data objects to arrays.
 */
class CustomEntityNormalizer extends ContentEntityNormalizer {
  /**
   * {@inheritdoc}
   */
  public function normalize($entity, $format = NULL, array $context = array()) {     
    $context = \Drupal::service("custom_api.entity_control_fields_show")->generateContextEntity($entity, $context);
    $attributes = parent::normalize($entity, $format, $context);   
    if(!empty($context)) {
      foreach ($attributes as $key => $value) {
       if(!array_key_exists($key, $context)) {
        unset($attributes[$key]);
       }
      }
    }     
    return $attributes; 
  }
}