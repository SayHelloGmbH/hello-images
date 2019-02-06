# Hello Images

## Description
This is a WordPress plugin that adds an image proxy to resize and crop images inside the `wp-content` folder.

And it's super easy!

Let's say you have an image: `https://mydomain.com/wp-content/uploads/myimage.jpg`.  
Now you need a croped and resized PNG you can simply add parameters to the image:  
`https://mydomain.com/wp-content/uploads/myimage.jpg?width=200&height=300&format=png`

## How it works
As soon as you activate the plugin an htaccess rule will be added. This allows the plugin to pass the image to a php file that does all the resizing, cropping, etc.  
And of course all images are cached after the first request.

## Parameters
* `width` => int, (1 - MaxWidth)
* `height` => int, (1 - MaxHeight)
* `format` => string, (`jpg`, `png` or `gif`)
* `quality` => int, (1 - 100)