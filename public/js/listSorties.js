let tableau = [];

function afficherTab(tableau){
    //Selecteur sur le template et sur le tableau
    let tbody=document.querySelector("#myTbody");
    let template=document.querySelector("#ligne");
    let urlSinscrire="../../sortie/sinscrire/";
    let urlSeDesister="../../sortie/seDesister/";
    let urlModifierSortie="../../sortie/modifierSortie/";
    let urlAfficher="../../sortie/afficherSortie/";
    tbody.innerHTML='';
    for (let sortie of tableau){
        //J'ajoute l'id de ma sortie à l'url de sinscrire
        let urlsinscrire2=urlSinscrire+sortie.id;
        let urlSeDesister2=urlSeDesister+sortie.id;
        let urlModifierSortie2=urlModifierSortie+sortie.id;
        let urlAfficher2=urlAfficher+sortie.id;

        //Je clone le contenu du template dans une variable
        let clone=template.content.cloneNode(true);

        //Je mets un selecteur à l'interieur de la partie html clonée
        let tabTd=clone.querySelectorAll("td");// j'ai un tableau


        tabTd[0].innerHTML=sortie.nom ;
        tabTd[1].innerHTML=new Date(sortie.dateHeureDebut).toLocaleString('fr-FR');
        tabTd[2].innerHTML=new Date(sortie.dateLimiteInscription).toLocaleDateString('fr-FR');
        tabTd[3].innerHTML=sortie.nb+"/"+sortie.nbInscriptionMax ;
        tabTd[4].innerHTML=sortie.etat;
        tabTd[6].innerHTML= sortie.organisateur;
        tabTd[7].querySelector("#btnSinscrire").setAttribute("href",urlsinscrire2);
        tabTd[7].querySelector("#btnModifierSortie").setAttribute("href",urlModifierSortie2);
        tabTd[7].querySelector("#btnAfficher").setAttribute("href",urlAfficher2);
        tabTd[7].querySelector("#btnSedesister").setAttribute("href",urlSeDesister2);



        //----------------------------------------------------------------------------------------------------------------
        //Si la sortie est Cloturée alors le bouton s'inscrire est caché à l'utilisateur
        if (sortie.estCloturee == true){
            tabTd[7].querySelector("#btnSinscrire").setAttribute("hidden",'');
        }

        //----------------------------------------------------------------------------------------------------------------
        //Si l'utilisateur est déjà inscrit alors nous lui cachons le bouton s'inscrire
        if(sortie.estInscrit == true){
            tabTd[7].querySelector("#btnSinscrire").setAttribute("hidden",'');
        }

        //----------------------------------------------------------------------------------------------------------------
        //Si l'utilisateur est l'organistauer ou est administrateur alors il peut modifier la sortie via le bouton modifier
        if((sortie.estOrganisateur == true)||(sortie.estAdmin == true)){
            tabTd[7].querySelector("#btnModifierSortie").setAttribute('class' ,'btn btn-light');
        }

        //----------------------------------------------------------------------------------------------------------------
        //Si la sortie est archivée, alors, les détails de la sortie de sont plus consultables par les utilisateurs
        if(sortie.estArchivee == true){
            tabTd[7].querySelector("#btnAfficher").setAttribute("hidden",'');
        }

        //----------------------------------------------------------------------------------------------------------------
        //Si la sortie est archivée alors le bouton s'inscrire est caché
        if(sortie.estArchivee == true){
            tabTd[7].querySelector("#btnSinscrire").setAttribute("hidden",'');
        }

        //----------------------------------------------------------------------------------------------------------------
        //Si l'utilisateur n'est pas inscrit alors nous cachons le bouton Se Désister
        if(sortie.estInscrit == false){
            tabTd[7].querySelector("#btnSedesister").setAttribute("hidden",'');
        }

        //----------------------------------------------------------------------------------------------------------------
        //Si la sortie est passée alors nous cachons le bouton s'inscire à l'utilisateur
        if(sortie.estPassee == true){
            tabTd[7].querySelector("#btnSinscrire").setAttribute("hidden",'');
        }

        //----------------------------------------------------------------------------------------------------------------
        //Si la sortie est annulée alors nous cachons le boutons s'inscrire à l'utilisateur
        if(sortie.estAnnulee == true){
            tabTd[7].querySelector("#btnSinscrire").setAttribute("hidden",'');
        }

        //----------------------------------------------------------------------------------------------------------------
        //Si la sortie est à l'état créé alors nous cachons le bouton s'inscrire à l'utilisateur
        if(sortie.estCree == true){
            tabTd[7].querySelector("#btnSinscrire").setAttribute("hidden",'');
        }

        //----------------------------------------------------------------------------------------------------------------
        //Si l'utilisateur n'est pas inscrit alors nous laissons le champ d'indication Inscrit vide
        if (sortie.estInscrit == false){
            tabTd[5].querySelector('i').setAttribute('hidden','');
        }

        //----------------------------------------------------------------------------------------------------------------
        tbody.appendChild(clone);
    }
}


    let url = '../../sortie/api/listSorties/';
    //ce qui permet dexploiter les données de notre json dans le controller

    fetch(url)
        .then(response=>response.json())
        .then(tab=>
            {
                tableau = tab;
                afficherTab(tableau);
                console.log(tableau);
                for (let s of tab){
                    s.dateHeureDebut2 = new Date(s.heureComparaison);
                }
            }
        );

    //----------------------------------------------------------------------------------------------------------------
    function filtrer(){
        let tableau2 = tableau;
        tableau2 = filtrerNom(tableau2);
        tableau2 = filtrerCampus(tableau2);

        let userOrganisateur = document.querySelector('#estOrga').checked;
        if (userOrganisateur){
            tableau2 = filtrerOrganisteur(tableau2);
        }

        let estInscrit = document.querySelector('#estInscrit').checked;
        if(estInscrit){
            tableau2 = filtrerInscrit(tableau2);
        }

        let estNInscrit = document.querySelector('#estNInscrit').checked;
        if (estNInscrit){
            tableau2 = filtrerNonInscrit(tableau2);
        }

        let datePassee = document.querySelector('#datePassee').checked;
        if(datePassee){
            tableau2 = filtrerDatePassee(tableau2);
        }

        let dateDebut = document.querySelector('#datePremiere').value;
        tableau2 = filtrerPremiereDate(tableau2, dateDebut);

        let dateFin = document.querySelector('#dateSeconde').value;
        tableau2 = filtrerSecondeDate(tableau2, dateFin);


        afficherTab(tableau2);
    }

    //-----------------------------------------------------------------------
    function filtrerNom(tab){
        let tableau2 = [];
        let nom = document.querySelector('#nom').value;
        if (nom.length > 0){
            for ( let s of tab){
                if ( s.nom.indexOf(nom) != -1 ){
                    tableau2.push(s);
                }
            }
        }else{
            tableau2 =tab; // je filtre pas
        }
       return tableau2;
    }

    //-----------------------------------------------------------------------
    function filtrerCampus(tab){
        let tableau2 = [];
        let campus = document.querySelector('#campus').value;
        console.log(campus);

        if (campus != 0){
            for (let s of tab){

                if(s.campus == campus){

                    tableau2.push(s);
                }
            }
        } else {
            tableau2 = tab;
        }
        return tableau2;
    }

    //-----------------------------------------------------------------------
    function filtrerOrganisteur(tab){
        let tableau2 = [];
        for (s of tab){
            if ( s.estOrganisateur === true){
                tableau2.push(s);
            }
        }
        return tableau2;
    }

    //-----------------------------------------------------------------------
    function filtrerInscrit(tab){
        let tableau2 = [];
        for (s of tab){
            if(s.estInscrit === true){
                tableau2.push(s);
            }
        }
        return tableau2;
    }

    //-----------------------------------------------------------------------
    function filtrerNonInscrit(tab){
        let tableau2 = [];
        for (s of tab){
            if(s.estInscrit === false){
                tableau2.push(s);
            }
        }
        return tableau2;
    }

    //-----------------------------------------------------------------------
    function filtrerDatePassee(tab){
        let tableau2 = [];
        let dateJour = new Date();
        for (s of tab){
            if (s.dateHeureDebut2 < dateJour){
                tableau2.push(s);
            }
        }
        return tableau2;
    }

    //-----------------------------------------------------------------------
    function filtrerPremiereDate(tab, dateDebut) {
    let tab2 = [];

    if (dateDebut.length >0) {
        dateDebut = new Date(dateDebut);
        for (let s of tab)
        {
            if (s.dateHeureDebut2 >= dateDebut) {
                tab2.push(s);
            }
        }
    }else {
        tab2 = tab;
    }
    return tab2;
}

    //----------------------------------------------------------------------------------------------------------------
    function filtrerSecondeDate(tab, dateFin) {
    let tab2 = [];
    if (dateFin.length >0) {
        dateFin = new Date(dateFin);
        for (let s of tab)
        {
            if (s.dateHeureDebut2 <= dateFin) {
                tab2.push(s);
            }
        }
    }else {
        tab2 = tab;
    }
    return tab2;
}
