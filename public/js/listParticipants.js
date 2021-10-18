function afficherParticipants(tableau){
    let tbody=document.querySelector("#tbodyParticipant");
    let template=document.querySelector("#ligneP");
    let urlAfficherUnProfil="../../profil/afficherUnProfil/"
    for (participant of tableau){
        let urlAfficherUnProfil2=urlAfficherUnProfil +participant.id;
        let clone=template.content.cloneNode(true);
        let tabTd=clone.querySelectorAll("td");
        tabTd[0].innerHTML=participant.pseudo;
        //tabTd[1].innerHTML= <a href="../../profil/afficherUnProfil">participant.prenom+" "+participant.nom</a>;
       tabTd[1].querySelector(".btnAfficherPofil").setAttribute("href",urlAfficherUnProfil2);
        tabTd[1].querySelector(".btnAfficherPofil").innerHTML = participant.prenom +' ' +participant.nom;
        tbody.appendChild(clone);
    }
}
let idSortie=document.querySelector("#idSortie").value;

let url='../../sortie/api/listParticipants/';
let url2=url+idSortie+"/";
console.log(idSortie);
fetch(url2)
    .then(response=>response.json())
    .then(tableau=> {
            afficherParticipants(tableau);
        }
    )