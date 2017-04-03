<?php

namespace Base;

abstract class Base {

    public function getPath() {
        $path = __FILE__;
        return $path;
    }
    
    public function getClass() {
        $nsClass = __CLASS__;
        return $nsClass;
    }
}