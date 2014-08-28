<?php

namespace Ecedi\Donate\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DonateAdminBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
