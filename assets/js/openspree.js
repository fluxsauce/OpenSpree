/**
 * For the fuuuuuuutre...
 */
$(document).ready(function() {
  $('.board td').hover(
    function () {
      $(this).addClass('hover');
    },
    function () {
      $(this).removeClass('hover');
    }
  );
  $('.toggle_button.implementation').click(
    function() {
      $('.implementation.toggle_button').toggle();
      $('.implementation.content').slideToggle();
    }
  );
      
});