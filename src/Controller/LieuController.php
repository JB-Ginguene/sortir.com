<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\ManageEntity\UpdateEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/lieu/create", name="lieu_create")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UpdateEntity $updateEntity): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class,$lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()){
            $updateEntity->save($lieu);
            $this->addFlash('success', 'lieu ajouté, recommencez la saisie');
            return $this->redirectToRoute('sortie_home');
        }

        return $this->render('lieu/create.html.twig', [
            'lieuForm' => $lieuForm->createView(),
        ]);
    }
}
