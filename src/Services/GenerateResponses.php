<?php
namespace Drupal\custom_api\Services;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\JsonResponse;



class GenerateResponses {
    public function prepareResponse($data, $code = 200) {
        return new JsonResponse(["data" => $data], $code);
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
}