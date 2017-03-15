<?php
declare(strict_types=1);

namespace \Autoloader;

/**
 * Autoloader is avtomatically invoked for any undefined class and includes its class file, to define the class.
 * Later, it also check, if class was sucessfully defined.
 * Namespaces should always be in the form: \this\is\namespace\class
 * Paths must always be absolute paths in form /this/is/path (for linux)
 *
 * @author Saso Filipovic <saso.filipovic@gmail.com>
 */
class Autoloader {
    
    /**
     *
     * @var type
     */
    private $classPaths = array();


    /**
     * Registers a namespace with a path.
     * @param string $namespace Namespace to be registered
     * @param string $path Absolute filesystem path
     * @throws \Exception
     */
    public function registerNamespace( string $namespace, string $path='' ) {
        if ( !isset( $this->classPaths[ $namespace ] ) ) {
            $this->classPaths[ $namespace ] = $path; // clean values registered
        } else {
            throw new \Exception( "Namespace '{$namespace}' with value '{$namespace}' can't be registered, as it's already registered with value'{$this->classPaths[$namespace]}'." );
        }
    }

    /**
     * Register autoloader's method with SPL hook/trigger
     * @uses spl_autoload_register()
     * @throws \Exception
     */
    public function init() {
        spl_autoload_register( array( $this, 'defineClass' ), true );
    }

    /**
     * Registered method, to be called from spl trigger, which finds and includes proper class file and thus defines selected class.
     * Works well with interfaces and traits. [does it?]
     *
     * @param string $className namespace+class, which needs to be defined (include/require).
     * @throws \Exception
     */
    public function defineClass( $namespaceClassName ) {
        $filePath = $this->findClassFilepath( $namespaceClassName );

        if( file_exists($filePath) ) {
            include( $filePath );
            if (!class_exists($namespaceClassName, false)) {
                throw new \Exception("File '{$filePath}' was included, but class '{$namespaceClassName}' is still not defined.");
            }
        } else {
            throw new \Exception( "File '{$filePath}' for requested class '{$namespaceClassName}' doesn't exist." );
        }
    }

    //////////////////////////////////////////////////////////
    // private functions below
    //
    
	/**
	 * Method maps the \namespace\class to the class definition file, to be able to be instantiated.
	 * @param string $className Name of the class, to be defined
	 * @return mixed (absolute path to the file) or throws an Exception
	 */
    private function findClassFilepath( $className ) {
        list( $namespace, $class) = $this->parseClassName($className);
        list( $matchedNS, $restOfNS ) = $this->matchLongestRegNamespace( $namespace );

        $matchedNS = '\\' . implode( '\\', $matchedNS );
        $restPath  = (count($restOfNS)>0) ? '/' : '';
        $restPath .= implode( '/', $restOfNS );

        $classFilePath  = $this->classPaths[ $matchedNS ];  // registered namespace part
        $classFilePath .= $restPath;                        // subPath part from the rest of the namespace
        $classFilePath .= '/' . $class . '.php';            // actual class name

        return $classFilePath;
    }
    /**
     * Compare given namespace with registered ones and finds a match with the longest registered one.
     * @param type $namespace
     * @return array with 2 elements, both arrays of namespace elements
     * @throws \Exception
     */
    private function matchLongestRegNamespace( $namespace ) {
        $origNamespace = $namespace;
        $namespace = explode( '\\', ltrim($namespace,'\\') );
        $tmpLength = count( $namespace );

        for ( $i=$tmpLength; $i>=0; $i-- ) {
            $slice = array_slice( $namespace, 0, $i, true );    // creates 'parent' version of namespace, in array
            $tmpNS = '\\' . implode( '\\', $slice );            // makes it nice string
            if (array_key_exists($tmpNS, $this->classPaths) ) { // match?
                $restOfNS = array_slice( $namespace, $i, $tmpLength-$i, true);
                return array( $slice, $restOfNS );
            }
        }

        // none is found
        throw new \Exception( "Namespace '{$origNamespace}' doesn't exist among registered namespaces." );
    }
    /**
     * Splits full namespace\class to two elements.
     * @param string $namespaceClassName namespace + classname, to be parsed.
     * @return array Array of namespace and classname values.
     */
    private function parseClassName( $namespaceClassName ) {
        $position	= strrpos( $namespaceClassName, '\\' );         //Find the position of the last occurrence of \
        $namespace	= substr( $namespaceClassName, 0, $position );  //Return part of a string that define the path
        $class		= substr( $namespaceClassName, $position+1 );

        return array(
            $namespace,
            $class,
        );
    }
    
}
