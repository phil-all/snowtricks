const addFormToVideosCollection = (e) => {
    const videosCollectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('li');

    item.innerHTML = videosCollectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            videosCollectionHolder.dataset.index
        );

    videosCollectionHolder.appendChild(item);

    videosCollectionHolder.dataset.index++;
};

document
    .querySelectorAll('.add_video_link')
    .forEach(btn => {
        btn.addEventListener("click", addFormToVideosCollection)
    });