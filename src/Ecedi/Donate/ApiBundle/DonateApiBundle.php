<?php

namespace Ecedi\Donate\ApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DonateApiBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSOAuthServerBundle';
    }
}
