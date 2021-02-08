<?php
namespace Drupal\custom_api\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;



class ApiController extends ControllerBase {

    public function getEntityIndex($entity_type, $id) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        $schema = [
            "title" => [],
            "field_articles" => [
                "nid" => [],
                "title" => [],
                "field_image" => [],
                "field_media" => [
                    "field_media_image" => []
                ],
            ],            
        ];


        $output = $normalize->getEntity($entity_type, $id, $schema);
        return new JsonResponse($output, 200);
    }

    public function createEntity($entity_type) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        $resp = \Drupal::service("custom_api.entity_responses");
        try {
            $entity = $normalize->createUpdateEntity($entity_type);  
            $entity = $normalize->convertJson($entity); 
            return $resp->prepareResponse($entity);
        } catch (\Exception $th) {
            return $resp->prepareError($th);
        }        
    }

    public function updateEntity($entity_type, $id) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        $resp = \Drupal::service("custom_api.entity_responses");
        try {
            $entity = $normalize->createUpdateEntity($entity_type, $id);  
            $entity = $normalize->convertJson($entity); 
            return $resp->prepareResponse($entity);
        } catch (\Exception $th) {
            return $resp->prepareError($th);
        }        
    }

    public function deleteEntity($entity_type, $id) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        $resp = \Drupal::service("custom_api.entity_responses");
        try {
            $entity = $normalize->deleteEntity($entity_type, $id);  
            return $resp->prepareResponse([
                'id' => $id,
                'entity_type' => $entity_type
            ]);
        } catch (\Exception $th) {
            return $resp->prepareError($th);
        }  
    }
}