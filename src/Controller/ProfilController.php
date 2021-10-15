<?php

namespace App\Controller;

use App\Form\ModifierProfilFormType;

use App\Repository\CampusRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class ProfilController extends AbstractController
{
    /**
     * @Route("/modifierprofil/{id}", name="profil_modifierprofil")
     */
    public function modifierprofil( Request $request,
                                    $id,
                                    UtilisateurRepository $utilisateurRepository,
                                    EntityManagerInterface $em ,
                                    CampusRepository $campusRepository,
                                    SluggerInterface $slugger)
    {
        $campus=$campusRepository->findAll();
        $user = $utilisateurRepository->findOneBy(['id' => $id]);
        $modifform = $this->createForm(ModifierProfilFormType::class, $user);
        $modifform->handleRequest($request);


        if ($modifform->isSubmitted() && $modifform->isValid()) {
            $photoProfil = $modifform->get('image')->getData();

            if ($photoProfil){
                $originalFileName = pathinfo($photoProfil->getClientOriginalName(), PATHINFO_FILENAME);
                $saveFileName = $slugger->slug($originalFileName);
                $newFileName = $saveFileName.'-'.uniqid().'.'.$photoProfil->guessExtension();

                try {
                    $photoProfil->move(
                        $this->getParameter('img_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {

                }
                $user->setImage($newFileName);
            }
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