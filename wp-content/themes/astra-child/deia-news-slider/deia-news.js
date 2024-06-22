jQuery(document).ready(function ($) {
  // Set the base speed of the auto-scroll (milliseconds)
  var baseScrollSpeed = 15000; // Adjust the base scroll speed here
  var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

  // Adjust scroll speed for mobile devices
  if (isMobile) {
    baseScrollSpeed = 5000; // Faster scroll speed for mobile
  }

  // Get the news slider element and its width
  var newsSlider = $("#news-slider");
  var newsSliderList = $("#news-slider .news-slider-list");
  var sliderWidth = newsSlider.width(); // Use the width of the slider
  var totalWidth = newsSliderList[0].scrollWidth; // Calculate the total width of the news slider list

  // Clone the news slider list to ensure continuous looping
  newsSliderList.append(newsSliderList.html());

  // Calculate the scroll speed based on the width of the news slider list
  var scrollSpeed = (baseScrollSpeed * totalWidth) / sliderWidth;

  // Animate the slider list to scroll left
  function scrollNews() {
    newsSliderList.animate(
      {
        left: "-=" + totalWidth, // Scroll the entire width of the news slider list
      },
      scrollSpeed,
      "linear",
      function () {
        // Reset position when the entire list is scrolled
        newsSliderList.css("left", 0);
        // Call the function again after the animation completes
        scrollNews();
      }
    );
  }

  // Start scrolling
  scrollNews();
});
