<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\ResearchFilterType;
use App\Form\SortieType;
use App\ManageEntity\UpdateSorties;
use App\ManageEntity\UpdateEntity;
use App\ManageRoutes\CustomRedirections;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\ResearchFilter\ResearchFilter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sortie_home")
     */
    public function home(Request $request,
                         SortieRepository $sortieRepository,
                         EntityManagerInterface $entityManager,
                         UpdateSorties $updateSorties,
                         CustomRedirections $customRedirections): Response
    {
        //Redirection au login si il n'y a pas d'utilisateur connecté
        if (null == $this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $research = new ResearchFilter();
        $researchForm = $this->createForm(ResearchFilterType::class, $research);
        $researchForm->handleRequest($request);
        if ($researchForm->isSubmitted() && $researchForm->isValid()) {
            $sorties = $sortieRepository->findByPersonnalResearch($research, $entityManager);
            return $this->render('sortie/home.html.twig', [
                'researchForm' => $researchForm->createView(),
                'sorties' => $sorties
            ]);
        } else {

            $sorties = $sortieRepository->findAllForHomePage($entityManager);

            //Actualisation des états de sorties
            $sortiesActualisees = $updateSorties->actualisationSorties($sorties);

            $userInSession = $this->getUser();

            return $this->render('sortie/home.html.twig', [
                'researchForm' => $researchForm->createView(),
                'sorties' => $sortiesActualisees
            ]);
        }
    }

    /**
     * @Route("/sortie/detail/{id}", name="sortie_detail", requirements={"id"="\d+"})
     */
    public
    function detail($id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->findDetailSortieById($id);

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
    function create(Request $request, EntityManagerInterface $entityManager, UpdateEntity $updateEntity): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            if ($sortieForm->get('enregistrer')->isClicked()) {
                //l'état de sortie est juste crée
                $etatSortie = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Créée']);
            } else {
                //l'état de sortie est juste publiée
                $etatSortie = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
            }

            //On récupère l'utilisateur en session
            /**
             * @var Participant $organisateur
             */
            $organisateur = $this->getUser();

            //on set sur la sortie l'organisateur ainsi que l'état
            $sortie->setOrganisateur($organisateur)->setEtat($etatSortie);

            $updateEntity->save($sortie);

            $this->addFlash('success', 'Sortie créée');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie
        ]);
    }


    /**
     * @Route("/sortie/edit/{id}", name="sortie_edit", requirements={"id"="\d+"})
     */
    public
    function edit($id, Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository, UpdateEntity $updateEntity): Response
    {

        $sortie = $entityManager->getRepository(Sortie::class)->find($id);


        //pour limiter l'accès à l'organisateur de la sortie
        if ($this->getUser() != $sortie->getOrganisateur()) {
            return $this->redirectToRoute('sortie_home');
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            //Verification si l'utilisateur souhaite annuler la sortie
            if (!$sortieForm->get('annuler')->isClicked()) {

                //Verification si l'utilisateur souhaite publier une sortie
                if ($sortieForm->get('publier')->isClicked()) {
                    $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));
                }

                $updateEntity->save($sortie);
                $this->addFlash('success', 'Sortie éditée');
                $redirection = $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);

            } else {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Annulée']));
                $updateEntity->save($sortie);
                $this->addFlash('success', 'Sortie annulée');

                $redirection = $this->redirectToRoute('sortie_home');
            }

            return $redirection;
        }
        return $this->render('sortie/edit.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route("/sortie/ajax-site", name="sortie_ajax_site")
     */
    public function infosLieu(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent());

        $lieu = $data->lieu;

        $lieu = $entityManager->getRepository(Lieu::class)->findOneBy(['nom' => $lieu]);

        return new JsonResponse(['rue' => $lieu->getRue(),
            'latitude' => $lieu->getLatitude(),
            'longitude' => $lieu->getLongitude(),
            'code_postal' => $lieu->getVille()->getCodePostal(),
            'ville' => $lieu->getVille()->getNom()]);
    }

    /**
     * @Route("/sortie/edit/ajax-site", name="edit_ajax_site")
     */
    public function infosLieu2(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent());

        $lieu = $data->lieu;

        $lieu = $entityManager->getRepository(Lieu::class)->findOneBy(['nom' => $lieu]);

        return new JsonResponse(['rue' => $lieu->getRue(),
            'latitude' => $lieu->getLatitude(),
            'longitude' => $lieu->getLongitude(),
            'code_postal' => $lieu->getVille()->getCodePostal(),
            'ville' => $lieu->getVille()->getNom()]);
    }

    /**
     * @Route("/ajax-sortie-inscription", name="ajax_sortie_inscription")
     */
    public function inscriptionSortie(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent());
        $userid = $data->userid;
        $sortieid = $data->sortieid;
        $sortie = $entityManager->getRepository(Sortie::class)->find($sortieid);
        $entityManager->getRepository(Sortie::class)->ajouterParticipant($sortieid, $userid, $entityManager);
        return new JsonResponse([
            'sortieid' => $sortieid,
            'userid' => $userid,
            'participant' => $sortie->getParticipants()->count(),
            'participantMax' => $sortie->getNbInscriptionsMax()
        ]);
    }

    /**
     * @Route("/ajax-sortie-desinscription", name="ajax_sortie_desinscription")
     */
    public function desinscriptionSortie(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent());
        $userid = $data->userid;
        $sortieid = $data->sortieid;
        $entityManager->getRepository(Sortie::class)->retirerParticipant($sortieid, $userid, $entityManager);
        $sortie = $entityManager->getRepository(Sortie::class)->find($sortieid);
        return new JsonResponse([
            'sortieid' => $sortieid,
            'userid' => $userid,
            'participant' => $sortie->getParticipants()->count(),
            'participantMax' => $sortie->getNbInscriptionsMax(),
        ]);
    }

    /**
     * @Route("/sortie/detail/inscription", name="detail_inscription")
     */
    public function inscriptionSortieDetail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent());
        $userid = $data->userid;
        $sortieid = $data->sortieid;
        $sortie = $entityManager->getRepository(Sortie::class)->find($sortieid);
        $entityManager->getRepository(Sortie::class)->ajouterParticipant($sortieid, $userid, $entityManager);
        return new JsonResponse([
            'sortieid' => $sortieid,
            'userid' => $userid,
            'participant' => $sortie->getParticipants()->count(),
            'participantMax' => $sortie->getNbInscriptionsMax()
        ]);
    }

    /**
     * @Route("/sortie/detail/desinscription", name="detail_desinscription")
     */
    public function desinscriptionSortieDetail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent());
        $userid = $data->userid;
        $sortieid = $data->sortieid;
        $entityManager->getRepository(Sortie::class)->retirerParticipant($sortieid, $userid, $entityManager);
        $sortie = $entityManager->getRepository(Sortie::class)->find($sortieid);
        return new JsonResponse([
            'sortieid' => $sortieid,
            'userid' => $userid,
            'participant' => $sortie->getParticipants()->count(),
            'participantMax' => $sortie->getNbInscriptionsMax()
        ]);
    }
}
