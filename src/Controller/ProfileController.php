<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\ProfileFormType;
use App\ManageEntity\UpdateEntity;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{

    /**
     * @Route("/profile/{id}", name="profile_edit", requirements={"id"="\d+"})
     */
    public function edit($id,
                         ParticipantRepository $participantRepository,
                         Request $request,
                         EntityManagerInterface $entityManager,
                         UserPasswordEncoderInterface $passwordEncoder,
                         UpdateEntity $updateEntity): Response
    {

        $profile = $participantRepository->find($id);
        if (!$profile) {
            throw $this->createNotFoundException("Ce profile n'existe pas");
        }
        $profileForm = $this->createForm(ProfileFormType::class, $profile);
        $profileForm->handleRequest($request);


        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            //hash du password
            $profile->setPassword(
                $passwordEncoder->encodePassword(
                    $profile,
                    $profileForm->get('password')->getData()
                ));

            $file = $profileForm->get('avatar')->getData();

            /**
             * @var UploadedFile $file
             */

            if ($file) {
                $newFileName = $profile->getNom() . '-' . uniqid() . '-' . $file->guessExtension();
                $file->move($this->getParameter('upload_profile_avatar_dir'), $newFileName);
                $profile->setAvatar($newFileName);
            }

            $updateEntity->save($profile);

            $this->addFlash('success', 'profile mis à jour');
            return $this->redirectToRoute('profile_detail',[
                'id'=> $profile->getId()
            ]);
        }

        return $this->render('profile/edit.html.twig', [
            'profile' => $profile,
            'profileForm' => $profileForm->createView()
        ]);
    }

    /**
     * @Route("/profile/detail/{id}", name="profile_detail", requirements={"id"="\d+"})
     */
    public function detail($id, ParticipantRepository $participantRepository, SortieRepository $sortieRepository): Response
    {
        $profile = $participantRepository->find($id);
        $sortiesOrganisees = $sortieRepository->findByIdParticipant($id);

        if (!$profile) {
            throw $this->createNotFoundException("Désolé, ce profile n'existe pas");
        }
        return $this->render('profile/detail.html.twig', [
            'profile' => $profile,
            'sortiesOrganisees' => $sortiesOrganisees
        ]);
    }

    /**
     * @Route("/admin/profile/management", name="profile_management")
     */
    public function profileManagement(ParticipantRepository $participantRepository): Response
    {
        $participants= $participantRepository->findAll();
        if (!$participants) {
            throw $this->createNotFoundException("Désolé, aucun particpants!");
        }
        return $this->render('profile/management.html.twig', [
            'participants' => $participants
        ]);
    }

    /**
     * @Route("/admin/profile/ajax-profile-delete", name="profile_management_ajax_profile_delete")
     */
    public function desinscriptionSortieDetail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent());
        $participantId = $data->participantId;
        $participant = $entityManager->getRepository(Participant::class)->find($participantId);
        $entityManager->remove($participant);
        $entityManager->flush();
        return new JsonResponse([
            'participantid' => $participantId,
            'nom' => $participant->getNom(),
            'prenom'=>$participant->getPrenom()
        ]);
    }

}
