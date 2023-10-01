<?php
namespace Drupal\custom_api\Services;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;


class EntityNormalizer {  

    private $lang = NULL;
    
    public $content = [];

    public $visualization = NULL;

    public $parametersGet = NULL;
    
    public function __construct() {
        $request = \Drupal::request();

        $this->lang =  \Drupal::languageManager()->getCurrentLanguage()->getId();
        
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
        throw new \Exception("The entity is not created", 500);
    }

    public function deleteEntity($entity_type, $id) {
        $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($id);
        if(!empty($entity)) {
            $entity->delete();
        } else {
            throw new \Exception("The entity not exists", 404);
        }       
    }
    
    

    public function getEntity($target_type, $target_id, $schema = []) {
        $response = NULL;
        \Drupal::service("module_handler")->invokeAll('custom_api_response_entity_alter', [&$response, $target_type, $target_id, $this->lang]);

        if(empty($response)) {
            $storage = \Drupal::entityTypeManager()->getStorage($target_type);
            $entity = $storage->load($target_id);
            if(!empty($entity)) {
                if($entity->hasTranslation($this->lang)){
                    $entity = $entity->getTranslation($this->lang);
                }
                $entity_serialized = $this->convertJson($entity, $schema);
                \Drupal::service("module_handler")->invokeAll('custom_api_get_entity_alter', [&$entity_serialized, $target_type, $entity->bundle(), $target_id, $this->lang]);
                return $entity_serialized;
            }
            throw new \Exception("The entity not exists", 404);
        } else {
            $response = $this->jsonSerialize($response, $target_type, $schema);
            return $response;
        }        
    } 

    private function jsonSerialize(&$json_to_serialize, $target_type, $schema = []) {
        $et = $target_type;
        $bundle = $json_to_serialize["type"][0]["target_id"];
        $attributes = \Drupal::service("custom_api.entity_control_fields_show")->generateContextEntityByEntityTypeAndBundle($et, $bundle, $schema);
        foreach ($json_to_serialize as $key => $value) {
            if(!array_key_exists($key, $attributes)) {
                unset($json_to_serialize[$key]);
            }
        }

        foreach ($json_to_serialize as $field_name => &$value_field) {
            if(is_array($value_field)) {
                foreach ($value_field as &$item_array) {
                    if(
                        is_array($item_array) && 
                        array_key_exists("legacy", $item_array) &&
                        array_key_exists("entity_id", $item_array["legacy"]) &&
                        array_key_exists("entity_bundle", $item_array["legacy"])
                    ) {
                        $item_array = $this->getEntity($item_array["legacy"]["entity_type"], $item_array["legacy"]["entity_id"], $attributes[$field_name]);
                       // dump($item_array);
                    }
                }
            }
        }
        return $json_to_serialize;
    }

    

    public function getEntityByAlias($target_type, $alias, $schema = []) {
        $url = Url::fromUri('internal:' . $alias);
        if ($url->isRouted()) {
            $params = $url->getRouteParameters();
            $entity_type = key($params);
            return  $this->getEntity($target_type, $params[$target_type], $schema); 
        }
        throw new \Exception("The entity not exists", 404);
    }

    public function convertJson($entity, $schema = []) {
        $array_entity = json_decode(\Drupal::service("serializer")->serialize($entity, 'json', $schema), true);
        $legacy_data = [
            "entity_type" => $entity->getEntityTypeId(),
            "entity_id" => $entity->id(),
            "entity_bundle" => $entity->bundle()
        ];
        \Drupal::service("module_handler")->invokeAll('custom_api_legacy_data_alter', [&$legacy_data]);
        $array_entity["legacy"] = $legacy_data;;
        try {
            $alias = \Drupal::service('path_alias.manager')->getAliasByPath($entity->toUrl()->toString());
            $array_entity["alias"] = $alias;    
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $array_entity;
    }
    
    public function processField(&$value_field, &$field, $schema= []) {
        $this->cleanField($value_field, $field, $schema);        
    }

    public function cleanField(&$value_field, $field, $schema_base= []) {       
        for ($i=0; $i < count($value_field) ; $i++) { 
            $current = &$value_field[$i];
            if(is_array($current) && array_key_exists("value", $current)) {
                $current = $current["value"];
            } else if (
                is_array($current) &&
                array_key_exists("target_id", $current) && 
                array_key_exists("target_type", $current) && 
                is_numeric($current["target_id"])
            ) {
                if(is_array($current) && $current["target_type"] == 'file') {
                    $file = File::load($current["target_id"]);
                    $image_uri = $file->getFileUri();
                    $file_type = $file->getMimeType();
                    $name = $field->getName();
                    if($this->isImage($file_type)) {
                        $img_styles = $this->processImageStyle($name, $schema_base, $image_uri);  
                        $current["styles_img"] = $img_styles;                    
                    } 
                    $this->processFile($current);
                    
                } else {
                    
                    $name = $field->getName();
                    
                    if(array_key_exists($name, $schema_base)) {
                        $schema = $schema_base[$name];
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