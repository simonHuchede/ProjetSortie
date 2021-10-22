<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Form\LieuFormType;
use App\Form\ModifierSortieFormType;
use App\Form\SortieFormType;
use App\Form\VilleFormType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VilleRepository;
use App\services\Services;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @Route("/sortie",name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/creerSortie/", name="creerSortie")
     */
    public function creerSortie(Request $request,
                                 EntityManagerInterface $entityManager,
                                UtilisateurRepository $utilisateurRepository,EtatRepository $repoEtat, LieuRepository $repoLieu){
        //$utilisateur=$utilisateurRepository->findOneBy(['id'=>$id]);
        $ville = new Ville();
        $formulaireVille = $this->createForm(VilleFormType::class,$ville);
        $utilisateur = $this->getUser();
        $sortie = new Sortie();
        $formulaireSortie= $this->createForm(SortieFormType::class,$sortie);
        $formulaireSortie->handleRequest($request);
        $info = $request->get('info');
        $dateCloture = $sortie->getDateLimiteInscription();
        $dateDebut = $sortie->getDateHeureDebut();
        $newLieu = new Lieu();
        $formulaireLieu = $this->createForm(LieuFormType::class,$newLieu);
        $dateDuJour = new \DateTime('now');
        if ($formulaireSortie->isSubmitted() && $formulaireSortie->isValid() &&  $dateCloture < $dateDebut && $dateDuJour < $dateCloture){
            if($info =='1'){
                $etat = $repoEtat->find(1);
            }elseif ($info == '2'){
                $etat = $repoEtat->find(2);
            }
            $lieuId = $request->get('lieu');
            $lieu = $repoLieu->find($lieuId);
            $sortie->setLieu($lieu);
            $sortie->setOrganisateur($utilisateur);
            $sortie->setCampus($utilisateur->getCampus());
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this -> redirectToRoute("sortie_listSorties");
        }

            return $this->renderForm("sortie/creerSortie.html.twig", compact('formulaireSortie','formulaireLieu','formulaireVille'));
    }
    /**
     * @Route("/api/ville-lieu/",name="apiVilleLieu")
     */
    public function apiVilleLieu (VilleRepository $villeRepository,LieuRepository $lieuRepository){

        $villes=$villeRepository->findAll();
        $tab_villes=[];

        foreach ($villes as $v)
        {
           $infos_v['id'] = $v->getId();
           $infos_v['nom'] = $v->getNom();
           $infos_v['codePostal'] = $v->getCodePostal();
           $tab_villes[]=$infos_v;

        }

        $lieux=$lieuRepository->findAll();
        $tab_lieux=[];

        foreach ($lieux as $lieu)
        {
            $infos_l['id'] = $lieu->getId();
            $infos_l['nom'] = $lieu->getNom();
            $infos_l['rue'] = $lieu->getRue();
            $infos_l['latitude'] = $lieu->getLatitude();
            $infos_l['longitude'] = $lieu->getLongitutde();
            $infos_l['ville'] = $lieu->getVille()->getId();
            $tab_lieux[]=$infos_l;

        }
        $tab["villes"]=$tab_villes;
        $tab["lieux"]=$tab_lieux;

        return $this->json($tab);
    }




    /**
     * @Route("/listSorties",name="listSorties")
     */
    public function listSorties(CampusRepository $repoCampus)
    {
        $listCampus = $repoCampus->findBy([],["nom"=>"ASC"]);
        return $this->render("sortie/listSorties.html.twig", compact('listCampus'));
    }


    /**
     * @Route("/api/listSorties/",name="api_listSorties")
     */
    public function apiListSorties(SortieRepository $sortieRepository, Services $service)
    {


        $tableau=[];
        $user=$this->getUser();
        $service->clotureInscription();
        $service->archiver();
        $service->estPassee();
        $service->estEnCours();
        $listSorties=$sortieRepository->findAll();

        // boucle foreach pour récuperer tout ce qu'il y a dans le tableau
        foreach ($listSorties as $sortie){

            $dateDebut = $sortie->getDateHeureDebut();

            $tab['id']=$sortie->getId() ;
            $tab['nom']=$sortie->getNom() ;
            $tab['dateHeureDebut']=$sortie->getDateHeureDebut() ;
            $tab['dateLimiteInscription']=$sortie->getDateLimiteInscription() ;
            $tab['nbInscriptionMax']=$sortie->getNbInscriptionsMax() ;
            $tab['etat']=$sortie->getEtat()->getLibelle() ;
            $tab['idPseudo']=$sortie->getOrganisateur()->getId();
            $tab['organisateur']=$sortie->getOrganisateur()->getPseudo() ;
            $tab['nb']=$sortie->getNbParticipants();
            $tab['campus']=$sortie->getCampus()->getId();
            $tab['estOrganisateur']=$service->verifEstOrganisateur($sortie, $user);
            $tab['estInscrit']=$service->verifEstInscrit($sortie,$user);
            $tab['heureComparaison']= $dateDebut;
            $tab['estCloturee']=$service->verifEstCloture($sortie);
            $tab['estArchivee']=$service->verifEstArchivee($sortie);
            $tab['estAdmin']= $service->verifEstAdministrateur($user);
            $tab['estPassee']=$service->verifEstPassee($sortie);
            $tab['estAnnulee']=$service->verifEstAnnulee($sortie);
            $tab['estCree']=$service->verifEstCree($sortie);
            //$tab['participants']=$sortie->getParticipants() ;

            $tableau[]=$tab;
        }


        return $this->json($tableau);
    }

    public function clotureInscription (SortieRepository $sortieRepository,
                                 EntityManagerInterface $em,
                                 EtatRepository $etatRepo){
        $etat = $etatRepo->find(3);
        $sorties=$sortieRepository->findAll();
        foreach ($sorties as $sortie ){
            if ((new \DateTime('now')) == $sortie->getDateLimiteInscription()) {
                $sortie->setEtat($etat);
            }
        }
    }

    /**
     * @Route("/sinscrire/{id}",name="sinscrire")
     */
    public function sinscrire(Sortie $sortie,EntityManagerInterface $em, EtatRepository $etatRepo){
        $etat = $etatRepo->find(2);
        $etat2 = $sortie->getEtat();
        $datelimite = $sortie->getDateLimiteInscription();
        $nbInscrits = $sortie->getNbParticipants();
        $nbInscritsMax = $sortie->getNbInscriptionsMax();

        if ((new \DateTime('now')) < $datelimite && $nbInscrits < $nbInscritsMax && $etat === $etat2 ) {
            $user =$this->getUser();
            $sortie->addParticipant($user);
            $em->persist($sortie);
            $em->flush();
        }

        return $this->redirectToRoute("sortie_listSorties");

    }
    /**
     * @Route("/seDesister/{id}", name="seDesister")
     */
    public function seDesister(Sortie $sortie,EntityManagerInterface $em){
        $dateDebut = $sortie->getDateHeureDebut();

        if ((new \DateTime('now')) < $dateDebut) {
            $user=$this->getUser();
            $sortie->removeParticipant($user);
            $em->persist($sortie);
            $em->flush();}


        return $this->redirectToRoute( "sortie_listSorties");
    }

    /**
     * @Route("/modifierSortie/{id}", name="modifierSortie")
     */
    public function modifierSortie(Sortie $sortie, EntityManagerInterface $em, Request $request, EtatRepository $repoEtat){
        $formulaireModifierSortie=$this->createForm(ModifierSortieFormType::class,$sortie);
        $formulaireModifierSortie->handleRequest($request);
        $infoSortie = $request->get('infoSortie');
        $userId = $this->getUser()->getId();
        $user2Id = $sortie->getOrganisateur()->getId();

        if($userId == $user2Id) {
            if($formulaireModifierSortie->isSubmitted() && $formulaireModifierSortie->isValid()){
                if($infoSortie == '1'){
                    $em->flush();
                    $this->addFlash("success", "La sortie a été modifiée.");
                } elseif ($infoSortie == '2'){
                    $etat = $repoEtat->find(2);
                    $sortie->setEtat($etat);
                    $em->flush();
                    $this->addFlash("success", "La sortie a été publiée.");
                }

                return $this->redirectToRoute("sortie_listSorties");
            }}




        return $this->render("/sortie/modifierSortie.html.twig",
            ['formulaireModifierSortie'=>$formulaireModifierSortie->createView(),
                'id'=>$sortie->getId(),
                'sortie'=>$sortie]
        );
    }

    /**
     * @Route("/annulerSortie/{id}", name="annulerSortie")
     */
    public function annulerSortie(Sortie $sortie, EntityManagerInterface $em, EtatRepository $etatRepo){
        $userId = $this->getUser()->getId();
        $user2Id = $sortie->getOrganisateur()->getId();
        $user3Id = $this->getUser()->getAdministrateur();

        if (($userId == $user2Id) OR $user3Id== true) {
            $etat = $etatRepo->find(6);
            $sortie->setEtat($etat);
           // $em->persist($sortie);
            $em->flush();
            $this->addFlash("succes", "La sortie a été annulée.");
        }
        return $this->redirectToRoute("sortie_listSorties");
    }
    /**
     * @Route("/afficherSortie/{id}", name="afficherSortie");
     */
    public function afficherSortie(Sortie $sortie){

        return $this->render("sortie/afficherSortie.html.twig",
            compact('sortie'));
    }
    /**
     * @Route("/api/listParticipants/{id}/", name="api_listParticipants")
     */
    public function apiListParticipants(Sortie $sortie){
        $listParticipants=$sortie->getParticipants();
        $tableau=[];
        foreach ($listParticipants as $participant){
            $tab['id']=$participant->getId();
            $tab['pseudo']=$participant->getPseudo();
            $tab['prenom']=$participant->getPrenom();
            $tab['nom']=$participant->getNom();
            $tab['telephone']=$participant->getTelephone();
            $tab['mail']=$participant->getEmail();
            $tab['campus']=$participant->getCampus()->getNom();
            $tab['photo']=$participant->getImage();
            $tableau[]=$tab;
        }
        return $this->json($tableau);
    }
    /**
     * @Route("/ajouterLieu/",name="ajouter_lieu")
     */
    public function ajouterLieu(EntityManagerInterface $em,VilleRepository $villeRepository,Request $request){

        $lieu = new Lieu();
        $formulaireLieu = $this->createForm(LieuFormType::class,$lieu);
        $formulaireLieu->handleRequest($request);

            $em->persist($lieu);
            $em->flush();

        return $this->redirectToRoute('sortie_creerSortie');

    }
    /**
     * @Route("/ajouterVille/",name="ajouter_ville")
     */
    public function ajouterVille(EntityManagerInterface $em,VilleRepository $villeRepository,Request $request){

        $ville = new Ville();
        $formulaireVille = $this->createForm(VilleFormType::class,$ville);
        $formulaireVille->handleRequest($request);

        $em->persist($ville);
        $em->flush();

        return $this->redirectToRoute('sortie_creerSortie',['formulaireVille' => $formulaireVille->createView()]);

    }


}