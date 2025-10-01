<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Document>
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function findDocumentsBy($activeInd = true, $partyId = null)
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d.id, d.name, f.type AS file_type, f.id AS file_id , f.name AS file_name, dt.name AS document_type_name, p.name AS party_name, pt.name AS party_type_name, d.createdAt, d.updatedAt, u.name AS user_name  ') // solo Document como raíz
            ->leftJoin(User::class, 'u', 'WITH', 'u.id = d.entityUserId')
            ->addSelect('u.name AS HIDDEN userName') // si necesitas el nombre del usuario para filtrar u ordenar
            ->join('d.documentType', 'dt')
            ->join('d.party', 'p')
            ->join('p.partyType', 'pt')
            ->join('d.file', 'f')
            ->andWhere('d.activeInd = :active')
            ->setParameter('active', $activeInd);


        if ($partyId !== null) {
            $qb->andWhere('d.party = :party')
                ->setParameter('party', $partyId);
        }

        return $qb->getQuery()->getResult();
    }


    //    /**
    //     * @return Document[] Returns an array of Document objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Document
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
