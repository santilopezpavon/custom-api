<?php
namespace Drupal\custom_api\Services;
use Drupal\Core\Url;


class EntityNormalizer {
    private $clean = [
        'uuid', 'uid', 'vid', "revision_timestamp", "revision_uid", "revision_log", "path", "revision_translation_affected"
    ];

    private $lang = NULL;
    private $content = NULL;
    
    public function __construct() {
        $request = \Drupal::request();
        $this->lang = $request->query->get("lang");
        $this->content = json_decode(\Drupal::request()->getContent(), true);
    }
    

    public function createUpdateEntity($entity_type, $id = null) {
        $entity = NULL;
        $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
        if(!empty($id)) {            
            $entity = $storage->load($id); 
        }
        if(is_array($this->content)) {
            if($entity === NULL) {
                $entity = $storage->create($this->content); 
            } else {
                foreach ($this->content as $key => $value) {
                    $entity->set($key, $value);
                }
            }            
        }
        if($entity !== NULL) {
            $entity->save();
        }
        return $entity;
    }

    public function deleteEntity($entity_type, $id) {
        $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($id);
        $entity->delete();
    }
    
    public function cleanEntity(&$values) {
        foreach ($this->clean as $ignore) {
            unset($values[$ignore]);                       
        }        
    }

    public function getEntity($target_type, $target_id) {
        $storage = \Drupal::entityTypeManager()->getStorage($target_type);
        $entity = $storage->load($target_id);
        if($entity->hasTranslation($this->lang)){
            $entity = $entity->getTranslation($this->lang);
        }
        return $this->convertJson($entity);

    }

    public function convertJson($entity) {
        return json_decode(\Drupal::service("serializer")->serialize($entity, 'json'), true);

    }

    public function cleanField(&$value_field) {
        for ($i=0; $i < count($value_field) ; $i++) { 
            $current = &$value_field[$i];
            if(array_key_exists("value", $current)) {
                $current = $current["value"];
            } else if (array_key_exists("target_id", $current) && array_key_exists("target_type", $current) && is_numeric($current["target_id"])) {
                $current = $this->getEntity($current["target_type"], $current["target_id"]);
            }
            $this->processImg($current);
        }        
    }

    public function processImg(&$value_field) {
        if(array_key_exists("uri", $value_field)) {
            $uri = $value_field["uri"][0];
            $url = file_create_url($uri);           
            $value_field["url"] = [$url];
        }
    }
    
}