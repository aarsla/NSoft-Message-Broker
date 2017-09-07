<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MessageRepository
 */
class MessageRepository extends EntityRepository
{
    public function queryMessages() {
        $query = $this->createQueryBuilder('m');
        $query->orderBy('m.createdAt', 'ASC');

        return $query;
    }
}
