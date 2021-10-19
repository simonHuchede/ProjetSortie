<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleFormType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gestion",name="gestion_")
 * @IsGranted ("ROLE_ADMIN")
 */
class GestionController extends AbstractController
{
    /**
     *@Route("/gestionApp",name="gestion_app")
     *@IsGranted("ROLE_ADMIN")
     */
    public function gestionApp(){
        $ville = new Ville();
        $formulaireVille=$this->createForm(VilleFormType::class,$ville);
        return $this->renderForm("/gestion/gestionApp.html.twig",compact("formulaireVille"));
    }
/**
 * @Route("/ajouterVille",name="ajouter_ville")
 *@IsGranted("ROLE_ADMIN")
 */
public function ajouterVille(Request $request, EntityManagerInterface $em){
    $ville = new Ville();
    $formulaireVille=$this->createForm(VilleFormType::class,$ville);
    $formulaireVille->handleRequest($request);
    $em->persist($ville);
    $em->flush();
    return $this->redirectToRoute("gestion_gestion_app");
}
}