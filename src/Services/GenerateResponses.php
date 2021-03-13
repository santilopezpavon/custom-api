<?php
namespace Drupal\custom_api\Services;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\JsonResponse;



class GenerateResponses {

    public function prepareResponse($data, $code = 200) {
        return new JsonResponse(["data" => $data], $code);
    }

    public function getQueryParameter($key, $default = FALSE) {
        if(array_key_exists($key, $_GET)) {
            return $_GET[$key];
        }
        return $default;
    }

    public function prepareError($exception, $code = 500) {
        $message = $exception->getMessage();
        $array_message = explode(":", $message);
        $message_final = trim($array_message[2]);
        if(empty($message_final)) {
            $message_final = $message;
        }
        return new JsonResponse(["message" => $message_final], $code);
    }

    public function getBodyParameters() {
        $content = \Drupal::request()->getContent();
        if(!empty($content)) {
             $decode = json_decode(\Drupal::request()->getContent(), true);

            return $decode;
        }
        return [];
    }

    public function getBodyParamater($name, $default = FALSE) {
        $params = $this->getBodyParameters();
        if(array_key_exists($name, $params)) {
            return $params[$name];
        } 
        return $default;
    }


}