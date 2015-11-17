Multiple thumbnails
===================

Allows multiple featured images, specified per post type.

## Installation
If you're using Composer to manage WordPress, add this plugin to your project's dependencies. Run:
```sh
composer require trendwerk/multiple-thumbnails 1.0.3
```

Or manually add it to your `composer.json`:
```json
"require": {
	"trendwerk/multiple-thumbnails": "1.0.3"
},
```

## Usage

### Step 1
Add the post type support 'thumbnails' to any post type

```php
'supports' => array( 'title', 'editor', 'thumbnails', 'revisions' )
```

### Step 2
Specify the thumbnails you want to use

```php
'supports'             => array( 'title', 'editor', 'thumbnails', 'revisions' ),
'thumbnails'           => array(
	'main-image'       => array(
		'label'        => __( 'Main image', 'tp' ),
	),
	'additional-image' => array(
		'label'        => __( 'Additional image', 'tp' ),
	),
),
```

### Step 3
Display the thumbnails with one of the following functions. These are near equivalents of the WordPress functions, only with the thumbnail name added to it. For example:
	
```php
if( tp_has_post_thumbnail( get_the_ID(), 'main-image' ) )
	tp_the_post_thumbnail( 'main-image', 'large' );
```

**Display thumbnail**

```php	
tp_the_post_thumbnail( $thumbnail, $size = 'thumbnail', $attr = '' )
```

**Check if there is a thumbnail set**
	
```php
tp_has_post_thumbnail( $post_id, $thumbnail )
```

**Retrieve thumbnail**
	
```php
tp_get_the_post_thumbnail( $post_id, $thumbnail, $size = 'thumbnail', $attr = '' )
```

**Get the attachment ID of a thumbnail**

```php
tp_get_post_thumbnail_id( $post_id, $thumbnail )
```
