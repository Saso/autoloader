<?php
namespace Just\NS2;

use \Base\Base;

class NS2Class extends Base {
    public function getPath() {
        $path = __FILE__;
        return $path;
    }
    
    public function getClass() {
        $nsClass = __CLASS__;
        return $nsClass;
    }
    
}
