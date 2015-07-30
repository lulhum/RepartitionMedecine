<?php

namespace Lulhum\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LulhumUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    } 
}
