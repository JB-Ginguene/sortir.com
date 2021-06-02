<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{

    /**
     * @Route("/profile/{id}", name="app_profile")
     */

    public function edit($id,
                         ParticipantRepository $participantRepository,
                         Request $request,
                         EntityManagerInterface $entityManager): Response
    {

        $profile =$participantRepository->find($id);
        if (!$profile){
            throw $this->createNotFoundException("ce profil n'existe pas");
        }
        $profileForm = $this->createForm(ProfileFormType::class, $profile);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted()&& $profileForm->isValid()){
            $entityManager->persist($profile);
            $entityManager->flush();

            $this->addFlash('success', 'profil mis à jour');
            return $this->redirectToRoute('main_team');
        }

        return $this->render('profile/edit.html.twig', [
            'profile' => $profile,
            'profileForm'=>$profileForm->createView()
        ]);
    }

}
