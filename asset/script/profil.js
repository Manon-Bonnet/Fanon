"use strict";

//Pour attendre le chargement de la page
document.addEventListener('DOMContentLoaded', (event) => {
  // Get all the elements
  const modifyProfilButtons = document.getElementById('modifier-profil');
  const undoModify = document.getElementsByClassName('undo-modify');

  // Add the listeners
  modifyProfilButtons.addEventListener("click", changeElement);
});