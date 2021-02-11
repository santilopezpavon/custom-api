## Download
git clone https://github.com/snt1986/custom-api.git custom_api

## Get an Entity
POST to url:
* /api/{entity_type}/get/{id}
the body have the Schema of the data that do yo need return:
```json
{
	"schema": {
	  "title": []
  }
}
```


```json
{ 
  "schema": {
    "title": [],
     "field_image":["large"]
    }
}      
```
```json
{ 
  "schema": {
    "title": [],
    "field_image":["large"],
    "field_media": {
      "field_media_image": ["large", "medium"]
    }
  }
}      
```

## Create an Entity
* /api/{entity_type}/create
```json
{ 
  "schema": {
    "title": []  
  },
  "title": "Hola mundo",
  "type": "article"
}      
```

## Update an Entity
* /api/{entity_type}/update/{id}
```json
{ 
  "schema": {
    "title": []  
  },
  "title": "Hola mundo",
}      
```

## Delete an Entity
* /api/{entity_type}/delete/{id}

## Implement View
* /api/{view_id}/view/{display}
* Params GET: 
  * current_page [optional] the default value is 0
  * lang [optional]
* Body: 
  * Schema [optional]
  * Fiters [optional]
```json 
{
  "schema": {
  	"title": []
  	
  },
  "title": "ad"
}
         
``` 



         