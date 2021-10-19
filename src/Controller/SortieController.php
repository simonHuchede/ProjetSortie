<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\ModifierSortieFormType;
use App\Form\SortieFormType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie",name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/creerSortie/{id}", name="creerSortie")
     */
    public function creerSortie(Request $request,
                                 EntityManagerInterface $entityManager,
                                $id,UtilisateurRepository $utilisateurRepository,EtatRepository $repoEtat){
        $utilisateur=$utilisateurRepository->findOneBy(['id'=>$id]);
        $sortie = new Sortie();
        $formulaireSortie= $this->createForm(SortieFormType::class,$sortie);
        $formulaireSortie->handleRequest($request);
        $info = $request->get('info');
        $dateCloture = $sortie->getDateLimiteInscription();
        $dateDebut = $sortie->getDateHeureDebut();


        if ($formulaireSortie->isSubmitted() && $formulaireSortie->isValid() && $info == '1' && $dateCloture < $dateDebut){
            $etat = $repoEtat->find(1);
            $sortie->setOrganisateur($utilisateur);
            $sortie->setCampus($utilisateur->getCampus());
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this -> redirectToRoute("main_home");
        } elseif ($formulaireSortie->isSubmitted() && $formulaireSortie->isValid() && $info == '2' && $dateCloture < $dateDebut){
            $etat = $repoEtat->find(2);
            $sortie->setOrganisateur($utilisateur);
            $sortie->setCampus($utilisateur->getCampus());
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this -> redirectToRoute("main_home");
        }
            return $this->renderForm("sortie/creerSortie.html.twig", compact('formulaireSortie'));
    }
    /**
     * @Route("/listSorties",name="listSorties")
     */
    public function listSorties()
    {

        return $this->render("sortie/listSorties.html.twig");
    }


    /**
     * @Route("/api/listSorties/",name="api_listSorties")
     */
    public function apiListSorties(SortieRepository $sortieRepository)
    {

        $listSorties=$sortieRepository->findAll();
        $tableau=[];

        // boucle foreach pour récuperer tout ce qu'il y a dans le tableau
        foreach ($listSorties as $sortie){
            $tab['id']=$sortie->getId() ;
            $tab['nom']=$sortie->getNom() ;
            $tab['dateHeureDebut']=$sortie->getDateHeureDebut() ;
            $tab['dateLimiteInscription']=$sortie->getDateLimiteInscription() ;
            $tab['nbInscriptionMax']=$sortie->getNbInscriptionsMax() ;
            $tab['etat']=$sortie->getEtat()->getLibelle() ;
            $tab['organisateur']=$sortie->getOrganisateur()->getPseudo() ;
            $tab['nb']=$sortie->getNbParticipants();
            //$tab['participants']=$sortie->getParticipants() ;

            $tableau[]=$tab;
        }


        return $this->json($tableau);
    }



    /**
     * @Route("/detail/{sortie}", name="sortieDetail")
     */
    public function detailSortie(Sortie $sortie){

        return $this->render("sortie/detail.html.twig", compact('sortie'));
    }
    /**
     * @Route("/sinscrire/{id}",name="sinscrire")
     */
    public function sinscrire(Sortie $sortie,EntityManagerInterface $em, EtatRepository $etatRepo){
        $etat = $etatRepo->find(2);
        $etat2 = $sortie->getEtat();
        $dateDebut = $sortie->getDateLimiteInscription();
        $nbInscrits = $sortie->getNbParticipants();
        $nbInscritsMax = $sortie->getNbInscriptionsMax();

        if ((new \DateTime('now')) < $dateDebut && $nbInscrits < $nbInscritsMax && $etat === $etat2 ) {
            $user =$this->getUser();
            $sortie->addParticipant($user);
            $em->persist($sortie);
            $em->flush();
        }

        return $this->redirectToRoute("sortie_listSorties");

    }
    /**
     * @Route("/seDesister/{id}", name="seDesister")
     */
    public function seDesister(Sortie $sortie,EntityManagerInterface $em){
        $dateDebut = $sortie->getDateHeureDebut();

        if ((new \DateTime('now')) < $dateDebut) {
            $user=$this->getUser();
            $sortie->removeParticipant($user);
            $em->persist($sortie);
            $em->flush();}


        return $this->redirectToRoute( "sortie_listSorties");
    }

    /**
     * @Route("/modifierSortie/{id}", name="modifierSortie")
     */
    public function modifierSortie(Sortie $sortie, EntityManagerInterface $em, Request $request){
        $formulaireModifierSortie=$this->createForm(ModifierSortieFormType::class,$sortie);
        $formulaireModifierSortie->handleRequest($request);
        $userId = $this->getUser()->getId();
        $user2Id = $sortie->getOrganisateur()->getId();

        if($userId == $user2Id) {
            if($formulaireModifierSortie->isSubmitted() && $formulaireModifierSortie->isValid()){
                $em->flush();
                $this->addFlash("success", "La sortie a été modifiée.");
                return $this->redirectToRoute("sortie_listSorties");
            }}




        return $this->render("/sortie/modifierSortie.html.twig",
            ['formulaireModifierSortie'=>$formulaireModifierSortie->createView(),
                'id'=>$sortie->getId()]
        );
    }

    /**
     * @Route("/annulerSortie/{id}", name="annulerSortie")
     */
    public function annulerSortie(Sortie $sortie, EntityManagerInterface $em, EtatRepository $etatRepo, Utilisateur $utilisateur){
        $userId = $this->getUser()->getId();
        $user2Id = $sortie->getOrganisateur()->getId();

        if ($userId == $user2Id) {
            $etat = $etatRepo->find(6);
            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash("succes", "La sortie a été annulée.");
        }
        return $this->render("sortie/listSorties.html.twig");
    }
    /**
     * @Route("/afficherSortie/{id}", name="afficherSortie");
     */
    public function afficherSortie(Sortie $sortie){

        return $this->render("sortie/afficherSortie.html.twig",
            compact('sortie'));
    }
    /**
     * @Route("/api/listParticipants/{id}/", name="api_listParticipants")
     */
    public function apiListParticipants(Sortie $sortie){
        $listParticipants=$sortie->getParticipants();
        $tableau=[];
        foreach ($listParticipants as $participant){
            $tab['id']=$participant->getId();
            $tab['pseudo']=$participant->getPseudo();
            $tab['prenom']=$participant->getPrenom();
            $tab['nom']=$participant->getNom();
            $tableau[]=$tab;
        }
        return $this->json($tableau);
    }
}