<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\ResearchFilter\ResearchFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Sortie::class);
        $this->security = $security;
    }

    public function findByPersonnalResearch(ResearchFilter $research)
    {
        // on récupère l'id de l'utilisateur connecté :
        $userId = $this->security->getUser()->getId();
        // s = sortie
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->leftJoin('s.participants', 'participant')
            ->addSelect('participant');

        if ($research->getSite()) {
            $queryBuilder->andWhere('s.site >= ' . $research->getSite()->getId());
        }
        if ($research->getNomSortie()) {
            $queryBuilder->andWhere("s.nom LIKE '%" . $research->getNomSortie() . "%'");
        }
        if ($research->getDateMin()) {
            $queryBuilder->andWhere("s.dateHeureDebut >= '" . $research->getDateMin()->format('Y-m-d H:i:s') . "'");
        }
        if ($research->getDateMax()) {
            $queryBuilder->andWhere("s.dateHeureDebut <= '" . $research->getDateMax()->format('Y-m-d H:i:s') . "'");
        }
        if (in_array('organisateur', $research->getSpecificitees())) {
            $queryBuilder->andWhere('s.organisateur = ' . $userId);
        }
        if (in_array('inscrit', $research->getSpecificitees())) {
            $queryBuilder->andWhere('participant.id = ' . $userId);
        }
        if (in_array('noninscrit', $research->getSpecificitees())) {
            $queryBuilder->andWhere('participant.id != ' . $userId);
        }
        if (in_array('sortiespassees', $research->getSpecificitees())) {
            $now = new \DateTime();
            $queryBuilder->andWhere("s.dateHeureDebut <= '" . $now->format('Y-m-d H:i:s') . "'");
        }
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function findByIdParticipant($id)
    {
        // s = sortie
        $queryBuilder = $this->createQueryBuilder('s')
            ->andWhere('s.organisateur = ' . $id);
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
}
