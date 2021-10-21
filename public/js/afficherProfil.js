function afficherProfil(btn){
    let id = btn.getAttribute('data-id');
    console.log(id);
    let affichage = document.querySelector('#profilAfficher');
    let urlAfficherProfil = "../../profil/afficherUnProfil/";
    for (let p of tabParticipant){
        console.log(p);
        if (p.id == id){
            document.querySelector('#prenom').innerHTML = p.prenom;
            document.querySelector('#nom').innerHTML = p.nom;
            document.querySelector('#telephone').innerHTML = p.telephone;
            document.querySelector('#email').innerHTML = p.mail;
            document.querySelector('#campus').innerHTML = p.campus;
        }
    }

    let urlAfficherProfil2 = urlAfficherProfil + participant.id;


}