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



         