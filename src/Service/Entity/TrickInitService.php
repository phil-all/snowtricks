<?php

namespace App\Service\Entity;

use App\Entity\User;
use App\Entity\Trick;
use DateTimeImmutable;
use App\Service\Entity\TypeInitService;

class TrickInitService
{
    public function setNew(User $user): Trick
    {
        /** @var DateTimeImmutable */
        $now = new DateTimeImmutable('now');

        return (new trick())
            ->setUser($user)
            ->setCreatedAt($now)
            ->setUpdateAt($now);
    }
}
