# Hello Images

## Description

This is a WordPress plugin that adds an image proxy to resize and crop images inside the `wp-content` folder.

This is especially useful if you use WordPress as a headless CMS and allow the frontend to use the image sizes it needs
itself.

Let's say you have an image:  
`https://mywpsite.com/wp-content/uploads/2022/02/myimage.jpg`

Now you need a cropped and resized JPG you can simply add parameters to your Request:  
`https://mywpsite.com/wp-content/uploads/hello-images/size-400x400/2022/02/myimage.jpg`

`hello-images/` is needed for the image manipulation to work, which can be followed by other parameters. Like for example `size-400x400/`.


## How it works

As soon as you activate the plugin a new uploads folder `hello-images` will be added with a .htaccess file and a PHP Script. The .htaccess routes all not existing images to the index.php script, where the image will be generated.

After the first request, the result will be saved and next time that image is requested it will be returned directly.

## Parameters

* `{width}x{height}` => `size-[0-4000]x[0-4000]`
* `quality` => `quality-[1-100]`;
* `blur` => `blur-[radius: int]`;

### Sizes

The sizes are expected as {width}x{height} parameters. The values can be set from 0-4000.
Width, as well as height, can also be set to 0. In this case the aspect ration is retained and the image is only resized, not cropped.