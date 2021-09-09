document.addEventListener('DOMContentLoaded', (event) => {
  // Get all the elements
  const modifyStockButtons = document.getElementsByClassName('modify-stock');
  const undoModify = document.getElementsByClassName('undo-modify');

  // Add the listeners
  for(let i = 0 ; i < modifyStockButtons.length ; i++){
    modifyStockButtons[i].addEventListener("click", changeElement);
    undoModify[i].addEventListener("click", changeElement);
    event.preventDefault();
  }
});


// functions

// Drag n Drop pictures
const dropSpace = document.getElementsByClassName('drop-space')[0];
const addProductForm = document.getElementsByClassName('add-produit-form')[0];
const imagePreviewSpace = document.getElementById("image-preview");
const dragAndDropText = document.getElementsByClassName("drag-n-drop-text")[0];

// Enlever les evenements par défaut au drop
dropSpace.addEventListener('dragenter', preventDefault, false);
dropSpace.addEventListener('dragleave', preventDefault, false);
dropSpace.addEventListener('dragover', preventDefault, false);
dropSpace.addEventListener('drop', preventDefault, false);

// Ajouter les écouteurs
dropSpace.addEventListener('drop', handleDrop, false);
dropSpace.addEventListener("click", function() {
  fakeInput.click();
});

let fakeInput = document.createElement('input');
fakeInput.accept = "image/*";
fakeInput.type = 'file';
fakeInput.multiple = 'true';

let files, imagesNb = 0, j = 0, allImages = [];

//functions
function preventDefault(e) {
  e.preventDefault();
  e.stopPropagation();
}

fakeInput.addEventListener("change", function() {
  files = this.files;
  handleFiles(files);
});

function handleDrop(e) {
  let data = e.dataTransfer, files = data.files;
  handleFiles(files)      
}

function handleFiles(files) {
  for (let i = 0 ; i < files.length; i++) {
    if (validateImage(files[i])){ 
      previewAnduploadImage(files[i]);
      let newInput = document.createElement('input');
      newInput.type = 'file';
      newInput.accept = "image/*";
      newInput.classList.add('hidden');
      newInput.name = 'image-url-' + j;
      newInput.files = files;
      j++;
      addProductForm.appendChild(newInput);
    };
  }
}

function validateImage(image) {
  // check the type
  let validTypes = ['image/jpeg', 'image/png'];
  if (validTypes.indexOf( image.type ) === -1) {
    return false;
  }

  // check the size
  var maxSizeInBytes = 10e6; // 10MB
  if (image.size > maxSizeInBytes) {
    return false;
  }

  return true;
}

function previewAnduploadImage(image) {
 
  // container
  let imgView = document.createElement("div");
  imgView.className = "image-view";
  imagePreviewSpace.appendChild(imgView);
  allImages.push(imgView);

  let img = document.createElement("img");
  imgView.appendChild(img);
  img.classList.add("img-drop");

  // read the pic
  let reader = new FileReader();
  reader.onload = function(e) {
    img.src = e.target.result;
  }
  reader.readAsDataURL(image); 

  imagesNb ++;
  for(let i = 0 ; i < imagesNb ; i++){
    allImages[i].style.width = (100 / imagesNb) -1 + '%';
  }

  if(imagesNb >= 3){
    let addedImages = document.getElementsByClassName('img-drop');
    console.log(addedImages);
    let cpt = addedImages.length;
    for(let i = 0 ; i < cpt; i++){
      addedImages[0].classList.add('img-drop-more');
      addedImages[0].classList.remove('img-drop');
    }
  }

  dragAndDropText.classList.add('hidden');

}
