<?php

namespace classes;

use JscPhp\Router2\CRoute;
use JscPhp\Router2\MRoute;

#[CRoute]
class Sample1
{
    #[MRoute('/sample/view/:id|d/:test', 'get')]
    public function view()
    {

    }

    #[MRoute('/', 'get')]
    public function home_test()
    {

    }
}