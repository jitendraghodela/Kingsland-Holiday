// Description: Custom JS file for Kings Theme
// Used in: All pages
// Author: jitendra ghodela
// Created: 26/11/2024
// make sure to add this file in the main js file
// Destinations
// section ens

document
  .getElementById("add-itinerary-btn")
  .addEventListener("click", function () {
    var itineraryRepeater = document.getElementById("itinerary-repeater");
    var newIndex = itineraryRepeater.children.length;
    var newItineraryItem = `
    <div class="itinerary-item" style="margin-bottom: 10px;">
      <input type="text" name="itinerary[${newIndex}][day_title]" placeholder="Day Title" value=""
        style="width: 100%; margin-bottom: 5px;" />

      <input type="text" name="itinerary[${newIndex}][day_tags]" placeholder="Day Tags (comma-separated)" value="" 
        style="width: 100%; margin-bottom: 5px;" />

      <textarea name="itinerary[${newIndex}][day_label]" placeholder="Day Activities"
        style="width: 100%; margin-bottom: 5px;"></textarea>

      <button type="button" class="remove-itinerary-btn button">Remove Day</button>
    </div>`;
    itineraryRepeater.insertAdjacentHTML("beforeend", newItineraryItem);
  });

// Event delegation to handle removal of itinerary items
document
  .getElementById("itinerary-repeater")
  .addEventListener("click", function (e) {
    if (e.target && e.target.classList.contains("remove-itinerary-btn")) {
      e.target.parentElement.remove();
    }
  });

document.getElementById("add-faq-btn").addEventListener("click", function () {
  var faqRepeater = document.getElementById("faq-repeater");
  var newIndex = faqRepeater.children.length;
  var newFAQItem = `<div class="faq-item" style="margin-bottom: 10px;">
<input type="text" name="faqs[${newIndex}][question]" placeholder="Question" style="width: 48%; margin-right: 2%;" />
<input type="text" name="faqs[${newIndex}][answer]" placeholder="Answer" style="width: 48%;" />
</div>`;
  faqRepeater.insertAdjacentHTML("beforeend", newFAQItem);
});

// Add new hotel item
document.getElementById("add-hotel-btn").addEventListener("click", function () {
  var hotelsRepeater = document.getElementById("hotels-repeater");
  var newIndex = hotelsRepeater.children.length;
  var newHotelItem = `
    <div class="hotel-item" style="margin-bottom: 10px; display:flex; gap: 7px; align-items: stretch;">
      <div>
        <img src="" alt="Hotel Image" style="width: 150px; height: 150px; margin-top: 10px;">
        <div style="display:flex; justify-content: space-evenly;">
          <button type="button" class="upload-image-btn" data-target="hotels[${newIndex}][image]" style="width: 72px; height: 47px; padding:0;">Upload Image</button>
          <button type="button" class="remove-hotel-btn" style="width: 72px; height: 47px; padding:0;">Remove</button>
        </div>
      </div>
      <div style="display:inline">
        <input type="text" name="hotels[${newIndex}][name]" placeholder="Hotel Name" />
        <input type="text" name="hotels[${newIndex}][address]" placeholder="Hotel Address" />
        <p class="note">
          Note: Write city in 2nd Last
        </p>
      </div>
      <input type="hidden" name="hotels[${newIndex}][image]" value="" />
      <select name="hotels[${newIndex}][rating]" style="width: 20%; height:10%">
        <option value="1">1 Star</option>
        <option value="2">2 Stars</option>
        <option value="3">3 Stars</option>
        <option value="4">4 Stars</option>
        <option value="5">5 Stars</option>
      </select>
    </div>`;
  hotelsRepeater.insertAdjacentHTML("beforeend", newHotelItem);
});

// Event delegation to handle removal of hotel items
document
  .getElementById("hotels-repeater")
  .addEventListener("click", function (e) {
    if (e.target && e.target.classList.contains("remove-hotel-btn")) {
      e.target.closest(".hotel-item").remove();
    }
  });

// Media uploader for hotel images
document.addEventListener("click", function (e) {
  if (e.target && e.target.classList.contains("upload-image-btn")) {
    e.preventDefault();
    var targetInput = document.querySelector(
      'input[name="' + e.target.getAttribute("data-target") + '"]'
    );
    var targetImage = e.target.closest(".hotel-item").querySelector("img");
    var customUploader = wp
      .media({
        title: "Select Image",
        button: {
          text: "Use this image",
        },
        multiple: false,
      })
      .on("select", function () {
        var attachment = customUploader
          .state()
          .get("selection")
          .first()
          .toJSON();
        targetInput.value = attachment.url;
        targetImage.src = attachment.url;
      })
      .open();
  }
});

// inclusions or exclusions
document
  .getElementById("add-inclusion-btn")
  .addEventListener("click", function () {
    var inclusionsRepeater = document.getElementById("inclusions-repeater");
    var newIndex = inclusionsRepeater.children.length;
    var newInclusionItem = `<div class="inclusion-item" style="margin-bottom: 10px;">
<input type="text" name="inclusions[${newIndex}]" style="width: 90%;" />
<button type="button" class="remove-inclusion-btn">Remove</button>
</div>`;
    inclusionsRepeater.insertAdjacentHTML("beforeend", newInclusionItem);
  });

// Event delegation to handle removal of inclusion items
document
  .getElementById("inclusions-repeater")
  .addEventListener("click", function (e) {
    if (e.target && e.target.classList.contains("remove-inclusion-btn")) {
      e.target.parentElement.remove();
    }
  });

document
  .getElementById("add-exclusion-btn")
  .addEventListener("click", function () {
    var exclusionsRepeater = document.getElementById("exclusions-repeater");
    var newIndex = exclusionsRepeater.children.length;
    var newExclusionItem = `<div class="exclusion-item" style="margin-bottom: 10px;">
<input type="text" name="exclusions[${newIndex}]" style="width: 90%;" />
<button type="button" class="remove-exclusion-btn">Remove</button>
</div>`;
    exclusionsRepeater.insertAdjacentHTML("beforeend", newExclusionItem);
  });

// Event delegation to handle removal of exclusion items
document
  .getElementById("exclusions-repeater")
  .addEventListener("click", function (e) {
    if (e.target && e.target.classList.contains("remove-exclusion-btn")) {
      e.target.parentElement.remove();
    }
  });
// section ens
//

document.addEventListener("DOMContentLoaded", function () {
  // Get all sidebar links
  const tabLinks = document.querySelectorAll(".admon-css-tab-link");
  // Get all content sections
  const tabContents = document.querySelectorAll(".admon-css-tab-content");

  tabLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      // Remove active class from all links
      tabLinks.forEach((link) => link.classList.remove("active"));
      // Add active class to the clicked link
      this.classList.add("active");

      // Get the tab that corresponds to the clicked link
      const tabId = this.getAttribute("data-tab");

      // Hide all content sections
      tabContents.forEach((content) => content.classList.remove("active"));

      // Show the content that corresponds to the clicked link
      document.getElementById(tabId).classList.add("active");
    });
  });
});
// end section ens

function toggleFaq(element) {
  var answer = element.nextElementSibling;
  var arrow = element.querySelector(".arrow");
  if (answer.style.display === "none") {
    answer.style.display = "block";
    arrow.textContent = "▲";
  } else {
    answer.style.display = "none";
    arrow.textContent = "▼";
  }
}
// header
document.addEventListener("DOMContentLoaded", function () {
  var mobTabSticky = document.querySelector(".mob-tab-sticky");
  var header = document.querySelector("header"); // Adjust the selector to match your header element

  window.addEventListener("scroll", function () {
    if (window.scrollY > header.offsetHeight || header.offsetHeight === 0) {
      mobTabSticky.style.position = "fixed";
      mobTabSticky.style.top = "0px";
    } else {
      mobTabSticky.style.position = "static";
    }
  });
});
// contact from
// Show modal
function showContactForm() {
  document.querySelector(".fromM").classList.add("show-modal");
  document.querySelector(".modal-overlay").classList.add("show-modal");
}

// Hide modal
function hideContactForm() {
  document.querySelector(".fromM").classList.remove("show-modal");
  document.querySelector(".modal-overlay").classList.remove("show-modal");
}

// Event listeners
document.querySelector(".close-btn").addEventListener("click", hideContactForm);
document
  .querySelector(".modal-overlay")
  .addEventListener("click", hideContactForm);
