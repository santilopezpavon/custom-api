<?php

namespace Drupal\custom_api\Services;

use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Defines a class to build the Api Responses. 
 */
class GenerateResponses {

    /**
     * Function for building the Response.
     * 
     * @param array $data
     *      The Array object to return in Api Response.     * 
     * @param integer $code
     *      The Code value of the Response.
     * 
     * @return json 
     *      The Json Response formated with the Code value.
     */
    public function prepareResponse($data, $code = 200) {
        return new JsonResponse(["data" => $data], $code);
    }

    /**
     * Function for get a Query parameter.
     * 
     * @param string $name_parameter
     *      The name of the parameter. 
     * @param any $default
     *      The value resonse if the value not exists in the Query.
     * 
     * @return any 
     *      The value of the Query parameter or if not existe the default value.
     */
    public function getQueryParameter($name_parameter, $default = FALSE) {
        if(array_key_exists($name_parameter, $_GET)) {
            return $_GET[$name_parameter];
        }
        return $default;
    }

    /**
     * Function for extract the human text error.
     * 
     * @param string $exception
     *      The exception of the error, is the Catch Exception. 
     * @param any $code
     *      The code of the Json Response.
     * 
     * @return json 
     *      A Json Response with a Human message Exception.
     */
    public function prepareError($exception, $code = 500) {
        $message = $exception->getMessage();
        $array_message = explode(":", $message);
        $message_final = trim($array_message[2]);
        if(empty($message_final)) {
            $message_final = $message;
        }
        return new JsonResponse(["message" => $message_final], $code);
    }

    /**
     * Function for extract all body parameters of the Request.     
     * 
     * @return array 
     *      An array with all body parameters.
     */
    public function getBodyParameters() {
        $content = \Drupal::request()->getContent();
        if(!empty($content)) {
             $decode = json_decode(\Drupal::request()->getContent(), true);

            return $decode;
        }
        return [];
    }

    /**
     * Function for extract a body parameter of the Request.     
     *
     * @param string $name
     *      The name of the body paramater. 
     * @param any $default
     *      The default value if the param not exists.
     * 
     * @return any 
     *      The body parameter.
     */
    public function getBodyParamater($name, $default = FALSE) {
        $params = $this->getBodyParameters();
        if(array_key_exists($name, $params)) {
            return $params[$name];
        } 
        return $default;
    }


}