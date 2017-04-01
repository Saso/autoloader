## Simple to use psr-4 compatible autoloader for PHP classes.

## Instalation
Add this to your compose.json:
```
"require": {
    "Saso/autoloader": "dev-master"
},

"repositories": [
    {
        "type": "vcs",
        "url":  "git@github.com:Saso/autoloader.git"
    }
]
```
and install in command line:
```
composer install --no-autoloader
```

.. or, just clone this repo and check the code.

## Usage:

in bootstrap add lines:
```
$autoloader = new Autoloader();
$autoloader->registerNamespace( '\My\Namespace\And\Class', $Path );
```
Replace Namespace and Path with the actual ones. Register as many namespaces, as you want. 

Autoloader is safe for overlaping paths.
