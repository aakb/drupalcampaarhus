(function($) {
  function initInstafeed(tag, id) {
    console.log(tag);
    console.log(id);
    var clientId = '0';
    var enableCaption = false;
    var limit = 6;
    var resolution = 'low_resolution';
    var sortBy = 'most-recent';

    var feed = new Instafeed({
      "get": 'tagged',
      "tagName": tag,
      "target": id,
      "clientId": clientId,
      "enableCaption": enableCaption,
      "limit": limit,
      "resolution": resolution,
      "sortBy": sortBy,
      "template":
        '<figure class="instagram-gallery--figure">' +
          '<a href="{{link}}" title="{{caption}}" class="instagram-gallery--link">' +
            '<img src="{{image}}" class="instagram-gallery--image">' +
          '</a>' +
          (enableCaption ? '<figcaption class="instagram-gallery--caption">{{caption}}</figcaption>' : '') +
        '</figure>'
    });
    feed.run();
  }

  $('.instagram-gallery--wrapper').each(function() {
    initInstafeed((this.id.split('instafeed--'))[1], this.id);
  });
}(jQuery));
