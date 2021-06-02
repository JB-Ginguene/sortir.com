//une fois que la page est chargée on appel la fonction init
window.onload = init;

function init() {

    let route = window.location.protocol + "//" + window.location.host + window.location.pathname;
    console.log('route : ' + route);


    // CREATION SORTIE
    if (window.location.href === route + 'sortie/create#') {
        let select = document.getElementById('sortie_lieu');
        let genererButton = document.getElementById('generer_adresse')
        let lieu_field = select.options[select.selectedIndex].text;

        genererButton.addEventListener('click', function () {
            //pour actualiser l'élement du select
            lieu_field = select.options[select.selectedIndex].text;
            //On prepare un objet qui porte les infos
            let data = {'lieu': lieu_field};

            fetch("ajax-site", {method: 'POST', body: JSON.stringify(data)})
                //promesse : le contenu du data dans le dernier then
                .then(function (response) {
                    return response.json();
                }).then(function (data) {
                document.getElementById('rue').innerHTML = "Adresse : " + data.rue;
                document.getElementById('latitude').innerHTML = "Latitude : " + data.latitude;
                document.getElementById('longitude').innerHTML = "Longitude : " + data.longitude;
                document.getElementById('code_postal').innerHTML = "Code postal : " + data.code_postal;
                document.getElementById('ville').innerHTML = "Ville : " + data.ville;
            });
        })
    }

// INSCRIPTION SORTIE (page d'accueil)
    if (window.location.href === route) {
        let inscriptionButtons = Array.from(document.getElementsByClassName('sortie_inscription'));
        console.log(inscriptionButtons);

        inscriptionButtons.forEach(function (elem, idx) {
            elem.addEventListener('click', function () {
                let data = {'sortieid': elem.dataset.sortieid, 'userid': elem.dataset.userid};
                console.log(data);
                console.log(data.sortieid)
                console.log(data.userid)

                fetch("ajax-sortie-inscription", {method: 'POST', body: JSON.stringify(data)})
                    //promesse : le contenu du data dans le dernier then
                    .then(function (response) {
                        return response.json();
                    }).then(function (data) {
                    console.log("t'y es  presque !!!")
                    console.log(elem);

                    let td = document.createElement('td');
                    let a = document.createElement('a');
                    a.classList.add("bg-success");
                    a.classList.add("btn");
                    a.classList.add("btn-sm");
                    a.setAttribute("role", "button");
                    a.innerHTML = "Inscrit·e";
                    td.append(a);
                    let parent = elem.parentNode;
                    parent.parentNode.replaceChild(td, parent);
                    });
            });
        });
    }
}

