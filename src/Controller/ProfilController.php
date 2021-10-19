<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ModifierProfilFormType;

use App\Repository\CampusRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/profil",name="profil_")
 */
class ProfilController extends AbstractController
{
    /**
     * @Route("/modifierprofil/{id}", name="modifierprofil")
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
        $info = $request->get('infoProfil');
        $modifform = $this->createForm(ModifierProfilFormType::class, $user);
        $modifform->handleRequest($request);


        if ($modifform->isSubmitted() && $modifform->isValid() && $info == '1') {
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
            return $this->redirectToRoute("profil_modifierprofil",['id' => $id]);

        }
        return $this->render('profil/monprofil.html.twig', [
            'modifform' => $modifform->createView(),
            'campus'=> $campus,
        ]);
    }

    /**
     * @Route("/afficherUnProfil/{id}",name="afficherUnProfil")
     */
    public function afficherUnProfil(Utilisateur $utilisateur){

    return $this->render("profil/afficherUnProfil.html.twig",compact('utilisateur'));
    }
}