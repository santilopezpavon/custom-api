<?php
namespace Drupal\custom_api\Services;

/**
 * Defines a class to gestion the fields to Show. 
 */
class EntityControlFieldsShow {  

   /**
    * The properties mandatory to show.
    *
    * @var Array
    */
    private $mandatory_properties = ["title", "metatag", "name", "alias", "nid", "id","tid", "type", "langcode"];

   /**
    * The structure for print the Entity and the childs.
    *
    * @param Entity $entity
    *      The Entity for get the fields to show.
    *
    * @param Array $context
    *      The Array with the information to print for the Entity.
    *
    * @return array
    *      The Array with the fields to print. 
    */
    public function generateContextEntity($entity, $context = []) {
        $view_display_array = $this->getDispayInfo($entity, $context);
        if($view_display_array === FALSE) {
          return $context;
        }
        $array_display = $view_display_array->toArray()["content"];
        foreach ($array_display as $key => $value) {
          $options = [];
          if(array_key_exists("settings", $value)) {
            if(array_key_exists("view_mode", $value["settings"]) ) {
              $options["display"] = $value["settings"]["view_mode"];
            }
            if(array_key_exists("image_style", $value["settings"]) ) {
              $options = [$value["settings"]["image_style"]];
            }          
          }
          $array_display[$key] = $options;  
        }
        foreach ($this->mandatory_properties as $value) {
          $array_display[$value] = [];
        }
        return $array_display;        
    }

    /**
    * The display information for the entity.
    *
    * @param Entity $entity
    *      The Entity for get the fields to show.
    *
    * @param Array $context
    *      The Array with the information to print for the Entity.
    *
    * @return \Drupal\Core\Entity\Entity\EntityViewDisplay
    *      The EntityViewDisplay for the entity. 
    */
    public function getDispayInfo($entity, $context) {
      if(array_key_exists("display", $context)) {
        $display = $context["display"];
        return \Drupal::entityTypeManager()->getStorage('entity_view_display')->load($entity->getEntityType()->id() . '.' . $entity->bundle() . '.' . $display);
      }
      return FALSE;
    }

}