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
    $entityNormalizer = \Drupal::service("custom_api.entity_normalize");
    $entityNormalizer->cleanEntity($attributes);
    if(!empty($context)) {
      foreach ($attributes as $key => $value) {
       if(!array_key_exists($key, $context)) {
        unset($attributes[$key]);
       }
      }
    }


    /*$classString = "\\Drupal\custom_api\\visualization\\NodePage";
  $teaser = $classString::existo();
  
    
    $id_type = ucfirst($entity->getEntityType()->id());
    $bundle = ucfirst($entity->bundle());

    $entityNormalizer = \Drupal::service("custom_api.entity_normalize");
    $entityNormalizer->cleanEntity($attributes);*/   

    /*foreach ($attributes as $key => $value) {
      if(empty($value)) {
        unset($attributes[$key]);
      }
    }*/
    
    
    //kint($teaser);
   /* $classString = "\\Drupal\custom_api\\visualization\\" . $id_type . $bundle;
    if(class_exists($classString)) {
      $visualization = $entityNormalizer->visualization;
      kint("hola");
      if (property_exists ($classString , $visualization ) ) {
        kint("hola");
      }
    } */
    

     
    return $attributes; 
  }
}