<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\ResearchFilter\ResearchFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use function Symfony\Component\String\s;

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

    /**
     * Fonction qui permet d'effectuer une recherche personnalisée grâce à des filtres
     * @param ResearchFilter $research
     * @param EntityManagerInterface $entityManager
     * @return int|mixed|string
     */
    public function findByPersonnalResearch(ResearchFilter $research, EntityManagerInterface $entityManager)
    {
        $etatsPassee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => "Passée"]);
        // on récupère l'id de l'utilisateur connecté :
        $userId = $this->security->getUser()->getId();
        // s = sortie
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->leftJoin('s.participants', 'participant')
            ->leftJoin('s.lieu', 'lieu')
            ->leftJoin('s.etat', 'etat')
            ->leftJoin('s.site', 'site')
            ->leftJoin('lieu.ville', 'ville')
            ->addSelect('participant')
            ->addSelect('etat')
            ->addSelect('lieu')
            ->addSelect('site')
            ->addSelect('ville')

            // on recupere uniquement les sorties NON archivées :
            ->where("s.etat != " . $etatsPassee->getId());

        if ($research->getSite()) {
            $queryBuilder->andWhere('s.site >= :id')
                ->setParameter('id', $research->getSite()->getId());
        }
        if ($research->getNomSortie()) {
            $queryBuilder->andWhere("s.nom LIKE :nom")
                ->setParameter('nom', "%" . $research->getNomSortie() . "%");
        }
        if ($research->getDateMin()) {
            $queryBuilder->andWhere("s.dateHeureDebut >= :dateMin")
                ->setParameter('dateMin', $research->getDateMin()->format('Y-m-d H:i:s'));
        }
        if ($research->getDateMax()) {
            $queryBuilder->andWhere("s.dateHeureDebut <= :dateMax")
                ->setParameter('dateMax', $research->getDateMax()->format('Y-m-d H:i:s'));
        }
        if (in_array('organisateur', $research->getSpecificitees())) {
            $queryBuilder->andWhere('s.organisateur = :userid')
                ->setParameter('userid', $userId);
        }
        if (in_array('inscrit', $research->getSpecificitees())) {
            $queryBuilder->andWhere('participant.id = :userid')
                ->setParameter('userid', $userId);
        }
        if (in_array('noninscrit', $research->getSpecificitees())) {
            $queryBuilder->andWhere('participant.id != :userid')
                ->setParameter('userid', $userId);
        }
        if (in_array('sortiespassees', $research->getSpecificitees())) {
            $now = new \DateTime();
            $nowMinusOneMonth = $now->modify('-1month');
            $queryBuilder->andWhere("s.dateHeureDebut <= :dateNow AND s.dateHeureDebut >= :dateNowMinusOneMonth")
            ->setParameter('dateNow', $now->format('Y-m-d H:i:s'))
            ->setParameter('dateNowMinusOneMonth', $nowMinusOneMonth->format('Y-m-d H:i:s'));
        }
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    /**
     * Fonction qui permet de retourner les sorties grâce à un id organisateur
     * @param $id
     * @return int|mixed|string
     */
    public function findByIdParticipant($id)
    {
        // s = sortie
        $queryBuilder = $this->createQueryBuilder('s')
            ->andWhere('s.organisateur = ' . $id);
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    /**
     * Fonction qui permet d'ajouter un participant à une sortie
     * @param $idSortie
     * @param $idParticipant
     * @param EntityManagerInterface $entityManager
     */
    public function ajouterParticipant($idSortie, $idParticipant, EntityManagerInterface $entityManager)
    {
        $sortie = $this->find($idSortie);
        $participant = $entityManager->getRepository(Participant::class)->find($idParticipant);

        $sortie->addParticipant($participant);
        $entityManager->persist($sortie);
        $entityManager->flush();
    }

    /**
     * Fonction qui permet de retirer un participant d'une sortie
     * @param $idSortie
     * @param $idParticipant
     * @param EntityManagerInterface $entityManager
     */
    public function retirerParticipant($idSortie, $idParticipant, EntityManagerInterface $entityManager)
    {
        $sortie = $this->find($idSortie);
        $participant = $entityManager->getRepository(Participant::class)->find($idParticipant);
        $sortie->removeParticipant($participant);
        $entityManager->persist($sortie);
        $entityManager->flush();
    }


    public function findDetailSortieById($id)
    {
        // s = sortie
        $queryBuilder = $this->createQueryBuilder('s')
            ->leftJoin('s.participants', 'participant')
            ->leftJoin('s.lieu', 'lieu')
            ->leftJoin('s.etat', 'etat')
            ->leftJoin('s.site', 'site')
            ->leftJoin('lieu.ville', 'ville')
            ->addSelect('participant')
            ->addSelect('etat')
            ->addSelect('lieu')
            ->addSelect('site')
            ->addSelect('ville')
            ->where('s.id = ' . $id);
        $query = $queryBuilder->getQuery();
        return $query->getSingleResult();
    }

    /**
     * Fonction qui retourne la liste de toutes les sorties
     * @param EntityManagerInterface $entityManager
     * @return int|mixed|string
     */
    public function findAllForHomePage(EntityManagerInterface $entityManager)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->leftJoin('s.participants', 'participant')
            ->leftJoin('s.lieu', 'lieu')
            ->leftJoin('s.etat', 'etat')
            ->addSelect('participant')
            ->addSelect('etat');
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

}
