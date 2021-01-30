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
    $attributes = parent::normalize($entity, $format, $context); 
    \Drupal::service("custom_api.entity_normalize")->cleanEntity($attributes);    
    return $attributes; 
  }
}