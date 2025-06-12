function autocomplete(input, hiddenInput, source) {
    let currentFocus;
    input.addEventListener("input", function () {
        const val = this.value;
        closeAllLists();
        if (!val) return false;
        fetch(`${source}?q=${encodeURIComponent(val)}`)
            .then(response => response.json())
            .then(data => {
                //Erstelle die DIV-Liste für Autosuggestion unter dem Eltern-Element
                const list = document.createElement("div");
                list.setAttribute("id", this.id + "autocomplete-list");
                list.setAttribute("class", "autocomplete-suggestions");
                this.parentNode.appendChild(list);
                //Füge jedes Produkt der Autosuggestion-Liste hinzu, wenn der User entsprechende Kategorien und Namen eingibt, durch eine For-Each Schleife
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
    //Wenn die Eingabe beendet wurde, wird diese Methode aufgerufen, um die Child DIV-Box unter "autocomplete-suggestions" zu löschen.
    function closeAllLists() {
        const lists = document.querySelectorAll(".autocomplete-suggestions");
        lists.forEach(l => l.parentNode.removeChild(l));
    }
    //Führe closeAllLists() aus, wenn der User auf ein Produkt geclickt hat.
    document.addEventListener("click", function (e) {
        closeAllLists();
    });
}
