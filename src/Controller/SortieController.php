<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sortie_home")
     */
    public function home(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();
        $userInSession = $this->getUser();
        return $this->render('sortie/home.html.twig', [
            'sorties' => $sorties
        ]);
    }

    /**
     * @Route("/sortie/detail/{id}", name="sortie_detail")
     */
    public
    function detail($id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException("Désolé, la sortie demandée n'existe pas dans notre base de données");
        }

        return $this->render('sortie/detail.html.twig', [
            "sortie" => $sortie
        ]);
    }

    /**
     * @Route("/sortie/create", name="sortie_create")
     */
    public
    function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class,$sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            //On set l'état de la sortie à "Ouvert"
            $etatSortie = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Ouverte']);
            //On récupère l'utilisateur en session
            $organisateur = $this->getUser();

            //on set sur la sortie
            $sortie->setOrganisateur($organisateur)->setEtat($etatSortie);


            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Sortie créée');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm'=>$sortieForm->createView()
        ]);
    }

}
