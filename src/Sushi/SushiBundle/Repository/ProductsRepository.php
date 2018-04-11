<?php
// src/Sushi/SushiBundle/Repository/ProductsRepository.php
namespace Sushi\SushiBundle\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * ProductsRepository
 *
*/
class ProductsRepository extends EntityRepository
{
    public function findAllProductsByGroupId($groupId)
    {
        return $this->getEntityManager()-> createQuery(
              'SELECT p FROM SushiSushiBundle:Products p WHERE p.price >= 100 AND p.groupId = :groupId ORDER BY p.orderProduct ASC'
            )->setParameter('groupId', 100)->getResult();
            //)->setParameter('groupId', $groupId)->getResult();
    }
}