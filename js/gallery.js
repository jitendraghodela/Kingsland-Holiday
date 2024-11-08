jQuery(document).ready(function ($) {
  var frame;
  var $galleryRepeater = $("#gallery-repeater");

  // Add new gallery item
  $("#add-gallery-image-btn").on("click", function () {
    var index = $galleryRepeater.find(".gallery-item").length;
    var newItem =
      `
            <div class="gallery-item" style="margin-bottom: 10px;">
                <input type="hidden" name="gallery[` +
      index +
      `]" />
                <button type="button" class="upload-gallery-image-btn" data-target="gallery[` +
      index +
      `]">Upload Image</button>
                <button type="button" class="remove-gallery-image-btn">Remove</button>
            </div>
        `;
    $galleryRepeater.append(newItem);
  });

  // Upload image
  $galleryRepeater.on("click", ".upload-gallery-image-btn", function (event) {
    event.preventDefault();

    var $button = $(this);
    var target = $button.data("target");

    if (frame) {
      frame.open();
      return;
    }

    frame = wp.media({
      title: "Select or Upload Image",
      button: {
        text: "Use this image",
      },
      multiple: false,
    });

    frame.on("select", function () {
      var attachment = frame.state().get("selection").first().toJSON();
      $button.siblings('input[name="' + target + '"]').val(attachment.id);
      $button.siblings("img").remove();
      $button.before(
        '<img src="' +
          attachment.url +
          '" style="max-width: 100px; max-height: 100px; margin-right: 10px;" />'
      );
    });

    frame.open();
  });

  // Remove gallery item
  $galleryRepeater.on("click", ".remove-gallery-image-btn", function () {
    $(this).closest(".gallery-item").remove();
  });
});

//
jQuery(document).ready(function ($) {
  var $galleryImages = $(".gallery-image");
  var currentIndex = 0;

  function showImage(index) {
    $galleryImages.hide();
    $galleryImages.eq(index).show();
  }

  $(".gallery-prev").on("click", function () {
    currentIndex =
      currentIndex > 0 ? currentIndex - 1 : $galleryImages.length - 1;
    showImage(currentIndex);
  });

  $(".gallery-next").on("click", function () {
    currentIndex =
      currentIndex < $galleryImages.length - 1 ? currentIndex + 1 : 0;
    showImage(currentIndex);
  });

  // Show the first image initially
  showImage(currentIndex);
});
