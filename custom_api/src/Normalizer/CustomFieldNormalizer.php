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
    $value_field = parent::normalize($field, $format, $context);  
    return $value_field;     
  }
}