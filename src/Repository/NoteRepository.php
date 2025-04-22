<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    //    /**
    //     * @return Note[] Returns an array of Note objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Note
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    /**
     * Récupère les notes filtrées selon les critères fournis
     *
     * @param string|null $matricule
     * @param string|null $nom
     * @param string|null $annee
     * @return Note[]
     */
    

    public function filtrerNote(?string $matricule, ?string $nom, ?string $annee): array
    {
        $qb = $this->createQueryBuilder('n')
            ->join('n.student', 's') // adapte le nom de la relation si nécessaire
            ->addSelect('s');

        if (!empty($matricule)) {
            $qb->andWhere('s.matricule LIKE :matricule')
            ->setParameter('matricule', '%' . $matricule . '%');
        }

        if (!empty($nom)) {
            $qb->andWhere('s.nom LIKE :nom')
            ->setParameter('nom', '%' . $nom . '%');
        }

        if ($annee) {
            $debut = new \DateTime("$annee-01-01 00:00:00");
            $fin = new \DateTime(($annee + 1) . "-01-01 00:00:00");
        
            $qb->andWhere('n.created_at >= :debut')
               ->andWhere('n.created_at < :fin')
               ->setParameter('debut', $debut)
               ->setParameter('fin', $fin);
        }
        

        return $qb->getQuery()->getResult();
    }



}
