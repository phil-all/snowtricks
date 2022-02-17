const newItem = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);

    const item = document.createElement("div");
    item.classList.add("col-12");
    item.classList.add("p-2");
    item.classList.add("mb-2");
    item.classList.add("item");
    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );

    item.querySelector(".btn-remove").addEventListener("click", () => item.remove());

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;
};

document
    .querySelectorAll('.btn-remove')
    .forEach(btn =>
        btn.addEventListener("click", (e) => e.currentTarget.closest('.item').remove()));

document
    .querySelectorAll('.btn-new')
    .forEach(btn =>
        btn.addEventListener("click", newItem)
    );
