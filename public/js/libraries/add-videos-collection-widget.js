const addVideoLink = document.createElement('b')
addVideoLink.classList.add('add_video_list', 'btn', 'btn-primary')
addVideoLink.innerText = 'Ajouter une url de video Youtube'
addVideoLink.dataset.collectionHolderClass = 'videos'

const newVideoLinkLi = document.createElement('li').append(addVideoLink)

const videosCollectionHolder = document.querySelector('ul.videos')
videosCollectionHolder.appendChild(addVideoLink)

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
}

addVideoLink.addEventListener("click", addFormToVideosCollection)