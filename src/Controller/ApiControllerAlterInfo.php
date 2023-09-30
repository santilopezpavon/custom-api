<?php
namespace Drupal\custom_api\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\custom_api\Controller\ApiControllerBase;


class ApiControllerAlterInfo extends ApiControllerBase {
    
    public $schema = FALSE;    
    public function createEntity($entity_type) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        
        $schema = $this->entity_responses->getBodyParamater("schema", []);

        try {
            $entity = $normalize->createUpdateEntity($entity_type, NULL, $schema);  
            //$entity = $normalize->convertJson($entity); 
            return $this->entity_responses->prepareResponse($entity);
        } catch (\Exception $th) {
            return $this->entity_responses->prepareError($th);
        }        
    }
    public function updateEntity($entity_type, $id) {
        $normalize = \Drupal::service("custom_api.entity_normalize");

        $schema = $this->entity_responses->getBodyParamater("schema", []);

        try {
            $entity = $normalize->createUpdateEntity($entity_type, $id, $schema);  
            //$entity = $normalize->convertJson($entity); 
            return $this->entity_responses->prepareResponse($entity);
        } catch (\Exception $th) {
            return $this->entity_responses->prepareError($th);
        }        
    }

    public function deleteEntity($entity_type, $id) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        try {
            $entity = $normalize->deleteEntity($entity_type, $id);  
            return $this->entity_responses->prepareResponse([
                'id' => $id,
                'entity_type' => $entity_type
            ]);
        } catch (\Exception $th) {
            return $this->entity_responses->prepareError($th);
        }  
    }

   
   
}