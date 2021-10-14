

let url = "http://127.0.0.1:8000/sortie/api/listSortie/"
    fetch(url)
        .then(response=>response.json())
        .then(tableau=>
            {
                console.log(tableau)
            }
        );