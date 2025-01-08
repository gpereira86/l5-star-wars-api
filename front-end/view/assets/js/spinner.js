function showSpinner(chosenTag) {
    const loadingScreen = document.createElement("div");
    loadingScreen.id = "loading-screen";
    loadingScreen.className = "loading";
    loadingScreen.innerHTML = `
    <div class="starwars-spinner">
      <div class="blade"></div>
      <div class="blade"></div>
      <div class="blade"></div>
      <div class="blade"></div>
    </div> <br><br>
    <p class="blades-loading-text">Loading... May the Force be with you!</p>
  `;
    chosenTag.appendChild(loadingScreen);
}

function hideSpinner(chosenTag) {
    const loadingScreen = document.getElementById("loading-screen");
    if (loadingScreen) {
        chosenTag.removeChild(loadingScreen);
    }
}

window.showSpinner = showSpinner;
window.hideSpinner = hideSpinner;
