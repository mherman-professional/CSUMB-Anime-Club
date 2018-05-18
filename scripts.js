// Add event listeners to change the displayed collection navigation links
// when the "next" or "previous" options are selected or when the window is resized
document.getElementById('prevCollection').addEventListener("click", function() {
  changeCollectionNav('prev');
});
document.getElementById('nextCollection').addEventListener("click", function() {
  changeCollectionNav('next');
});
window.addEventListener("resize", changeCollectionNav);

// This function sets the class attribute of collection navigation links to change which links are displayed
function changeCollectionNav(action) {
  // Calculate the number of Links to display
  var numDisplay = Math.floor((window.innerWidth - 150) / 160);
  // Get all collection links
  var linksAll = document.querySelectorAll("a.colNav");
  // Get all currently displayed collection links
  var visible = document.querySelectorAll("a.display");
  var firstLink;

  if (visible.length > 0) {
    // Search for the collection link in linksAll which matches the first currently displayed collection link
    for (i = 0; i < linksAll.length; i++) {
      if (linksAll[i] == visible[0]) {
        firstLink = i;
        break;
      }
    }
    // Reset all collection links to hidden (the ones we want to display will be set later)
    for (i = 0; i < visible.length; i++) {
      visible[i].setAttribute('class', 'nav colNav hidden');
    }
  }
  else {
    // If this is the first time the page loaded set first link to display to 0
    firstLink = 0;
  }

  if (action == 'prev') {
    // Determine first link to display if "previous" is selected
    firstLink = ((firstLink - numDisplay) < 0)? 0 : (firstLink - numDisplay);
  }
  else if (action == 'next'){
    // Determine first link to display if "next" is selected
    firstLink = (((firstLink + numDisplay - 1) + numDisplay) >= linksAll.length)? (linksAll.length - numDisplay) : (firstLink + numDisplay);
  }

  // Set class attribute to include "display" starting with link set as first and continuing for the number of links to be displayed
  for (i = 0; i < numDisplay; i++) {
    linksAll[firstLink++].setAttribute('class', 'nav colNav display');
  };
};
