document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("packagesContainer");
  const grid = document.getElementById("packagesGrid");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");

  function getScrollAmount() {
    const card = document.querySelector(".wid-package-card");
    return card.offsetWidth + parseInt(getComputedStyle(card).marginRight);
  }

  function scrollNext() {
    container.scrollBy({
      left: getScrollAmount(),
      behavior: "smooth",
    });
  }

  function scrollPrev() {
    container.scrollBy({
      left: -getScrollAmount(),
      behavior: "smooth",
    });
  }

  function updateButtonStates() {
    const scrollLeft = container.scrollLeft;
    const maxScroll = container.scrollWidth - container.clientWidth;

    prevBtn.disabled = scrollLeft <= 0;
    nextBtn.disabled = scrollLeft >= maxScroll - 1;

    prevBtn.style.visibility = scrollLeft <= 0 ? "hidden" : "visible";
    nextBtn.style.visibility =
      scrollLeft >= maxScroll - 1 ? "hidden" : "visible";
  }

  prevBtn.addEventListener("click", scrollPrev);
  nextBtn.addEventListener("click", scrollNext);
  container.addEventListener("scroll", updateButtonStates);
  window.addEventListener("resize", updateButtonStates);

  updateButtonStates();

  // Touch handling
  let touchStartX = 0;
  let touchEndX = 0;

  container.addEventListener(
    "touchstart",
    (e) => {
      touchStartX = e.changedTouches[0].screenX;
    },
    false
  );

  container.addEventListener(
    "touchend",
    (e) => {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    },
    false
  );

  function handleSwipe() {
    const difference = touchStartX - touchEndX;
    const threshold = 50;

    if (Math.abs(difference) > threshold) {
      if (difference > 0) {
        scrollNext();
      } else {
        scrollPrev();
      }
    }
  }
});
