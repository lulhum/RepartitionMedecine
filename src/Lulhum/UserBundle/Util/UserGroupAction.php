<?php
// src/Lulhum/UserBundle/Util/UserGroupAction.php

namespace Lulhum\UserBundle\Util;

use Lulhum\RepartitionMedecineBundle\Util\GroupAction;

class UserGroupAction extends GroupAction
{
    protected $actions = array(
        'changePromotion' => 'Modifier la promotion',
     );

    protected $entity = 'LulhumUserBundle:User';

}