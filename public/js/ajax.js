//une fois que la page est chargée on appel la fonction init
window.onload = init;

function inscriptionDesinscription($path) {
    if ($path === '') {
        let inscriptionButtons = Array.from(document.getElementsByClassName('sortie_inscription'));
        console.log("inscriptionButtons début : ");
        console.log(inscriptionButtons);

        let desinscriptionButtons = Array.from(document.getElementsByClassName('sortie_desinscription'));
        console.log("desinscriptionButtons début : ");
        console.log(desinscriptionButtons);
        // PAGE ACCUEIL INSCRIPTION
        inscriptionButtons.forEach(function (elem) {
            elem.addEventListener('click', function () {

                let data = {'sortieid': elem.dataset.sortieid, 'userid': elem.dataset.userid};

                fetch("ajax-sortie-inscription", {method: 'POST', body: JSON.stringify(data)})
                    //promesse : le contenu du data dans le dernier then
                    .then(function (response) {
                        return response.json();
                    }).then(function (data) {
                    ////////////
                    if (data.participant !== data.participantMax) {
                        let td = document.createElement('td');
                        let a = document.createElement('a');
                        a.classList.add("bg-success");
                        a.classList.add("btn");
                        a.classList.add("btn-sm");
                        a.classList.add("sortie_desinscription");
                        a.setAttribute("role", "button");
                        a.setAttribute("data-sortieId", data.sortieid);
                        a.setAttribute("data-userId", data.userid);
                        a.innerHTML = "Inscrit·e";
                        td.append(a);
                        let parent = elem.parentNode;
                        document.getElementById('nbreParticipant' + data.sortieid).innerHTML = data.participant + "/" + data.participantMax;
                        if (parent.parentNode) {
                            parent.parentNode.replaceChild(td, parent);
                        }
                    } else if (data.participant === data.participantMax) {
                        let td = document.createElement('td');
                        // BOUTON 1 : COMPLET
                        let a1 = document.createElement('a');
                        a1.classList.add("bg-danger");
                        a1.classList.add("btn");
                        a1.classList.add("btn-sm");
                        a1.classList.add("mb-1");
                        a1.innerHTML = "Complet!";

                        // BOUTON 2 : DEJA INSCRIT :
                        let a2 = document.createElement('a');
                        a2.classList.add("bg-success");
                        a2.classList.add("btn");
                        a2.classList.add("btn-sm");
                        a2.classList.add("sortie_desinscription");
                        a2.setAttribute("role", "button");
                        a2.setAttribute("data-sortieId", data.sortieid);
                        a2.setAttribute("data-userId", data.userid);
                        a2.innerHTML = "Inscrit·e";

                        td.append(a1, a2);
                        let parent = elem.parentNode;
                        document.getElementById('nbreParticipant' + data.sortieid).innerHTML = data.participant + "/" + data.participantMax;
                        if (parent.parentNode) {
                            parent.parentNode.replaceChild(td, parent);
                        }
                    }

                    /////////////////
                    inscriptionDesinscription('');
                    ///////////
                });
            });
        })

        //DESINSCRIPTION :
        desinscriptionButtons.forEach(function (elem) {
            elem.addEventListener('click', function () {
                let data = {'sortieid': elem.dataset.sortieid, 'userid': elem.dataset.userid};

                fetch("ajax-sortie-desinscription", {method: 'POST', body: JSON.stringify(data)})
                    //promesse : le contenu du data dans le dernier then
                    .then(function (response) {
                        return response.json();
                    }).then(function (data) {

                    let td = document.createElement('td');
                    let a = document.createElement('a');
                    a.classList.add("bg-warning");
                    a.classList.add("btn");
                    a.classList.add("btn-sm");
                    a.classList.add("sortie_inscription");
                    a.setAttribute("role", "button");
                    a.setAttribute("data-sortieId", data.sortieid);
                    a.setAttribute("data-userId", data.userid);
                    a.innerHTML = "S'inscrire";
                    td.append(a);
                    let parent = elem.parentNode;
                    document.getElementById('nbreParticipant' + data.sortieid).innerHTML = data.participant + "/" + data.participantMax;
                    if (parent.parentNode) {
                        parent.parentNode.replaceChild(td, parent);
                    }
                    inscriptionDesinscription('');
                });
            });
        });
    }
    if ($path === 'sortie/detail') {
        let inscriptionButtons = document.getElementById('sortie_inscription');
        console.log("inscriptionButtons début : ");
        console.log(inscriptionButtons);

        let desinscriptionButtons = document.getElementById('sortie_desinscription');
        console.log("desinscriptionButtons début : ");
        console.log(desinscriptionButtons);
        // PAGE DETAIL INSCRIPTION
        if (inscriptionButtons) {
            inscriptionButtons.addEventListener('click', function () {
                let data = {
                    'sortieid': inscriptionButtons.dataset.sortieid,
                    'userid': inscriptionButtons.dataset.userid
                };
                console.log(data)
                fetch("inscription", {method: 'POST', body: JSON.stringify(data)})
                    //promesse : le contenu du data dans le dernier then
                    .then(function (response) {
                        return response.json();
                    }).then(function (data) {
                    // Si non-complet :
                    if (data.participant !== data.participantMax) {
                        let a = document.createElement('a');
                        a.classList.add("bg-success");
                        a.classList.add("btn");
                        a.classList.add("btn-lg");
                        a.setAttribute("id", "sortie_desinscription");
                        a.setAttribute("role", "button");
                        a.setAttribute("data-sortieId", data.sortieid);
                        a.setAttribute("data-userId", data.userid);
                        a.innerHTML = "Inscrit·e";
                        // création div :
                        let div = document.createElement('div');
                        div.classList.add("container");
                        div.classList.add("text-center");
                        div.classList.add("mb-2");
                        div.append(a);
                        let parent = inscriptionButtons.parentNode;
                        document.getElementById('nbreParticipant' + data.sortieid).innerHTML = data.participant + "/" + data.participantMax;
                        if (parent.parentNode) {
                            parent.parentNode.replaceChild(div, parent);
                        }
                    } else if (data.participant === data.participantMax) {
                        // BOUTON 1 : COMPLET
                        let a1 = document.createElement('a');
                        a1.classList.add("bg-danger");
                        a1.classList.add("btn");
                        a1.classList.add("btn-lg");
                        a1.innerHTML = "Complet!";
                        a1.setAttribute("id", "btn-complet");
                        let divComplet = document.createElement('div');
                        divComplet.classList.add("container");
                        divComplet.classList.add("text-center");
                        divComplet.classList.add("mb-2");
                        divComplet.append(a1);

                        // BOUTON 2 : DEJA INSCRIT :
                        let a2 = document.createElement('a');
                        a2.classList.add("bg-success");
                        a2.classList.add("btn");
                        a2.classList.add("btn-lg");
                        a2.setAttribute("id", "sortie_desinscription");
                        a2.setAttribute("role", "button");
                        a2.setAttribute("data-sortieId", data.sortieid);
                        a2.setAttribute("data-userId", data.userid);
                        a2.innerHTML = "Inscrit·e";
                        let divInscrit = document.createElement('div');
                        divInscrit.classList.add("container");
                        divInscrit.classList.add("text-center");
                        divInscrit.classList.add("mb-2");
                        divInscrit.append(a2);

                        //div contenant les div des boutons boutons :
                        let div = document.createElement('div');
                        div.classList.add("container");
                        div.classList.add("text-center");
                        div.classList.add("justify-content-between");
                        div.classList.add("d-flex");
                        div.append(divComplet, divInscrit);
                        let parent = inscriptionButtons.parentNode;
                        document.getElementById('nbreParticipant' + data.sortieid).innerHTML = data.participant + "/" + data.participantMax;
                        if (parent.parentNode) {
                            parent.parentNode.replaceChild(div, parent);
                        }
                    }

                    /////////////////
                    inscriptionDesinscription('sortie/detail');
                    ///////////
                });
            });
        }

        //DESINSCRIPTION :
        if (desinscriptionButtons) {
            desinscriptionButtons.addEventListener('click', function () {
                    let data = {
                        'sortieid': desinscriptionButtons.dataset.sortieid,
                        'userid': desinscriptionButtons.dataset.userid
                    };
                    fetch("desinscription", {
                        method: 'POST',
                        body: JSON.stringify(data)
                    })
                        //promesse : le contenu du data dans le dernier then
                        .then(function (response) {
                            return response.json();
                        }).then(function (data) {
                        // suppression du bouton complet si déja présent sur la page :
                        if (document.getElementById('btn-complet')) {
                            btnComplet = document.getElementById('btn-complet');
                            divBtnComplet = btnComplet.parentNode;
                            divBtnComplet.parentNode.removeChild(divBtnComplet);
                        }

                        // création bouton :
                        let a = document.createElement('a');
                        a.classList.add("bg-warning");
                        a.classList.add("btn");
                        a.classList.add("btn-lg");
                        a.setAttribute("id", "sortie_inscription");
                        a.setAttribute("role", "button");
                        a.setAttribute("data-sortieId", data.sortieid);
                        a.setAttribute("data-userId", data.userid);
                        a.innerHTML = "S'inscrire";
                        // création div :
                        let div = document.createElement('div');
                        div.classList.add("container");
                        div.classList.add("text-center");
                        div.classList.add("mb-2");
                        div.append(a);

                        let parent = desinscriptionButtons.parentNode;
                        document.getElementById('nbreParticipant' + data.sortieid).innerHTML = data.participant + "/" + data.participantMax;
                        if (parent.parentNode) {
                            parent.parentNode.replaceChild(div, parent);
                        }
                        inscriptionDesinscription('sortie/detail');
                    });
                }
            );

        }
    }
}

function profileManagement() {

    let deleteProfileButtons = Array.from(document.getElementsByClassName('profile_delete'));
    deleteProfileButtons.forEach(function (elem) {
        elem.addEventListener('click', function () {
            let confirmAction = confirm("Voulez-vous vraiment supprimer ce participant et toutes ses sorties organisées?");
            if (confirmAction) {
                alert("Participant supprimé avec succès");

            let data = {'participantId': elem.dataset.participantid};
            fetch("ajax-profile-delete", {method: 'POST', body: JSON.stringify(data)})
                .then(function (response) {
                    return response.json();
                }).then(function () {
                let row = elem.parentNode.parentNode;
                row.parentNode.removeChild(row);
                profileManagement();
            });} else {
                alert("Participant non supprimé");
            }
        });
    })

    // ACTIVATION / DESACTIVATION :
    // liste des boutons permettant d'activer un participant :
    let activerButtons = Array.from(document.getElementsByClassName('profile_change_activer'));
    // liste des boutons permettant de désactiver un participant :
    let desactiverButtons = Array.from(document.getElementsByClassName('profile_change_desactiver'));

    // on affiche les tableaux en console !
    console.log(activerButtons)
    console.log(desactiverButtons)


    // ON ACTIVE UN PARTICIPANT :
    activerButtons.forEach(function (elem) {
        elem.addEventListener('click', function () {
            let data = {'participantId': elem.dataset.participantid};
            fetch("ajax-profile-actif-change", {method: 'POST', body: JSON.stringify(data)})
                .then(function (response) {
                    return response.json();
                }).then(function (data) {
                // si l'utilisateur est désormais ACTIF, on affiche le boutton "DESACTIVER"
                // Bouton Désactiver
                let a = document.createElement('a');
                a.classList.add("bg-warning");
                a.classList.add("btn");
                a.classList.add("btn-sm");
                a.classList.add("mb-1");
                a.classList.add("text-white");
                a.classList.add("profile_change_desactiver");
                a.innerHTML = "Désactiver";
                a.setAttribute("data-participantid", data.participantId);
                console.log('actif : ');
                console.log(data.participantactif);
                console.log('UTILISATEUR ACTIF // elem :')
                console.log(elem)
                console.log("elem.parentNode : ")
                console.log(elem.parentNode)

                let td = document.getElementById('td'+data.participantId)
                
                if (elem.parentNode) {
                    td.removeChild(elem);
                    td.append(a)
                }
                profileManagement();
            });
        });
    })
    // ON DESACTIVE UN PARTICIPANT :
    desactiverButtons.forEach(function (tata) {
        tata.addEventListener('click', function () {
            let data = {'participantId': tata.dataset.participantid};
            fetch("ajax-profile-actif-change", {method: 'POST', body: JSON.stringify(data)})
                .then(function (response) {
                    return response.json();
                }).then(function (data) {
                // si l'utilisateur est désormais INNACTIF, on affiche le boutton "ACTIVER"
                // BOUTON ACTIVER
                let a = document.createElement('a');
                a.classList.add("bg-success");
                a.classList.add("btn");
                a.classList.add("btn-sm");
                a.classList.add("mb-1");
                a.classList.add("text-white");
                a.classList.add("profile_change_activer");
                a.innerHTML = "Activer";
                a.setAttribute("data-participantid", data.participantId);
                console.log('actif : ');
                console.log(data.participantactif);
                console.log('UTILISATEUR INNACTIF // elem :')
                console.log(tata)
                console.log("elem.parentNode : ")
                console.log(tata.parentNode)
                let td = document.getElementById('td'+data.participantId)

                if (tata.parentNode) {
                    td.removeChild(tata);
                    td.append(a)
                    profileManagement();
                }
            });
        });

    })
}

function init() {

    let url = window.location.href.split('sortir.com/public/')[1];

    // CREATION SORTIE
    if (url.includes('create')) {
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

    // EDITION SORTIE
    if (url.includes('edit')) {
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

    // DES/INSCRIPTION SORTIE (page d'accueil)
    if (url === '') {
        $path = '';
        inscriptionDesinscription($path);
    }

    // DES/INSCRIPTION SORTIE (page détail )
    if (url.includes('sortie/detail')) {
        $path = 'sortie/detail';
        inscriptionDesinscription($path);
    }

    //MANAGEMENT DES PARTICIPANTS :
    if (url.includes('profile/management')) {
        profileManagement();
    }
}

