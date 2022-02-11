const addImageLink = document.createElement('a')
addImageLink.classList.add('add_image_list', 'btn', 'btn-primary')
addImageLink.innerText = 'Ajouter une photo'
addImageLink.dataset.collectionHolderClass = 'images'

const newImageLinkLi = document.createElement('li').append(addImageLink)

const imagesCollectionHolder = document.querySelector('ul.images')
imagesCollectionHolder.appendChild(addImageLink)

const addFormToImagesCollection = (e) => {
    const imagesCollectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('li');

    item.innerHTML = imagesCollectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            imagesCollectionHolder.dataset.index
        );

    imagesCollectionHolder.appendChild(item);

    imagesCollectionHolder.dataset.index++;
}

addImageLink.addEventListener("click", addFormToImagesCollection)