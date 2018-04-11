<?php

namespace Sushi\SushiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProductsRepository extends EntityRepository
{
    public function findByGroupId($groupId)
    {
        return $this->getEntityManager()-> createQuery(
                //'SELECT p FROM SushiSushiBundle:Products p WHERE p.groupId = '+$groupId+' ORDER BY p.orderProduct ASC'
            //)->getResult();
              'SELECT p FROM SushiSushiBundle:Products p WHERE p.groupId = :groupId ORDER BY p.orderProduct ASC'
            )->setParameter('groupId', $groupId)->getResult();
    }
}