<?php
namespace Drupal\custom_api\Services;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;


class EntityNormalizer {
    
    private $clean = [
        'uuid', 'uid', 'vid', "revision_timestamp", "revision_uid", "revision_log", "path", "revision_translation_affected"
    ];

    private $lang = NULL;
    
    public $content = [];

    public $visualization = NULL;

    public $parametersGet = NULL;
    
    public function __construct() {
        $request = \Drupal::request();

        $this->lang = $request->query->get("lang");
        
        $content = \Drupal::request()->getContent();
        if(!empty($content)) {
            $this->content = json_decode($content, true);
        }       
        
        $this->visualization = $request->query->get("mode");
        $this->parametersGet = \Drupal::request()->query->all();

    }
    

    public function createUpdateEntity($entity_type, $id = null, $schema = []) {
        $entity = NULL;
        $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
        if(!empty($id)) {            
            $entity = $storage->load($id); 
            if($entity->hasTranslation($this->lang)){
                $entity = $entity->getTranslation($this->lang);
            }
        }
        if(is_array($this->content)) {
            if(array_key_exists("schema", $this->content)) {
                unset($this->content["schema"]);
            }
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
            return $this->convertJson($entity, $schema); 
        }
        throw new \Exception("The entity is not created", 1);
    }

    public function deleteEntity($entity_type, $id) {
        $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($id);
        if(!empty($entity)) {
            $entity->delete();
        } else {
            throw new \Exception("The entity not exists", 1);
        }       
    }
    
    public function cleanEntity(&$values) {
        foreach ($this->clean as $ignore) {
            unset($values[$ignore]);                       
        }        
    }

    public function getEntity($target_type, $target_id, $schema = []) {
        $storage = \Drupal::entityTypeManager()->getStorage($target_type);
        $entity = $storage->load($target_id);
        if(!empty($entity)) {
            if($entity->hasTranslation($this->lang)){
                $entity = $entity->getTranslation($this->lang);
            }
            return $this->convertJson($entity, $schema);
        }
        throw new \Exception("The entity not exists", 1);
    }

    public function getEntityByAlias($target_type, $alias, $schema = []) {
        $url = Url::fromUri('internal:' . $alias);
        if ($url->isRouted()) {
            $params = $url->getRouteParameters();
            $entity_type = key($params);
            return  $this->getEntity($target_type, $params[$target_type], $schema); 
        }
        throw new Exception("The entity not exists", 1);
    }

    public function convertJson($entity, $schema = []) {
        return json_decode(\Drupal::service("serializer")->serialize($entity, 'json', $schema), true);

    }
    
    public function processField(&$value_field, &$field, $schema= []) {
        $this->cleanField($value_field, $field, $schema);
        
    }

    public function cleanField(&$value_field, $field, $schema= []) {
        for ($i=0; $i < count($value_field) ; $i++) { 
            $current = &$value_field[$i];
            if(array_key_exists("value", $current)) {
                $current = $current["value"];
            } else if (array_key_exists("target_id", $current) && array_key_exists("target_type", $current) && is_numeric($current["target_id"])) {
                if($current["target_type"] == 'file') {
                    $file = File::load($current["target_id"]);
                    $image_uri = $file->getFileUri();
                    $file_type = $file->getMimeType();
                    $name = $field->getName();
                    if($this->isImage($file_type)) {
                        $img_styles = $this->processImageStyle($name, $schema, $image_uri);                      
                    } 
                    $this->processFile($current);
                    
                } else {
                    $name = $field->getName();
                    if(array_key_exists($name, $schema)) {
                        $schema = $schema[$name];
                    } else {
                        $schema = [];
                    }
                   $current = $this->getEntity($current["target_type"], $current["target_id"], $schema);

                }
            } 
        }   
        
    }

    public function processImageStyle($name, $schema, $image_uri) {
        $img_styles = [];
        if(array_key_exists($name, $schema)) {
            $options = $schema[$name];
            if(!empty($options)){ 
                
                foreach ($options as $option) {
                    $style = ImageStyle::load($option);
                    $url = $style->buildUrl($image_uri);
                    $img_styles[$option] = $url;
                }
            }                            
        }
        return $img_styles;
    }

    public function isImage($file_type) {
        return strpos($file_type, "image/") !== FALSE;
    }

    public function processFile(&$value_field, $img_styles = []) {
        if(array_key_exists("uri", $value_field)) {
            $uri = $value_field["uri"][0];
            $url = file_create_url($uri);           
            $value_field["url"] = [$url];
        }
        foreach ($img_styles as $key => $value) {
            $value_field[$key] = $value;
        }
    }
    
}