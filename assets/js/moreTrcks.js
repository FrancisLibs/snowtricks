<script>
    $(function() {
            //$('#btnPlus').click(function(e) {
                //e.preventDefault();
                var nbTricks = $('#tricksDisplay').children().length;
                var path = $("#linkToController").attr("data-path");
                $.ajax({
        type: 'GET',    //envoie de la requête
                    url: path,
                    data : 'nbTricks=' + nbTricks,
                    timeout: 3000,
                    dataType: 'json',

                    // réception du retour
                    success: function(data) {
                        var section = $('#tricksDisplay:first-child');
                        jQuery.each(data.tricks, function(index, value) {
                            var id =(data.tricks[index].id);
                            var name = (data.tricks[index].name);
                            var slug = (data.tricks[index].slug);
                            var mainPicture = (data.tricks[index].mainPicture);

                            var html ='';
                            html += '<div class="card m-4 text-white bg-secondary mb-3">';
                            html += '<img class="card-img-top" src="/'+ mainPicture + '" alt="">';
                            html += '<div class="card-body d-flex justify-content-between">';
                            html += '<div class="card-title">';
                            html += '<a href="/trick/' + slug + '-' + id + " /" + nbTricks + '">' + name + '</a>';
                            html += '</div>';
                            html += '<div class="liens">';
                            html += '</div></div></div>';
                            $('#tricksDisplay').append(html);
                        });
                        var displayBtn = data.displayBtn;

                        // Traitement affichage du bouton "plus"
                        if(!displayBtn) {
        $('#btnPlus').hide();
                        }
                    },
                    error: function() {
        alert('La requête n\'a pas abouti');
                    }
                });
            });
        });
    </script>