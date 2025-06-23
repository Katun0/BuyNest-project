// inventory_form_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    validateField(event) {
        const field = event.target;
        if (field.classList.contains('price-mask')) {
            validatePrice(field);
        }
    }
    
    save(event) {
        const priceInput = this.element.querySelector('.price-mask');
        if (!validatePrice(priceInput)) {
            event.preventDefault();
            return false;
        }

    }
}