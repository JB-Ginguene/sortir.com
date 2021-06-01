<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\ResearchFilterType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\ResearchFilter\ResearchFilter;
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
    public function home(Request $request, SortieRepository $sortieRepository): Response
    {
        $research = new ResearchFilter();
        $researchForm = $this->createForm(ResearchFilterType::class, $research);

        $researchForm->handleRequest($request);


        if ($researchForm->isSubmitted() && $researchForm->isValid()) {
            $properties = $sortieRepository->findByPersonnalResearch($research);
            return $this->render('sortie/home.html.twig', [
                'researchForm' => $researchForm->createView(),
                'properties' => $properties
            ]);
        } else {
            $sorties = $sortieRepository->findAll();
            $userInSession = $this->getUser();
            return $this->render('sortie/home.html.twig', [
                'researchForm' => $researchForm->createView(),
                'sorties' => $sorties
            ]);
        }
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
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('succes', 'Sortie crée');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', [

        ]);
    }

}
