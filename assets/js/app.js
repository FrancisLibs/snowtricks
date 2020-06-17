/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

// script d'effacement d'images dans le formulaire add images
window.onload = () => {
    //Gestion des boutons "supprimmer"
    let links = document.querySelectorAll("[data-delete]")

    // Boucle sur les liens
    for(link of links) {
        //Ecoute du click
        link.addEventListener("click", function(e){
            e.preventDefault()

            //Confirmation de suppression
            if(confirm('Voulez-vous supprimer cette image, si oui, elle le sera, sans validation !')){
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    // récup réponse en json
                    response => response.json()
                ).then(data => {
                    if(data.success)
                        this.parentNode.parentNode.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}