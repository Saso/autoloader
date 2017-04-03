<?php
namespace NS3; // separate NS

use \Base\Base;

class NS3Class {
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
