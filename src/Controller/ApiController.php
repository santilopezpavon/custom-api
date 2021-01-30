<?php
namespace Drupal\custom_api\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;



class ApiController extends ControllerBase {

    public function getEntityIndex($entity_type, $id) {
        $output = \Drupal::service("custom_api.entity_normalize")->getEntity($entity_type, $id);
        return new JsonResponse($output, 200);
    }
}