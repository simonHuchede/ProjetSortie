<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Form\RegistrationFormType;
use App\Form\VilleFormType;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/participant/gestion",name="gestion_")
 * @IsGranted("ROLE_ADMIN")
 */
class GestionController extends AbstractController
{
    /**
     * @Route("/participant/gestionApp",name="gestion_app")
     * @IsGranted("ROLE_ADMIN")
     */
    public function gestionApp(UtilisateurRepository $utilisateurRepository,
                               SortieRepository $sortieRepository){
        $archives=$sortieRepository->findByEtat(7);

        $user = new Utilisateur();
        $registrationForm  = $this->createForm(RegistrationFormType::class, $user);
        $ville = new Ville();
        $formulaireVille=$this->createForm(VilleFormType::class,$ville);
        $utilisateurs=$utilisateurRepository->findAll();
        return $this->renderForm("/gestion/gestionApp.html.twig",compact("formulaireVille","utilisateurs","registrationForm","archives"));
    }
/**
 * @Route("/ajouterVille",name="ajouter_ville")
 * @IsGranted("ROLE_ADMIN")
 *
 */
public function ajouterVille(Request $request, EntityManagerInterface $em){

    $ville = new Ville();
    $formulaireVille=$this->createForm(VilleFormType::class,$ville);
    $formulaireVille->handleRequest($request);
    $em->persist($ville);
    $em->flush();
    return $this->redirectToRoute("gestion_gestion_app");
}
    /**
     * @Route("/afficherUtilisateur/{id}",name="afficher_utilisateur")
     * @IsGranted("ROLE_ADMIN")
     */
    public function afficherUtilisateurs(UtilisateurRepository $utilisateurRepository){
        $utilisateurs=$utilisateurRepository->findAll();
        return $this->render("gestion/gestionApp.html.twig",
        compact('utilisateurs'));
    }
/**
 * @Route("/supprimerUtilisateur/{id}",name="supprimer_utilisateur")
 * @IsGranted("ROLE_ADMIN")
 */
public function supprimerUtilisateur(Utilisateur $u,
                                     EntityManagerInterface $em){

 $em->remove($u);
 $em->flush();
 return $this->redirectToRoute("gestion_gestion_app");
}
/**
 * @Route("/afficherArchives",name="afficher_archives")
 * @IsGranted("ROLE_ADMIN")
 */
public function afficherArchives(SortieRepository $sortieRepository){
    $archives=$sortieRepository->findByEtat(7);
    return $this->render("gestion/gestionApp.html.twig",
    compact("archives"));
}
}