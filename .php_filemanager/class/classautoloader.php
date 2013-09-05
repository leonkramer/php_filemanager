<?php

class classAutoLoader {
    public function __construct() {
        spl_autoload_register(array($this, 'loader'));
    }
    private function loader($className) {
        if (!include $className . '.php') {
            echo "failed to initialize class " . $className;
        }

    }
}

?>
