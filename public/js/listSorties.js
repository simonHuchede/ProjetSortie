
function afficherTab(tableau){
//selecteur sur le template et sur le tableau
    let tbody=document.querySelector("#myTbody");
    let template=document.querySelector("#ligne");
    for (let sortie of tableau){
        //je clone le contenu du template dans une variable
        let clone=template.content.cloneNode(true);
        //je mets un selecteur à l'interieur de la partie html clonée
        let tabTd=clone.querySelectorAll('td');// j'ai un tableau
        tabTd['nom']=sortie.nom ;
        tabTd['dateHeureDebut']=sortie.dateHeureDebut;
        tabTd['dateLimiteInscription']=sortie.dateLimiteInscription;
        tabTd['nbInscriptionMax']=sortie.nbInscriptionMax ;
        tabTd['etat']=sortie.etat;
        tabTd['organisateur']= sortie.organisateur;
        //tabTd['participants']=sortie.participants;

    }

}
let url = "http://127.0.0.1:8000/sortie/api/listSortie/"
    fetch(url)
        .then(response=>response.json())
        .then(tableau=>
            {
                afficherTab(tableau);
            }
        );