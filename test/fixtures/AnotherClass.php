<?php
namespace Just;

use \Base\Base;

class AnotherClass  extends Base {
    public function getPath() {
        $path = __FILE__;
        //echo "\nPath: '{$path}'\n\n";
        return $path;
    }
    
    public function getClass() {
        $nsClass = __CLASS__;
        //echo "\nNS+Class: '{$nsClass}'\n\n";
        return $nsClass;
    }
    
}
