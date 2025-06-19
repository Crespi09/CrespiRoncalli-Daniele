<?php

namespace App\Repository;

use App\Entity\Race;
use App\Entity\User;
use App\Entity\UserRace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserRace>
 */
class UserRaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRace::class);
    }

    //    /**
    //     * @return UserRace[] Returns an array of UserRace objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserRace
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    public function create(string $name, string $size, float $total, int $km, Race $race, User $user): ?UserRace
    {

        $userRace = new UserRace();

        $userRace->setName($name);
        $userRace->setSize($size);
        $userRace->setTotal($total);
        $userRace->setKm($km);
        
        $userRace->setUser($user);
        $userRace->setRace($race);

        return $userRace;
    }


    public function update(
        UserRace $userRace,
        ?string $name = null,
        ?string $size = null,
        ?float $total = null,
        ?int $km = null,
        ?Race $race = null
    ): UserRace {
        if ($name !== null) {
            $userRace->setName($name);
        }
    
        if ($size !== null) {
            $userRace->setSize($size);
        }
    
        if ($total !== null) {
            $userRace->setTotal($total);
        }
    
        if ($km !== null) {
            $userRace->setKm($km);
        }
    
        if ($race !== null) {
            $userRace->setRace($race);
        }
    
        return $userRace;
    }

}
