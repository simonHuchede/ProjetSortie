<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
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
    public function listSorties(SortieRepository $sortieRepository)
    {
        $listSorties=$sortieRepository->findAll();
        return $this->render("sortie/listSorties.html.twig",
        compact('listSorties')
        );
    }

    /**
     * @Route("/detail/{sortie}", name="sortieDetail")
     */
    public function detailSortie(Sortie $sortie){

        return $this->render("sortie/detail.html.twig", compact('sortie'));
    }
}