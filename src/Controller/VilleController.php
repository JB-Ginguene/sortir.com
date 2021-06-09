<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\ManageEntity\CheckVilleDuplicate;
use App\ManageEntity\UpdateEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    /**
     * @Route("/ville/create", name="ville_create")
     */
    public function index(Request $request, UpdateEntity $updateEntity, CheckVilleDuplicate $checkVilleDuplicate): Response
    {
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()){

            if ($checkVilleDuplicate->checkDuplicate($ville)){
                $this->addFlash('error', 'la ville existe déjà');
                return $this->redirectToRoute('lieu_create');
            }else{

                $updateEntity->save($ville);
                $this->addFlash('success', 'ville ajoutée');
                return $this->redirectToRoute('lieu_create');
            }

        }

        return $this->render('ville/create.html.twig', [
            'villeForm' => $villeForm->createView(),
        ]);
    }
}
