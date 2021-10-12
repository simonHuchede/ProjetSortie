<?php

namespace App\Controller;

use App\Form\ModifierProfilFormType;

use App\Repository\CampusRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/modifierprofil/{id}", name="profil_modifierprofil")
     */
    public function modifierprofil( Request $request,
                                    $id,
                                    UtilisateurRepository $utilisateurRepository,
                                    EntityManagerInterface $em ,
                                    CampusRepository $campusRepository)
    {
        $campus=$campusRepository->findAll();
        $user = $utilisateurRepository->findOneBy(['id' => $id]);
        $modifform = $this->createForm(ModifierProfilFormType::class, $user);
        $modifform->handleRequest($request);


        if ($modifform->isSubmitted() && $modifform->isValid()) {
            $em->flush();
            // Message flash
            $this->addFlash("success", "Votre profil a été modifié.");
            return $this->redirectToRoute("main_home");

        }
        return $this->render('main/monprofil.html.twig', [
            'modifform' => $modifform->createView(),
            'campus'=> $campus,
        ]);
    }
}