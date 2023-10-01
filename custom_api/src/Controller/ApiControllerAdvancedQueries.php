<?php

namespace Drupal\custom_api\Controller;

use Drupal\custom_api\Controller\ApiControllerBase;


class ApiControllerAdvancedQueries extends ApiControllerBase {

    public function multipleQuery() {
        $multiQuery = $this->entity_responses->getBodyParameters();
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
            return $this->entity_responses->prepareResponse($response);
        } catch (\Exception $th) {
            return $this->entity_responses->prepareError($th);
        } 
        
    }



    public function test(){    

        
    }
}
