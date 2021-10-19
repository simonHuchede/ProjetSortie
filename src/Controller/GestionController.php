<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gestion",name="gestion_"
 */
class GestionController extends AbstractController
{
/**
 * @Route ("/ajouterVille",name="ajouter_ville")
 *@IsGranted('ROLE_ADMIN')
 */
public function ajouterVille(){

}
}