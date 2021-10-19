<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\ModifierProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MdpController extends AbstractController
{
    /**
     * @Route("/ChangeMdp", name="app_mdp")
     */
    public function login( Request $request,
                           EntityManagerInterface $em,
                            User $user
    ): Response
    {
        $user=$this->getUser();
        $mdpform = $this->createForm(ChangePasswordFormType::class, $user);
        $mdpform ->handleRequest($request);

        if ($mdpform->isSubmitted() && $mdpform->isValid()){

            $newpwd = $mdpform->get('password')->getData();

            $user->setPassword($newpwd);

        $em->flush();
        // Message flash
        $this->addFlash("success", "Votre Mot De passe a été modifié.");
        return $this->redirectToRoute("main_home");

    }
        return $this->render('changemdp.html.twig',[
        'mdpform' => $mdpform->createView()
        ]);
    }
}