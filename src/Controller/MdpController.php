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
    /**
     * @Route("/changeMdp/{id}", name="modifiermotdepasse")
     */
    public function changeMdp ( Request $request,
                           $id,
                            EntityManagerInterface $em,
                            Utilisateur $utilisateur,
                            UserPasswordHasherInterface $userPasswordHasherInterface,
                            UtilisateurRepository $utilisateurRepository

    )
    {
        $utilisateur=$this->getUser();
        $user = $utilisateurRepository->findOneBy(['id' => $id]);
        $mdpform = $this->createForm(ChangePasswordFormType::class, $utilisateur);
        $mdpform ->handleRequest($request);

        if ($mdpform->isSubmitted() && $mdpform->isValid()){

            $newpwd = $mdpform->get('plainPassword')->getData();

            $utilisateur->setPassword(

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