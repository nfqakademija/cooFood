<?php

namespace cooFood\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class cooFoodUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
