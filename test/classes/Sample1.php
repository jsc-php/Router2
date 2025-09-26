<?php

namespace classes;

use JscPhp\Router2\CRoute;
use JscPhp\Router2\MRoute;

#[CRoute]
class Sample1
{
    #[MRoute('/sample/view/:id|d/:test', 'get')]
    public function view($id, $test)
    {
        var_dump($id, $test);
    }

    #[MRoute('/', 'get')]
    public function home_test()
    {
        echo "Hello World\n";
    }
}