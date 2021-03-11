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
    
    if(array_key_exists("display", $context)) {
      $display = $context["display"];
      $view_display_array = \Drupal::entityTypeManager()->getStorage('entity_view_display')->load($entity->getEntityType()->id() . '.' . $entity->bundle() . '.' . $display);
      if(!empty($view_display_array)) {
        $array_display = $view_display_array->toArray()["content"];
        foreach ($array_display as $key => $value) {
          $options = [];
          if(
            array_key_exists("settings", $value)        
          ) {
            if(array_key_exists("view_mode", $value["settings"]) ) {
              $options["display"] = $value["settings"]["view_mode"];
            }
            if(array_key_exists("image_style", $value["settings"]) ) {
              $options = [$value["settings"]["image_style"]];
            }          
          }
          $array_display[$key] = $options;          
        }        
        $array_display["title"] = [];
        $array_display["metatag"] = [];
        $array_display["alias"] = [];
        $array_display["nid"] = [];
        $array_display["type"] = [];
        $array_display["langcode"] = [];
        $context = $array_display;
      }    
    } 
    
    
    
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