let lieux=[];

    //-------------------------------------------------------------------------//
    function changeVille() {
        let  villeId = document.querySelector('#ville').value;
        afficherLieux(villeId);
        }

    //-------------------------------------------------------------------------//
    function afficherLieux(villeId) {
    let selectLieux =document.querySelector('#lieu');
    selectLieux.innerHTML='';

    for (let lieu of lieux){
       if (villeId == lieu.ville) {
           let option = document.createElement('option');//<option></option>
           option.setAttribute('value',lieu.id);//<option value="id"></option>
           option.textContent = lieu.nom;////<option value="id">Nomdelaville</option>
           selectLieux.appendChild(option);
            }
        }
    }

    //-------------------------------------------------------------------------//
    function afficherVille(tab){
    for (v of tab) {
        let option = document.createElement('option');//<option></option>
        option.setAttribute('value',v.id);//<option value="id"></option>
        option.textContent = v.nom;////<option value="id">Nomdelaville</option>
        document.querySelector('#ville').appendChild(option);
        }
    }

    //--------------------------------------------------------------------------//

    let url='../../sortie/api/ville-lieu/';
    fetch(url)
        .then(response=>response.json())
        .then(
            object => {
                afficherVille(object.villes);
                let villeId = object.villes[0].id;
                lieux=object.lieux;
                afficherLieux(villeId);
        });
