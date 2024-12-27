import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static values = {
        addLabel: String,
        deleteLabel: String
    }

    connect() {
        this.index = this.element.childElementCount
        const btn = document.createElement('button')
        btn.setAttribute('class', 'btn btn-secondary')
        btn.setAttribute('type', 'button')
        btn.innerText = this.addLabelValue || 'Ajouter'
        btn.addEventListener('click', this.addElement)
        this.element.childNodes.forEach(this.addDeleteButton)
        this.element.append(btn)
    }

    /**
     * @param {MouseEvent} e
     */
    addElement = (e)=> {
        e.preventDefault()
        const element = document.createRange().createContextualFragment(
            this.element.dataset['prototype'].replaceAll('___name___', this.index)
        ).firstElementChild
        this.addDeleteButton(element)
        this.index++
        e.currentTarget.insertAdjacentElement('beforebegin', element)
    }

    addDeleteButton = (item) => {
        const btn = document.createElement('button')
        btn.setAttribute('class', 'btn btn-secondary')
        btn.setAttribute('type', 'button')
        btn.innerText = this.deleteLabelValue || 'Supprimer'
        item.append(btn)
        btn.addEventListener('click', e => {
            e.preventDefault()
            item.remove()
        })
    }
}
