function autocomplete(input, hiddenInput, source) {
    let currentFocus;
    input.addEventListener("input", function () {
        const val = this.value;
        closeAllLists();
        if (!val) return false;

        fetch(`${source}?q=${encodeURIComponent(val)}`)
            .then(response => response.json())
            .then(data => {
                const list = document.createElement("div");
                list.setAttribute("id", this.id + "autocomplete-list");
                list.setAttribute("class", "autocomplete-suggestions");
                this.parentNode.appendChild(list);

                data.forEach(item => {
                    const itemDiv = document.createElement("div");
                    itemDiv.innerHTML = item.Name;
                    itemDiv.classList.add("autocomplete-suggestion");
                    itemDiv.addEventListener("click", function () {
                        input.value = item.Name;
                        hiddenInput.value = item.EAN;
                        closeAllLists();
                    });
                    list.appendChild(itemDiv);
                });
            });
    });

    function closeAllLists() {
        const lists = document.querySelectorAll(".autocomplete-suggestions");
        lists.forEach(l => l.parentNode.removeChild(l));
    }

    document.addEventListener("click", function (e) {
        closeAllLists();
    });
}
