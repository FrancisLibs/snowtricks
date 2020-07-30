/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
require("../css/app.css");

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

//console.log("Hello Webpack Encore! Edit me in assets/js/app.js");

// Gestion du bouton de l'affichage des médias
$(function () {
    var bouton = $("#btn-affichage-media");
    bouton.click(function (e) {
        bouton.hide();
        $("#div-affichage-photos-videos").show();
    });
});

// Affichage formulaire changement image à la une page show et edit
$("#btn_edit_main_picture").click(function () {
    $("#main_trick_name").fadeOut();
    $("#main_picture_upload_form").delay("slow").show("slow");
});

// Effacement d'un trick page edit
$(function () {
    $("#delete-trick").on("click", function (e) { 
        e.preventDefault();

        var path = $(this).parent().parent().find(".tricks_delete_form").attr("action");
        var token = $(this).prev().val();
        if (confirm("Etes-vous certain de vouloir supprimer cette image ?")) {
           var jsonToken = {"token": token};
           jsonToken = JSON.stringify(jsonToken);


            $.ajax({
                type: "DELETE",
                url: path,
                data: jsonToken,
                cache: false,
                contentType: false,
                processData: false,
                async: false,
                dataType: "json",
                error: function (erreur) {},
                success: function (data) {
                    if (data["success"]) 
                    {
                        $("#BlocTrickToDelete").remove();
                    }
                },
                complete: function () {}
            });
        }
    });
});

// Upload picture in the edit trick template
var champInput;
$(function () {
    $("#delegation").on("click", ("#btn_edit_picture"), function (e) {
        e.preventDefault();


        var btnEditPicture = $(this).hide("slow");
        btnEditPicture.next().hide("slow");
        var form = $(this).next().next();
        form.show("slow");
    });
});


$(function () {
    var selector = document.getElementsByName("uploadPictureFile");
    $("#delegation").on("change", ("[name='uploadPictureFile']"), function () {
        var path = $(this).next().attr("data-path");
        var ancienneImage = $(this).parent().parent().parent();
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
            dataType: "html",
            error: function (erreur) {},
            success: function (data) {
                ancienneImage.html(data);
            },
            complete: function () {}
        });
    });
});

// script d'effacement d'images dans admin/trick/edit.html.twig
$(function () {
    $("#delegation").on("click", ("[delete-picture]"), function (e) {
        e.preventDefault();
        if (confirm("Etes-vous certain de vouloir supprimer cette image ?")) {
            var path = $(this).attr("href");
            var image = $(this).parent().parent();
            var token = $(this).attr("data-token");
            var jsonToken = {"_token": token};
            jsonToken = JSON.stringify(jsonToken);

            $.ajax({
                type: "DELETE",
                url: path,
                data: jsonToken,
                cache: false,
                contentType: false,
                processData: false,
                async: false,
                dataType: "json",
                error: function (erreur) {},
                success: function (data) {
                    image.remove();
                },
                complete: function () {}
            });
        }
    });
});

// Remplacement d'une vidéo dans la page edit
$(function () {
    $("#delegation").on("click", ".btn-edit-video", function (e) {
        e.preventDefault();

        $(this).hide("slow");
        $(this).next().hide("slow");
        var form = $(this).parent().find(".upload-video-form");
        $("input[name=uploadVideoName]").val("");
        form.show("slow");
    });
});

$(function () {

    $("#delegation").on("submit", "form.upload-video-form", function (e) {
        e.preventDefault();

        var path = $(this).attr("controller-path");
        var link = $(this).find("input").val();
        var lien = {
            "link": link
        };
        lien = JSON.stringify(lien);
        var blocARemplacer = $(this).parent().parent();

        $.ajax({
            type: "POST",
            url: path,
            data: lien,
            cache: false,
            contentType: false,
            processData: false,
            async: false,
            dataType: "html",
            error: function (erreur) {},
            success: function (data) {
                blocARemplacer.html(data);
            },
            complete: function () {}
        });
    });
});

// Effacement d'une vidéo dans admin/trick/edit.html.twig
window.onload = () => {
    //Gestion des boutons "supprimmer"
    let links = document.querySelectorAll("[data-delete-video]");

    // Boucle sur les liens
    for (let link of links) {
        //Ecoute du click
        link.addEventListener("click", function (e) {
            e.preventDefault();

            //Confirmation de suppression
            if (confirm("Voulez-vous vraiment supprimer cette vidéo ?")) {
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        "_token": this.dataset.token
                    })
                }).then(
                    // récup réponse en json
                    response => response.json()
                ).then(data => {
                    if (data.success){
                        this.parentNode.parentNode.remove();
                    }
                    else {
                        alert(data.error);
                    }
                }).catch(e => alert(e));
            }
        });
    }
};

// Remplacement photo de l'utilisateur page profil
$(function () {
    $("#replace-user-picture").on("click", function (e) {
        e.preventDefault();
        var formulaire = $("#user-picture-form");
        var path = $("#path-Controller-user-picture").attr("data-path");
        formulaire.show("slow");
        champInput = $("#user_picture_input");

        champInput.on("change", function () {
            var file = $(this)[0].files[0];
            var oldPicture = $("#user-picture-bloc-to-replace");
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
                dataType: "html",
                error: function (erreur) {},
                success: function (data) {
                    oldPicture.html(data);
                },
                complete: function () {}
            });
        });
    });
});
