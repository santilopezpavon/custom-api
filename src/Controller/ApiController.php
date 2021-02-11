<?php
namespace Drupal\custom_api\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;



class ApiController extends ControllerBase {

    public function getEntityIndex($entity_type, $id) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        $resp = \Drupal::service("custom_api.entity_responses");

        $schema = $resp->getBodyParamater("schema", []);

        try {
            $output = $normalize->getEntity($entity_type, $id, $schema);
            return $resp->prepareResponse($output);
        } catch (\Exception $th) {
            return $resp->prepareError($th);
        }        
    }

    public function getEntityByAlias($entity_type) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        $resp = \Drupal::service("custom_api.entity_responses");

        $schema = $resp->getBodyParamater("schema", []);
        $alias = $resp->getBodyParamater("alias");

        try {
            $output = $normalize->getEntityByAlias($entity_type, $alias, $schema);
            return $resp->prepareResponse($output);
        } catch (\Exception $th) {
            return $resp->prepareError($th);
        } 

    }

    public function getViewIndex($view_id, $display) {
        $resp = \Drupal::service("custom_api.entity_responses");
        $view_service = \Drupal::service("custom_api.view_control");

        $schema = $resp->getBodyParamater("schema", []);
        
        try {
            $view = $view_service->getView($view_id, $display, $schema); 
            return $resp->prepareResponse($view);
        } catch (\Exception $th) {
            return $resp->prepareError($th);
        } 
      
    }

    public function createEntity($entity_type) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        $resp = \Drupal::service("custom_api.entity_responses");
        
        $schema = $resp->getBodyParamater("schema", []);

        try {
            $entity = $normalize->createUpdateEntity($entity_type, NULL, $schema);  
            //$entity = $normalize->convertJson($entity); 
            return $resp->prepareResponse($entity);
        } catch (\Exception $th) {
            return $resp->prepareError($th);
        }        
    }

    public function updateEntity($entity_type, $id) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        $resp = \Drupal::service("custom_api.entity_responses");

        $schema = $resp->getBodyParamater("schema", []);

        try {
            $entity = $normalize->createUpdateEntity($entity_type, $id, $schema);  
            //$entity = $normalize->convertJson($entity); 
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