<?php

namespace App\services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class Services
{
    protected $sortieRepository;
    protected $em;
    protected $etatRepo;
    public function __construct(SortieRepository $sortieRepository,
                                EntityManagerInterface $em,
                                EtatRepository $etatRepo)
    {
        $this->sortieRepository=$sortieRepository;
        $this->em=$em;
        $this->etatRepo=$etatRepo;

    }

    public function verifEstOrganisateur(Sortie $sortie, $user){
    if($sortie->getOrganisateur()->getId() == $user->getId()){
        return true;
    }else{
        return false;
    }

}
public function verifEstInscrit( $sortie, $user){
    $test = false;
    $participants =$sortie->getParticipants();
    foreach ($participants as $participant){
        if($participant->getId()==$user->getId()){
            $test = true;
        }
    }
    return $test;
}
    public function clotureInscription (){
        $etat = $this->etatRepo->find(3);
        $sorties=$this->sortieRepository->findAll();
        foreach ($sorties as $sortie ){
            if ((new \DateTime('now')) >= $sortie->getDateLimiteInscription()&&(new \DateTime('now')) < $sortie->getDateHeureDebut()){
                $sortie->setEtat($etat);


            }
        }
        $this->em->flush();


    }

    public function verifEstCloture($sortie){
        $test = false;
        $etat = $sortie->getEtat();
        if ($etat == $this->etatRepo->find(3)){
            $test = true;
        }
        return $test;
    }
public function verifEstAdministrateur($user)
{
    $test=false;
    foreach ($user->getRoles() as $role)
   if($role =="ROLE_ADMIN"){
       $test=true;
   }
    return $test;

}

    public function archiver(){
        $etat = $this->etatRepo->find(7);
        $sorties=$this->sortieRepository->findAll();

        foreach ($sorties as $sortie ){
            if ((new \DateTime('now')) >= $sortie->getDateHeureDebut()->add(new \DateInterval('P30D'))){
                $sortie->setEtat($etat);


            }
        }

        $this->em->flush();
    }
    public function verifEstArchivee($sortie){
        $test = false;
        $etat = $sortie->getEtat();
        if ($etat == $this->etatRepo->find(7)){
            $test = true;
        }
        return $test;
    }
    public function estPassee(){
        $etat = $this->etatRepo->find(5);
        $sorties=$this->sortieRepository->findAll();


        foreach ($sorties as $sortie ){
            $duree=$sortie->getDuree();
            if ((new \DateTime('now')) >= $sortie->getDateHeureDebut()->add(new \DateInterval('PT' . $duree . 'M'))){
                $sortie->setEtat($etat);


            }
        }
    }
    public function estEnCours(){
        $etat = $this->etatRepo->find(4);
        $sorties=$this->sortieRepository->findAll();


        foreach ($sorties as $sortie ){
            $duree=$sortie->getDuree();
            if ((new \DateTime('now')) < $sortie->getDateHeureDebut()->add(new \DateInterval('PT' . $duree . 'M')) && (new \DateTime('now')) >= $sortie->getDateHeureDebut()){
                $sortie->setEtat($etat);


            }
        }
    }

}