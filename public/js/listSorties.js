
function afficherTab(tableau){
//selecteur sur le template et sur le tableau
    let tbody=document.querySelector("#myTbody");
    let template=document.querySelector("#ligne");
    for (let sortie of tableau){
        //je clone le contenu du template dans une variable
        let clone=template.content.cloneNode(true);
        //je mets un selecteur à l'interieur de la partie html clonée
        let tabTd=clone.querySelectorAll("td");// j'ai un tableau
        tabTd[0].innerHTML=sortie.nom ;
        tabTd[1].innerHTML=sortie.dateHeureDebut;
        tabTd[2].innerHTML=sortie.dateLimiteInscription;
        tabTd[3].innerHTML=sortie.nbInscriptionMax ;
        tabTd[4].innerHTML=sortie.etat;
        tabTd[5].innerHTML= sortie.organisateur;
        //tabTd['participants']=sortie.participants;
        tbody.appendChild(clone);
    }

}
let url = 'http://127.0.0.1:8000/sortie/api/listSorties/';
    fetch(url)
        .then(response=>response.json())
        .then(tableau=>
            {
                afficherTab(tableau);
            }
        );