$(document).ready(function() {
  var actionMaxHeight = 0;
  $(".mp-actions li").each(function() {
    var thisHeight = parseInt($(this).height());
    if(thisHeight > actionMaxHeight) actionMaxHeight = thisHeight;
  }).each(function() {
    $(this).css({height: actionMaxHeight});
  });
  $(".mp-collections").jcarousel();

  if($.browser.msie && $.browser.version < 9) {
    $(".mp-actions li").prepend('<div class="ie-before"></div>').append('<div class="ie-after"></div>');
  }
});
