let tabParticipant;

function afficherParticipants(tableau){
    let tbody=document.querySelector("#tbodyParticipant");
    let template=document.querySelector("#ligneP");

    for (participant of tableau){

        let clone=template.content.cloneNode(true);
        let tabTd=clone.querySelectorAll("td");
        tabTd[0].innerHTML=participant.pseudo;
        tabTd[1].querySelector("button").innerHTML = participant.prenom +' ' +participant.nom;
        tabTd[1].querySelector("button").setAttribute('data-id', participant.id);
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
        tabParticipant = tableau;
            afficherParticipants(tableau);
        }
    )