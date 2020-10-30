window.onload = () => {
    //gestion des boutons supprimer on ramasse toutes les balises du DOM qui ont l'attribut data_delete
    let links = document.querySelectorAll("[data-delete]")

    for (link of links) {
        //on rajoute un écouteur
        link.addEventListener("click", function(e) {
            e.preventDefault()
                //confirmer la desrtuction
            if (confirm("Voulez vous vraiment détruire cette image ?")) {
                //le this pointe sur le lien cliqué donc l'url du href
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-type": "application/json"
                    },
                    body: JSON.stringify({ "_token": this.dataset.token })
                }).then(
                    //on récupère la réponse JSON
                    response => response.json()
                ).then(data => {
                    if (data.success)
                        this.parentElement.remove() // supprime la div qui est parente
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}