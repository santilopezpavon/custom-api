<?php
namespace Drupal\custom_api\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\custom_api\Controller\ApiControllerBase;


class ApiControllerBaseQueries extends ApiControllerBase {
    
    public $schema = FALSE;

  /**
   * Responds to entity GET requests by id.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   * @param string $entity_type
   *   The entity type.
   * @param int|string $id
   *   The entity id.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response object.
   */
    public function getEntityIndex($entity_type, $id) {
        $schema = $this->entity_responses->getBodyParamater("schema", []);
        $apply = $this->entity_responses->getQueryParameter("theme");    
        $schema["deep"] = 0;
        try {
            $output = $this->entity_normalize->getEntity($entity_type, $id, $schema);
            if($apply !== FALSE) {
                $regions = $this->regionsprint->getAuxiliarContent($output, $apply);
                return $this->entity_responses->prepareResponse($regions);
            }           
            return $this->entity_responses->prepareResponse($output);
        } catch (\Exception $th) {
            return $this->entity_responses->prepareError($th);
        }        
    }

  /**
   * Responds to entity GET requests by alias.
   *
   * @param string $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response object.
   */
    public function getEntityByAlias($entity_type) {
        $apply = $this->entity_responses->getQueryParameter("theme");

        $schema = $this->entity_responses->getBodyParamater("schema", []);
        $alias = $this->entity_responses->getBodyParamater("alias");

        try {
            $output = $this->entity_normalize->getEntityByAlias($entity_type, $alias, $schema);
            if($apply !== FALSE) {
                $regions = $this->regionsprint->getAuxiliarContent($output, $apply);
                return $this->entity_responses->prepareResponse($regions);
            }    
            return $this->entity_responses->prepareResponse($output);
        } catch (\Exception $th) {
            return $this->entity_responses->prepareError($th);
        } 

    }

/**
   * Responds to menu GET requests by id.
   *
   * @param string $id
   *   The menu id.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response object.
   */
    public function getMenuById($id) {
        try {
            $tree = \Drupal::service("custom_api.menu_generator")->getMenuItems($id); 
            return $this->entity_responses->prepareResponse($tree);
        } catch (\Exception $th) {
            return $this->entity_responses->prepareError($th);
        } 
    }


/**
   * Responds to view by the view_id and display_id.
   *
   * @param string $view_id
   *   The view id.
   * @param string $display
   *   The view display id
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response object.
   */
    public function getViewIndex($view_id, $display) {
        $view_service = \Drupal::service("custom_api.view_control");
        $schema = $this->entity_responses->getBodyParamater("schema", []);
        try {
            $view = $view_service->getView($view_id, $display, $schema); 
            return $this->entity_responses->prepareResponse($view);
        } catch (\Exception $th) {
            return $this->entity_responses->prepareError($th);
        }       
    } 

  
}