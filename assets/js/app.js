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

// script d'effacement d'images dans le formulaire add images du template admin/trick/_editform
window.onload = () => {
    //Gestion des boutons "supprimmer"
    let links = document.querySelectorAll("[data-delete]")

    // Boucle sur les liens
    for(link of links) {
        //Ecoute du click
        link.addEventListener("click", function(e){
            e.preventDefault()

            //Confirmation de suppression
            if(confirm('Voulez-vous vraiment supprimer cette image ?')){
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
                        this.parentNode.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}

// Upload picture in the edit trick template
$(function () {
    $(".btn_edit_picture").on('click', function (e) {
        e.preventDefault();
        $(this).hide('slow');
        $(this).parent().prev().show('slow');
        $(this).prev().val('');
    })
})

$(function () {
    selector = document.getElementsByName('uploadPictureFile');
    $(selector).change(function() { 
        var path = $(this).next().attr("href");
        var image = $(this);
        console.log('image ancienne : ', image);
        element = $(selector);
        var file = $(this)[0].files[0];
        var formData = new FormData();
        formData.append("file", file);

        $.ajax({
            type: "POST",
            url: path,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            async: false,
            dataType: "json",
            error: function (err) {
                console.error(err);
            },
            success: function (data) {
                console.log('nouvelle image : ', data);
            },
            complete: function () {
                console.log("Request finished.");
            }
        });
    });
});
