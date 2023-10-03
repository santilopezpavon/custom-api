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
        if(!empty($content)) {
            $decode = json_decode($content, true);
            if(isset($decode["alias"])) {
                $url = Url::fromUri('internal:' . $decode["alias"]);
                if ($url->isRouted()) {
                    $params = $url->getRouteParameters();
                    $entity_type = key($params);
                    $output["entity_type"] = $entity_type;
                    $output["id"] = $params[$entity_type];
                    $data = [];
                    $output["entity"] = $this->getEntityJson($entity_type, $params[$entity_type], $lang);
                }
            }
        }     
        
        
        return new JsonResponse(["data" => $output], 200);
    }

    private function getEntityJson($entity_type, $id, $lang) {     
  
        $entity = \Drupal::service("static_custom_api.files_cache")->getEntityFile($entity_type, $id, $lang);

        foreach ($entity as $field_name => &$value_field) {
            
            foreach ($value_field as &$value) {
                if(array_key_exists("target_type", $value)) {
                   $value["entity"] = $this->getEntityJson($value["target_type"], $value["target_id"], $lang);
                }
            }
        }
        return $entity;    
    }
}