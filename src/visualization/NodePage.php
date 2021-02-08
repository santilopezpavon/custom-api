<?php
namespace Drupal\custom_api\visualization;


class NodePage {

    public $teaser = [
        'title'
    ];

    public static function existo() {
        return [
            "NodeArticle" => [
                "title" => [],
                'field_image' => [],
                "field_media" => [],
                "MediaImage" => [
                    "field_media_image" => []
                ]           
            ],
            
        ];
    }
}