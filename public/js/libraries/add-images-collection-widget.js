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
};

document
    .querySelectorAll('.add_image_link')
    .forEach(btn => {
        btn.addEventListener("click", addFormToImagesCollection)
    });