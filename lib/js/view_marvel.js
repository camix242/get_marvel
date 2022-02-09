(function ($) {
  Drupal.behaviors.get_marvel = {
    attach: function (context, settings) {

      $(document).ready(function() {



      });

      const url = drupalSettings.url_marvel;

      $('.get-entity-collection', context).click(function (e) {
        e.preventDefault();

        var entity = $(this).attr('data-entity');
        $('#identity').val(entity);

        $('.nav-item .btn.btn-primary').addClass('btn-secondary').removeClass('btn-primary');
        $(this).addClass('btn-primary').removeClass('btn-secondary');
        $('#overlay').removeClass('d-none');
        
        $.get( url.replace('@entity', entity), function( data ) {
          // console.debug (data);
          setCards(data, false);
        }, 'json' );

      });

      $('.add-more', context).click(function (e) {
        e.preventDefault();

        var entity = $('#identity').val();
        var offset = $('#offset').val();

        $('#overlay').removeClass('d-none');
        
        $.get( url.replace('@entity', entity) + '&offset=' + offset, function( data ) {
          setCards(data, true);
          $('#offset').val(data.data.offset + data.data.limit);
        }, 'json' );

      });

      $(document, context).once('get-marvel').on('click', '.set-fav',function (e) {
        e.preventDefault();
        $('#overlay').removeClass('d-none');
        
        var id = $(this).attr('data-id');
        // console.debug(id); 
        var entity = $('#identity').val();
        
        var url_fav = "/ajax-fav-callback/"+entity+"/"+id;
        
        $.get( url_fav, function( data ) {
          
          var myModal = new bootstrap.Modal(document.getElementById('myModal'), {
          })
          myModal.show()

          $('#myModal .modal-body').html(data.message);
          
        }, 'json' )  .done(function() {
          $('#overlay').addClass('d-none');

        });

      });

      function setCards(data , add = false) {
       
        var html = "";

        data.data.results.forEach(function(index) {

          var title;
          if (index.title !== undefined) {
            title = index.title;
          } else {
            title = index.name;
          }

          html += "<div class='col-12 col-lg-4' >";
          html += " <div class='card text-white bg-primary mb-3'>";
          html += "  <div class='card-header'>" + title + " (ID:" + index.id + ")</div>";
          html += "  <div class='card-body'>";
          html += "      <div class='col-12 card-fav d-flex justify-content-center'>";
          html += "          <button type='button' class='btn btn-outline-light set-fav' data-id='" + index.id + "' >"; 
          html += "          + Favoritos </button>";
          html += "      </div>";
          html += "      <div class='col-12 px-3 card-img'>";
          html += "        <img class='img-fluid' loading='lazy' alt ='thumbnail-" + index.id + "'src='" + index.thumbnail.path + "." + index.thumbnail.extension + "'>";
          html += "      </div>";
          html += "      <p class='card-text'>" + index.description + "</p>";
          html += "    </div>";
          html += "  </div>";
          html += "</div>";

        }, this);
        // console.debug(html);
       
        if (add) {
          $('.content-marvel').append(html);
        } else {
          $('.content-marvel').html(html);
        }

        $('#overlay').addClass('d-none');


      }
  
    }
  };
})(jQuery);


