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

// Gestion du bouton de l'affichage des médias
$(function () {
    var bouton = $('#btn-affichage-media');
    bouton.click(function (e) {
        bouton.hide();
        $("#div-affichage-photos-videos").show();
    })
})

// Affichage formulaire changement image à la une page show et edit
$('#btn_edit_main_picture').click(function () {
    $('#main_trick_name').fadeOut();
    $('#main_picture_upload_form').delay("slow").show('slow');
});

// Effacement d'un trick page edit
$(function () {
    $(".delete-trick").on('click', function (e) {
        e.preventDefault();
        if (confirm('Etes-vous certain de vouloir supprimer cette image ?')) {
            //var token = $( "input[name='_token']" ).val();
            //var path = $(".pathToControllerDelete").attr('data-path');
            // var formData = new FormData();
            formData.append("_token", token);

            $.ajax({
                type: "DELETE",
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
                    console.log('retour : ', data);
                },
                complete: function () {
                    console.log("Request finished.");
                }
            });
        }
    })
})

// Upload picture in the edit trick template
champInput = "variable globale";
btnDeletePicture = "variable globale";
btnEditPicture = $('.btn_edit_picture');
$(function () {
    $("#delegation").on('click', ('.btn_edit_picture'), function (e) {
        e.preventDefault();
        btnEditPicture = $(this).hide('slow');
        btnDeletePicture = btnEditPicture.next();
        btnDeletePicture = $(this).next().hide('slow');
        champInput = $(this).next().next();
        champInput.find('input').val("");
        champInput.show('slow');
    })
})

$(function () {
    var selector = document.getElementsByName('uploadPictureFile');
    $("#delegation").on('change', ("[name='uploadPictureFile']"), function () {
        var path = $(this).next();
        var ancienneImage = $(this).parent().parent().parent();
        var file = $(this)[0].files[0];
        var formData = new FormData();
        formData.append("file", file);

        $.ajax({
            type: "POST",
            url: path.attr('data-path'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            async: false,
            dataType: "html",
            error: function (err) {
                console.error(err);
            },
            success: function (data) {
                ancienneImage.html(data);
            },
            complete: function () {
                console.log("Request finished.");
            }
        });
    });
});

// script d'effacement d'images dans admin/trick/edit.html.twig
$(function () {
    $("#delegation").on('click', ("[delete-picture]"), function (e) {
        e.preventDefault();
        if (confirm('Etes-vous certain de vouloir supprimer cette image ?')) {
            var path = $(this).attr('href');
            var image = $(this).parent().parent();
            var token = $(this).attr('data-token');
            var jsonToken = {
                "_token": token
            };
            var jsonToken = JSON.stringify(jsonToken);

            $.ajax({
                type: "DELETE",
                url: path,
                data: jsonToken,
                cache: false,
                contentType: false,
                processData: false,
                async: false,
                dataType: "json",
                error: function (err) {
                    console.error(err);
                },
                success: function (data) {
                    image.remove();
                },
                complete: function () {
                    console.log("Request finished.");
                }
            });
        }
    });
});

// Upload video in the edit trick template
btnEditVideo = $('.btn_edit_video');
src = btnEditVideo.parent().parent().children();
$(function () {
    btnEditVideo.on('click', function (e) {
        e.preventDefault();
        btnDelete = btnEditVideo.next();
        btnEditVideo = $(this).hide('slow');
        btnDeleteVideo = $(this).next().hide('slow');
        champInput = $(this).next().next();
        champInput.find('input').val("");
        champInput.show('slow');
    })
})

$(function () {
    selector = document.getElementsByName('uploadVideoName');
    $(selector).change(function () {
        var link = $(this).val();
        var path = $(this).next().attr('data-path');
        var formData = new FormData();
        formData.append("link", link);

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
                champInput.hide();
                console.log(link);
                src.val(link);
                btnEditVideo.show();
                btnDeleteVideo.show();
            },
            complete: function () {
                console.log("Request finished.");
            }
        });
    });
});

// Effacement d'une vidéo dans admin/trick/edit.html.twig
window.onload = () => {
    //Gestion des boutons "supprimmer"
    let links = document.querySelectorAll("[data-delete-video]")

    // Boucle sur les liens
    for (link of links) {
        //Ecoute du click
        link.addEventListener("click", function (e) {
            e.preventDefault()

            //Confirmation de suppression
            if (confirm('Voulez-vous vraiment supprimer cette vidéo ?')) {
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
                    if (data.success)
                        this.parentNode.parentNode.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}

// Remplacement photo de l'utilisateur page profil
$(function () {
    $('#replace-user-picture').on('click', function (e) {
        e.preventDefault();
        var formulaire = $('#user-picture-form');
        var path = $('#path-Controller-user-picture').attr('data-path');
        formulaire.show('slow');
        champInput = $('#user_picture_input');

        champInput.on('change', function () {
            var file = $(this)[0].files[0];
            var oldPicture = $('#user-picture-bloc-to-replace');
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
                error: function (err) {
                    console.error(err);
                },
                success: function (data) {
                    console.log(data);
                    oldPicture.html(data);
                },
                complete: function () {
                    console.log("Request finished.");
                }
            });
        });
    });
});
