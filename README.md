## Simple to use psr-4 compatible autoloader for PHP classes.

## Instalation
Add this to your compose.json:
```
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:Saso/autoloader.git"
        }
    ],
```
or, just clone this repo.

## Usage:

in bootstrap add lines:
```
        $autoloader = new Autoloader();
        $autoloader->registerNamespace( '\My\Namespace\And\Class', $Path );
```
Replace Namespace and Path with the actual ones. Register as many namespaces, as you want. 
Autoloader is safe for overlaping paths.
