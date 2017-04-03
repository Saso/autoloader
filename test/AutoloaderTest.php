<?php
declare(strict_types=1);

/**
 * Unit test for autoloader class.
 * It does not test the autoloader registered functionality in global codespace, it is part of integration tests.
 * ^ it means, we don't test autoloader, registered with spl_register_autoloader, but only as an object.
 *
 * @author Saso Filipovic <saso.filipovic@gmail.com>
 */

use PHPUnit\Framework\TestCase;
use Autoloader\Autoloader;
 
class AutoloaderTest extends TestCase {

    private $autoloader;
    private $filePath;
    
    function setUp() {
        $this->filePath = dirname(__DIR__) . '/source/Autoloader.php';
        $included_files = get_included_files();
        if ( !in_array( $this->filePath, $included_files ) ) { // include only if not included yet
            include( $this->filePath );
        }
    }

    function tearDown() {
    }

    function testFileExists() {
        $this->assertTrue(
            file_exists( $this->filePath ),
            'Autoloader class file doesn\'t exist.'
        );
    }

    function testAutoloaderExist() {
        $this->assertTrue(
            class_exists('Autoloader\Autoloader'), 
            "Autoloader class is not defined. Included file: '{$this->filePath}'." 
        );
        
        $autoloader = new Autoloader;

        $this->assertTrue( 
            $autoloader instanceof Autoloader,
           'Autoloader doesn\'t exist'
        );
    }

    function testMethodExists() {
        $autoloader = new Autoloader;
        
        $this->assertTrue( method_exists($autoloader, 'registerNamespace'),       'Method: Autoloader::registerNamespace is not defined.' );
        $this->assertTrue( method_exists($autoloader, 'defineClass'),             'Method: Autoloader::defineClass is not defined.' );
        // private..
        $this->assertTrue( method_exists($autoloader, 'init'),                    'Method: Autoloader::init is not defined.' );
        $this->assertTrue( method_exists($autoloader, 'parseClassName'),          'Method: Autoloader::parseClassName is not defined.' );
        $this->assertTrue( method_exists($autoloader, 'matchLongestRegNamespace'),'Method: Autoloader::matchLongestRegNamespace is not defined.' );
        $this->assertTrue( method_exists($autoloader, 'findClassFilepath'),       'Method: Autoloader::findClassFile is not defined.' );
    }

    /**
     * @expectedException \Exception
     */
    function testDoubleRegisterClassName() {
        $autoloader = new Autoloader;

        $autoloader->registerNamespace( '\fake3', '/some/path' );
        $autoloader->registerNamespace( '\fake3', '/path/to/non/unique/3' ); // duplicated value
    }

    /**
     * @expectedException \Exception
     */
    function testInit() {
        $autoloader = new Autoloader();

        $foo = new \Looking\Fo\r\My\MyClass();
    }
    
    /**
     * @expectedException \Exception
     */
    function testGoodRegBadClass() {
        $autoloader = new Autoloader();
        $autoloader->registerNamespace( '\Just', __DIR__.'/fixtures/' );
        $autoloader->registerNamespace( '\Base', __DIR__.'/fixtures/Base/' );

        $foo = new \Just\NS2\Fo\r\My\MyClass();
    }
    
    function testActualAutoloading() {
        $autoloader = new Autoloader();
        $autoloader->registerNamespace( '\Just', __DIR__.'/fixtures/' );
        $autoloader->registerNamespace( '\Base', __DIR__.'/fixtures/Base/' );
        
        try {
            $obj = new \Just\AnotherClass;
            $this->assertTrue( $obj instanceof \Just\AnotherClass );
        } catch( \Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @dataProvider goodClassesProvider
     */
    function testGoodLoading( $paths, $expClass  ) {
        $autoloader = new Autoloader();
        foreach( $paths as $ns => $path ) {
            $autoloader->registerNamespace( $ns, $path );
        }
        
        foreach ($expClass as $classPath => $className) {
            try {
                $obj = new $className;
                $this->assertTrue( is_a($obj, $className ));
                $this->assertEquals(
                    $obj->getPath(),
                    $classPath
                );
                $this->assertEquals(
                    $obj->getClass(),
                    $className
                );
            } catch( \Exception $e) {
                $this->fail($e->getMessage());
            }
        }
    }

    //////////////////////////////////////////////////////////////
    // Providers
    
    public function goodClassesProvider() {
        return array(
            array( #0
                array( // param1
                    '\Just' => __DIR__.'/fixtures',
                    '\Base' => __DIR__.'/fixtures/Base/',
                ),
                array( // param2
                    //katere classe naredimo in pricakujemo uspesno
                    __DIR__.'/fixtures/AnotherClass.php'    => 'Just\AnotherClass',
                ),
            ),
            array( #1
                array( // param1
                    '\Just' => __DIR__.'/fixtures',
                    '\Base' => __DIR__.'/fixtures/Base/',
                ),
                array( // param2
                    //katere classe naredimo in pricakujemo uspesno
                    __DIR__.'/fixtures/NS2/NS2Class.php'    => 'Just\NS2\NS2Class',
                ),
            ),
            array( #2
                array( // param1
                    '\Just' => __DIR__.'/fixtures/',
                    '\Base' => __DIR__.'/fixtures/Base/',
                ),
                array( // param2
                    //katere classe naredimo in pricakujemo uspesno
                    __DIR__.'/fixtures/AnotherClass.php'    => 'Just\AnotherClass',
                    __DIR__.'/fixtures/NS2/NS2Class.php'    => 'Just\NS2\NS2Class',
                ),
            ),
            array( #3
                array( // param1
                    '\Just' => __DIR__.'/fixtures/',
                    '\Base' => __DIR__.'/fixtures/Base/',
                    '\NS3'  => __DIR__.'/fixtures/NS3/',
                ),
                array( // param2
                    //katere classe naredimo in pricakujemo uspesno
                    __DIR__.'/fixtures/AnotherClass.php'    => 'Just\AnotherClass',
                    __DIR__.'/fixtures/NS2/NS2Class.php'    => 'Just\NS2\NS2Class',
                    __DIR__.'/fixtures/NS3/NS3Class.php'    => 'NS3\NS3Class',
                ),
            ),
        
        );
    }
    
}

