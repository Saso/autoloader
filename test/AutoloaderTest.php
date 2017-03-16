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
 
class AutoloaderCase extends TestCase {

    /**
     * @var \Autoloader\Autoloader
     */
    private $autoloader;

    private $filePath;

    public static function setUpBeforeClass() { // class constructor
        //
    }

    public static function tearDownAfterClass() { // class destructor
        //
    }
    
    function setUp() {
        $this->filePath = dirname(__DIR__) . '/Autoloader.php';
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
            'Autoloader class is not defined. Included file: '.$this->filePath 
        );
        
        $autoloader = new Autoloader;

        $this->assertTrue( 
            $autoloader instanceof Autoloader,
           'Autoloader doesn\'t exist' );
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

        $foo = new \Looking\Fo\r\MyClass();
    }
    
    // autoloader interferes with phpunit autoloading, making a mess.
    // maybe I'll make this test some tieme later.
    /*
    function testActualAutoloading() {
        $autoloader = new Autoloader();
        $autoloader->registerNamespace( 'Just', __DIR__.'/fixtures' );
        
        try {
            $fo2 = new \Just\AnotherClass;
        } catch( \Exception $e) {
            $this->fail($e->message());
        }
    }
    */
}

