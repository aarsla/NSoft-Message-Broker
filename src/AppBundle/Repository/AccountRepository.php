<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AccountRepository
 */
class AccountRepository extends EntityRepository
{
    public function getBalance() {
        return $this->findOneById(1);
    }
}
