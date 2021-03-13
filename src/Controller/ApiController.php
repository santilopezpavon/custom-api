<?php
namespace Drupal\custom_api\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;


class ApiController extends ControllerBase {
    
    public $schema = FALSE;

    public function getEntityIndex($entity_type, $id) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        $resp = \Drupal::service("custom_api.entity_responses");
        $schema = $resp->getBodyParamater("schema", []);
        $apply = $resp->getQueryParameter("theme");
        
       

        try {
            $output = $normalize->getEntity($entity_type, $id, $schema);
             
            if($apply !== FALSE) {
                $regions = \Drupal::service("custom_api.regionsprint")->getAuxiliarContent($output, $apply);
                return $resp->prepareResponse($regions);
            }            
            
            return $resp->prepareResponse($output);
        } catch (\Exception $th) {
            return $resp->prepareError($th);
        }        
    }

    public function getEntityByAlias($entity_type) {
        $normalize = \Drupal::service("custom_api.entity_normalize");
        $resp = \Drupal::service("custom_api.entity_responses");
        $apply = $resp->getQueryParameter("theme");

        $schema = $resp->getBodyParamater("schema", []);
        $alias = $resp->getBodyParamater("alias");

        try {
            $output = $normalize->getEntityByAlias($entity_type, $alias, $schema);
            if($apply !== FALSE) {
                $regions = \Drupal::service("custom_api.regionsprint")->getAuxiliarContent($output, $apply);
                return $resp->prepareResponse($regions);
            }    
            return $resp->prepareResponse($output);
        } catch (\Exception $th) {
            return $resp->prepareError($th);
        } 

    }

    public function getMenuById($id) {
        $resp = \Drupal::service("custom_api.entity_responses");        
        try {
            $tree = \Drupal::service("custom_api.menu_generator")->getMenuItems($id); 
            return $resp->prepareResponse($tree);
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

    public function multipleQuery() {
        $resp = \Drupal::service("custom_api.entity_responses");
        $multiQuery = $resp->getBodyParameters();
        $client = \Drupal::httpClient();            

        try {
            $response = [];
            foreach ($multiQuery as $key => $value) {
                $url = \Drupal\Core\Url::fromRoute($value["route"], $value["params"], [
                    'absolute' => TRUE,
                    "query" => $value["query"]
                ]);
                $path = $url->toString();
                $request = $client->post($path, [
                    'json' => $value["body"]
                ]);
                $response[$key] = json_decode($request->getBody());
            }
            return $resp->prepareResponse($response);
        } catch (\Exception $th) {
            return $resp->prepareError($th);
        } 
        
    }



    public function test(){
       /*  $master = \Drupal::requestStack()->getCurrentRequest();
        $path = $master->get('path', '/');

        $cache = new \Drupal\Core\Cache\CacheableMetadata();
        // Cache a different version based on the Query Args.
        $cache->addCacheContexts(['url.query_args:path']);
        // Add the block list as a cache tag.
        $cache->addCacheTags(\Drupal::entityTypeManager()->getDefinition('block')->getListCacheTags());

        kint($path); */
        $data = [
            "node_type" => 'article'
        ];
        
        \Drupal::service("custom_api.regionsprint");



        $block_layout = [];
        // $theme = \Drupal::theme()->getActiveTheme();
        //kint($theme->info->regions);
        $theme = \Drupal::service("theme_handler")->getTheme("bartik");
        kint($theme->info);
        $regions = $theme->info["regions"];
        //kint($theme);
       // $regions = $theme->getRegions();
        foreach ($regions as $region) {  
            $blocks = \Drupal::entityTypeManager()
              ->getStorage('block')
              ->loadByProperties(['theme' => $theme->getname(), 'region' => $region]);
            foreach ($blocks as $key => $block) {
              //  kint($block);
             //   kint($block->getVisibilityConditions());
                kint($block->getEntityTypeId());
            }
            $block_layout[$region] = $blocks;
        }
        kint($block_layout); 
         
       /* $menu_tree = \Drupal::menuTree();
        // Build the typical default set of menu tree parameters.
        $parameters = $menu_tree->getCurrentRouteMenuTreeParameters("main");
        // Load the tree based on this set of parameters.
        $tree = $menu_tree->load("main", $parameters);*/

        /*$tree = \Drupal::service("custom_api.menu_generator")->getMenuItems("main");
        kint($tree);
        kint("hola");*/

        
    }
}