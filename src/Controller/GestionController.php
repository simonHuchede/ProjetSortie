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
        //comme on utilise des modales, on doit recuperer toutes les vraiables dans la methode du twig
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
    //j'instancie une nouvelle ville
    $ville = new Ville();
    //je cree le formulaire
    $formulaireVille=$this->createForm(VilleFormType::class,$ville);
    //je recupere les données saisies
    $formulaireVille->handleRequest($request);
    //je les rentre en base
    $em->persist($ville);
    $em->flush();
    //je redirige
    return $this->redirectToRoute("gestion_gestion_app");
}
    /**
     * @Route("/afficherUtilisateur/{id}",name="afficher_utilisateur")
     * @IsGranted("ROLE_ADMIN")
     */
    public function afficherUtilisateurs(UtilisateurRepository $utilisateurRepository){
        //je recupère tous mes utilisateurs
        $utilisateurs=$utilisateurRepository->findAll();
        return $this->render("gestion/gestionApp.html.twig",
        //je les envois dans le twig
        compact('utilisateurs'));
    }
/**
 * @Route("/supprimerUtilisateur/{id}",name="supprimer_utilisateur")
 * @IsGranted("ROLE_ADMIN")
 */
public function supprimerUtilisateur(Utilisateur $u,
                                     EntityManagerInterface $em){
//je reccupere un objet $u en injection de dependance, je lui applique un remove et le flush(effectif en base de donnée )
 $em->remove($u);
 $em->flush();
 return $this->redirectToRoute("gestion_gestion_app");
}
/**
 * @Route("/afficherArchives",name="afficher_archives")
 * @IsGranted("ROLE_ADMIN")
 */
public function afficherArchives(SortieRepository $sortieRepository){
    //je recupère en base les sorties qui on pour etat l'id 7 (archivee)
    $archives=$sortieRepository->findByEtat(7);
    return $this->render("gestion/gestionApp.html.twig",
    //je renvois les archives à mon twig
    compact("archives"));
}
//dans cette methode je modifie l'attribu actif de l'utilisateur selectionné pour lui set à false
/**
 * @Route("/rendreInactif/{id}",name="rendre_inactif")
 */
public function rendreInactif(Utilisateur $utilisateur,
                              EntityManagerInterface $em){
    $utilisateur->setActif(false);
    $em->flush();
    return $this->redirectToRoute("gestion_gestion_app");
}
//dans cette methode je modifie l'attribu actif de l'utilisateur selectionné pour lui set à true

/**
 * @Route("/rendreActif/{id}",name="rendre_actif")
 */
public function rendreActif(Utilisateur $utilisateur,
                            EntityManagerInterface $em){
    $utilisateur->setActif(true);
    $em->flush();
    return $this->redirectToRoute("gestion_gestion_app");
}
/**
 * @Route("/rendreAdmin/{id}",name="rendre_admin")
 */
public function rendreAdmin(Utilisateur $utilisateur,
                            EntityManagerInterface $em){
    $utilisateur->setAdministrateur(true);
    $em->flush();
    return $this->redirectToRoute("gestion_gestion_app");
}
}