<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
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


        if ($formulaireSortie->isSubmitted() && $formulaireSortie->isValid()){
            $etat = $repoEtat->find(1);
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
    public function sinscrire(Sortie $sortie,EntityManagerInterface $em){
    $user =$this->getUser();
    $sortie->addParticipant($user);
    $em->persist($sortie);
    $em->flush();

        return $this->render("sortie/listSorties.html.twig");

    }
    /**
     * @Route("/seDesister/{id}", name="seDesister")
     */
    public function seDesister(Sortie $sortie,EntityManagerInterface $em){
        $user=$this->getUser();
        $sortie->removeParticipant($user);
        $em->persist($sortie);
        $em->flush();
        return $this->render("sortie/listSorties.html.twig");
    }
    /**
     * Route("/modifierSortie/{id}",name="modifierSortie")
     */
    public function modifierSortie(Sortie $sortie, EntityManagerInterface $em, Request $request){
        $formulaireModifierSortie=$this->createForm(ModifierSortieFormType::class,$sortie);
        $formulaireModifierSortie->handleRequest($request);

       if($formulaireModifierSortie->isSubmitted() && $formulaireModifierSortie->isValid()){
         $em->flush();
           $this->addFlash("success", "La sortie à été modifiée.");
           return $this->redirectToRoute("sortie_listSorties");
       }



        return $this->renderForm("/sortie/modifierSortie.html.twig",
        compact('formulaireModifierSortie')
        );
    }
}