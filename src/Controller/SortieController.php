<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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



        return $this->render('sortie/home.html.twig', [
            'sorties' => $sorties,
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
}
