<?php

namespace App\services;

use App\Entity\Sortie;

class Services
{
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


}