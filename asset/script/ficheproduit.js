"use strict";

//Pour attendre le chargement de la page
document.addEventListener('DOMContentLoaded', (event) => {
  // Get all the elements
  const miniatures = document.getElementsByClassName('second_produit_img');
  // Add the listeners
  for(let i = 0; i < miniatures.length; i++){
    miniatures[i].addEventListener("click", changePhoto);
  }
});