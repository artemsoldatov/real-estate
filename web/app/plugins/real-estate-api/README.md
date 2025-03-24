# Real Estate API Plugin

> This plugin exposes a REST API for managing real estate properties (custom post type `realestate`)

## Base URL

```
/wp-json/realestate/v1/
```

---

## Endpoints

### GET `/objects`

Fetches a list of real estate objects

**Optional filters:**

- `district` — Filter by taxonomy slug, ID, name
- `ecology` — Filter by ACF field `environmental_friendliness`
- `floors` — Filter by ACF field `number_of_floors`
- `type` — Filter by ACF field `building_type`

**Example:**

```
GET /wp-json/realestate/v1/objects?district=подільський&ecology=3&floors=5&type=цегла
```

---

### GET `/objects/{id}`

Fetch a single object by ID

**Example:**

```
GET /wp-json/realestate/v1/objects/398
```

---

### POST `/objects`

Create a new object.

**Example:**

```
POST /wp-json/realestate/v1/objects
```

**Body (JSON):**

```json
{
  "title": "Будинок 123",
  "house_name": "ЖК Харків",
  "location_coordinates": "40.45, 30.52",
  "number_of_floors": "9",
  "building_type": "цегла",
  "environmental_friendliness": "4",
  "image": 126,
  "district": [
    "Подільський"
  ],
  "premises": [
    {
      "area": 45,
      "number_of_rooms": "2",
      "balcony": "так",
      "bathroom": "так",
      "image": 128
    },
    {
      "area": 35,
      "number_of_rooms": "1",
      "balcony": "ні",
      "bathroom": "так",
      "image": 127
    }
  ]
}

```

---

### PUT `/objects/{id}`

Update an existing object

**Example:**

```
PUT /wp-json/realestate/v1/objects/409
```

**Body (JSON):**

```json
{
  "title": "Updated Будинок 123",
  "house_name": "ЖК Київ",
  "location_coordinates": "40.45, 30.52",
  "number_of_floors": "9",
  "building_type": "цегла",
  "environmental_friendliness": "4",
  "image": 126,
  "district": [
    "Київський"
  ],
  "premises": [
    {
      "area": 45,
      "number_of_rooms": "2",
      "balcony": "так",
      "bathroom": "так",
      "image": 128
    }
  ]
}
```

---

### DELETE `/objects/{id}`

Delete an object by ID

**Example:**

```
DELETE /wp-json/realestate/v1/objects/123
```

---

## API XML Endpoints

### POST `/import-xml`

Сreate objects via XML

**Example:**

``` 
POST /wp-json/realestate/v1/import-xml
```

**Body (XML):**

```xml
<objects>
	<object>
		<title>Новобуд Київський</title>
		<house_name>Багатоповерхівка</house_name>
		<district>Київський</district>
		<location_coordinates>50.1234, 30.1234</location_coordinates>
		<number_of_floors>5</number_of_floors>
		<building_type>цегла</building_type>
		<environmental_friendliness>4</environmental_friendliness>
		<image>https://dummyimage.com/600x400/ccc/000.png&amp;text=House+123</image>
		<premises>
			<room>
				<area>50</area>
				<number_of_rooms>2</number_of_rooms>
				<balcony>так</balcony>
				<bathroom>ні</bathroom>
				<image>https://dummyimage.com/300x200/ddd/000.png&amp;text=Room+123</image>
			</room>
			<room>
				<area>35</area>
				<number_of_rooms>1</number_of_rooms>
				<balcony>ні</balcony>
				<bathroom>так</bathroom>
				<image>https://dummyimage.com/300x200/ddd/000.png&amp;text=Room+321</image>
			</room>
		</premises>
	</object>
</objects>
```

---

## Authentication

All endpoints are currently public (`permission_callback` is `__return_true`).

---

## Requirements

- WordPress
- REST API support
- ACF plugin enabled
- The `realestate` custom post type must be registered
