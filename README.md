# Hello Images

## Description

This is a WordPress plugin that adds an image proxy to resize and crop images inside the `wp-content` folder.

This is especially useful if you use WordPress as a headless CMS and allow the frontend to use the image sizes it needs
itself.

Let's say you have an image:  
`https://mydomain.com/wp-content/uploads/2022/02/myimage.jpg`

Now you need a cropped and resized PNG you can simply add parameters to your Request:  
`https://mydomain.com/wp-content/uploads/hello-images/size-400x400/2022/02/myimage.jpg`

## How it works

As soon as you activate the plugin an htaccess rule will be added. This allows the plugin to pass the image to a php
file that does all the resizing, cropping, etc.  
And of course all images are cached after the first request.

## Parameters

* `widthxheight` => `size-[0-3000]x[0-3000]`
* `quality` => `quality-[1-100]`;
* `blur` => `blur-[radius: int]`;

### Sizes

Die grössen werden als {width}x{height} parameter erwartet. Die Werte können von 0-maxWidth/maxHeight gesetzt werden.
Die maximale Breite und Höhe ist standardmässig 3000, kann aber über einen Filter angepasst werden:

```php
add_filter( 'SayHello\HelloImages\MaxWidth', function($maxWidth) {
    return 5000;
});

add_filter( 'SayHello\HelloImages\MaxHeight', function($maxHeight) {
    return 5000;
});
```

Width, as well as height, can also be set to 0. In this case the aspect ration is retained and the image is only resized, not cropped.