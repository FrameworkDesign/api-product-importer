# Api Product Importer

> Api Product Importer is a Statamic addon that does something pretty neat.

### Install locally
If installing locally then create a local folder to put this in e.g. **addons**, then **weareframework** so full folder path is **addons/weareframework**
clone this repo in and then update your composer file to make statamic aware of a local file like so:

```
    ...
 
    "require": {
        ...,
        "weareframework/api-product-importer": "*"
    },
 
    ...
    "repositories": [
        {
            "type": "path",
            "url": "addons/weareframework/api-product-importer"
        }
    ]
```    

the docs at statamic refer to how to do this also: https://statamic.dev/extending/addons#private-addons
