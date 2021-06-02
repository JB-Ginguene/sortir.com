<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Form\ResearchFilterType;
use App\Form\SortieType;
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
    public function home(Request $request, SortieRepository $sortieRepository): Response
    {
        $research = new ResearchFilter();
        $researchForm = $this->createForm(ResearchFilterType::class, $research);

        $researchForm->handleRequest($request);


        if ($researchForm->isSubmitted() && $researchForm->isValid()) {
            $sorties = $sortieRepository->findByPersonnalResearch($research);
            return $this->render('sortie/home.html.twig', [
                'researchForm' => $researchForm->createView(),
                'sorties' => $sorties
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

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            if ($sortieForm->get('enregistrer')->isClicked()){
                //l'état de sortie est juste crée
                $etatSortie = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Créée']);
            }else{
                //l'état de sortie est juste publiée
                $etatSortie = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Ouverte']);
            }

            //On récupère l'utilisateur en session
            /**
             * @var Participant $organisateur
            */
            $organisateur = $this->getUser();

            //on set sur la sortie l'organisateur ainsi que l'état
            $sortie->setOrganisateur($organisateur)->setEtat($etatSortie);

            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Sortie créée');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm'=>$sortieForm->createView(),
            'sortie'=>$sortie
        ]);
    }


    /**
     * @Route("/sortie/edit/{id}", name="sortie_edit")
     */
    public
    function edit($id,Request $request, EntityManagerInterface $entityManager): Response
    {

        $sortie = $entityManager->getRepository(Sortie::class)->find($id);


        //pour limiter l'accès à l'organisateur de la sortie
        if ($this->getUser() != $sortie->getOrganisateur() ){
            return $this->redirectToRoute('sortie_home');
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            if ($sortieForm->get('publier')->isClicked()){
                $etatSortie = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Ouverte']);
                $sortie->setEtat($etatSortie);
            }

            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Sortie éditée');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/edit.html.twig', [
            'sortieForm'=>$sortieForm->createView(),
            'sortie'=>$sortie
        ]);
    }

    /**
     * @Route("/sortie/ajax-site", name="sortie_ajax_site")
     */
    public function infosLieu(Request $request, EntityManagerInterface $entityManager): Response
    {
       $data = json_decode($request->getContent());

       $lieu = $data->lieu;

       $lieu = $entityManager->getRepository(Lieu::class)->findOneBy(['nom'=>$lieu]);

       return new JsonResponse(['rue'=>$lieu->getRue(),
                                'latitude'=>$lieu->getLatitude(),
                                'longitude'=>$lieu->getLongitude(),
                                'code_postal'=>$lieu->getVille()->getCodePostal(),
                                'ville'=>$lieu->getVille()->getNom()]);


    }

}
