//une fois que la page est chargée on appel la fonction init
window.onload = init;

function init(){
    let select = document.getElementById('sortie_lieu');
    let generer = document.getElementById('generer_adresse')
    let lieu_field = select.options[select.selectedIndex].text;

    generer.addEventListener('click',function (){
        //On prepare un bojet qui porte les infos
        let data = {'lieu' : lieu_field};
        //pour actualiser l'élement du select
        lieu_field = select.options[select.selectedIndex].text;
        fetch("ajax-site", {method: 'POST', body: JSON.stringify(data)})
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