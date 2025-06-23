import { Controller } from '@hotwired/stimulus';
import { Turbo } from '@hotwired/turbo';
export default class extends Controller {
    connect() {
        console.log('Inventory form controller connected');
    }

    validateField(event) {
        const field = event.currentTarget.dataset.field;
        const form = this.element;

        // Create FormData from the form
        const formData = new FormData(form);

        // Send a fetch request to validate the field
        fetch(`/inventory/validate/${field}`, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'text/vnd.turbo-stream.html',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Process the Turbo Stream response
            Turbo.renderStreamMessage(html);
        })
        .catch(error => {
            console.error('Error validating field:', error);
        });
    }

    save(event) {
        event.preventDefault();
        const form = this.element;

        // Create FormData from the form
        const formData = new FormData(form);

        // Send a fetch request to save the form
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'text/vnd.turbo-stream.html',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Process the Turbo Stream response
            Turbo.renderStreamMessage(html);

            // If the form was submitted successfully, reset the form
            if (!html.includes('error')) {
                form.reset();
                // Dispatch an event that will be caught by the DOMContentLoaded handler
                document.dispatchEvent(new CustomEvent('form-reset'));
            }
        })
        .catch(error => {
            console.error('Error saving form:', error);
        });
    }
}