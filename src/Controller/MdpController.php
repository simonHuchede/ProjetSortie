<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ChangePasswordFormType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class MdpController extends AbstractController
{
    //dans cette methode je modifie mon mot de passe
    /**
     * @Route("/changeMdp/{id}", name="modifiermotdepasse")
     */
    public function changeMdp ( Request $request,
                           $id,
                            EntityManagerInterface $em,

                            UserPasswordHasherInterface $userPasswordHasherInterface,
                            UtilisateurRepository $utilisateurRepository

    )
    {
        //je recupère l'utilisateur en session

        $user = $utilisateurRepository->findOneBy(['id' => $id]);
        $mdpform = $this->createForm(ChangePasswordFormType::class, $user);
        $mdpform ->handleRequest($request);

        if ($mdpform->isSubmitted() && $mdpform->isValid()){


            $user->setPassword(

            $userPasswordHasherInterface->hashPassword(
                $user,
                $mdpform->get('plainPassword')->getData()
                )
            );

        $em->flush();
        // Message flash
        $this->addFlash("success", "Votre Mot De passe a été modifié.");
        return $this->redirectToRoute("profil_modifierprofil",['id' => $user->getId()]);

        }
        return $this->render('profil/monprofil.html.twig',[
        'mdpform' => $mdpform->createView()
        ]);
    }
}