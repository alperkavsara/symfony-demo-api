<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Shopping\ApiFilterBundle\Repository\Rfc14RepositoryInterface;
use Shopping\ApiFilterBundle\Service\Rfc14Service;

class UserRepository extends EntityRepository implements Rfc14RepositoryInterface
{
    /**
     * @param Rfc14Service $rfc14Service
     * @return User[]
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Shopping\ApiFilterBundle\Exception\PaginationException
     */
    public function findByRfc14(Rfc14Service $rfc14Service): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->leftJoin('u.addresses', 'a');

        $rfc14Service->applyToQueryBuilder($qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function persist(User $user): void
    {
        if ($user->getPlainPassword() !== null) {
            $user->setPasswordSalt(uniqid())
                ->setPasswordHash(md5($user->getPlainPassword() . $user->getPasswordSalt()));
        }

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush($user);
    }
}