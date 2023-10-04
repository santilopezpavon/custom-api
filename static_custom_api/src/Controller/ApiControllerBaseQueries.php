<?php
namespace Drupal\static_custom_api\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\custom_api\Controller\ApiControllerBase;
use Drupal\Core\Url;


class ApiControllerBaseQueries extends ControllerBase {

    public function getNodeByAlias() {
        $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $content = \Drupal::request()->getContent();
        $alias = FALSE;
        $output = [];

        $entity_cache =  \Drupal::service("static_custom_api.entity_cache");

       if(!empty($content)) {
            $decode = json_decode($content, true);
            if(isset($decode["alias"])) {
                $url = Url::fromUri('internal:' . $decode["alias"]);
                if ($url->isRouted()) {
                    $params = $url->getRouteParameters();
                    $entity_type = key($params);
                    $output["entity_type"] = $entity_type;
                    $output["id"] = $params[$entity_type];
                    
                    if(!empty($_GET["force"])) {
                        $output["entity"] = $entity_cache->getEntityFromDatabase($entity_type, $params[$entity_type], $lang);

                    } else {
                        $output["entity"] = $entity_cache->getEntityFromJSON($entity_type, $params[$entity_type], $lang);

                    }

                }
            }
        }   
        
        return new JsonResponse(["data" => $output], 200);
    }

    private function getEntityFromDatabase($entity_type, $id, $lang) {
        $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
        $entity_db = $storage->load($id);
        $files_cache = \Drupal::service("static_custom_api.files_cache");
        
        if(
            !empty($entity_db) && 
            method_exists($entity_db, "hasTranslation") &&
            $entity_db->hasTranslation($lang)
        ) {
            $entity_db = $entity_db->getTranslation($lang);
        }
        $json_entity = \Drupal::service("serializer")->serialize($entity_db, 'json', []);
        $entity = json_decode($json_entity, true);
        foreach ($entity as $field_name => &$value_field) {
            
            foreach ($value_field as &$value) {
                if(is_array($value) && array_key_exists("target_type", $value) && 
                $files_cache->isEntityTypeJsonAble($value["target_type"]) && is_int($value["target_id"])) {
                   $value["entity"] = $this->getEntityFromDatabase($value["target_type"], $value["target_id"], $lang);
                }
            }
        }
        return $entity;

    }

    private function getEntityJson($entity_type, $id, $lang) {     
  
        $entity = \Drupal::service("static_custom_api.files_cache")->getEntityFile($entity_type, $id, $lang);

        foreach ($entity as $field_name => &$value_field) {
            
            foreach ($value_field as &$value) {
                if(is_array($value) && array_key_exists("target_type", $value)) {
                   $value["entity"] = $this->getEntityJson($value["target_type"], $value["target_id"], $lang);
                }
            }
        }
        return $entity;    
    }
}