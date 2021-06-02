//une fois que la page est chargée on appel la fonction init
window.onload = init;

function init(){
    let select = document.getElementById('sortie_lieu');
    let genererButton = document.getElementById('generer_adresse')
    let lieu_field = select.options[select.selectedIndex].text;

    genererButton.addEventListener('click',function (){
        //pour actualiser l'élement du select
        lieu_field = select.options[select.selectedIndex].text;
        //On prepare un objet qui porte les infos
        let data = {'lieu' : lieu_field};

        fetch("ajax-site", {method: 'POST', body: JSON.stringify(data)})
            //promesse : le contenu du data dans le dernier then
            .then(function (response){
                return response.json();
            }).then(function (data) {
            document.getElementById('rue').innerHTML = "Adresse : " + data.rue;
            document.getElementById('latitude').innerHTML = "Latitude : " + data.latitude;
            document.getElementById('longitude').innerHTML = "Longitude : " + data.longitude;
            document.getElementById('code_postal').innerHTML = "Code postal : " + data.code_postal;
        });
    })
}