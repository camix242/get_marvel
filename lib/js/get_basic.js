(function ($) {
  Drupal.behaviors.top_basic = {
    attach: function (context, settings) {
      $(document).ready(function() {
        if ($('.views-infinite-scroll-content-wrapper').length != 0) {
          $('.views-infinite-scroll-content-wrapper').addClass('row gx-0 justify-content col-12 col-md-10 offset-md-1');
        }
      });
    }
  };
})(jQuery);


