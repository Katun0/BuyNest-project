import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('change', this.toggleInactive.bind(this));
    }

    async toggleInactive(event) {
        // Atualiza a URL atual
        const currentUrl = new URL(window.location.href);
        if (this.element.checked) {
            currentUrl.searchParams.set('show_inactive', '1');
        } else {
            currentUrl.searchParams.delete('show_inactive');
        }

        // Atualiza a URL sem recarregar a página
        window.history.pushState({}, '', currentUrl.toString());

        // Faz a requisição Turbo para atualizar a tabela
        const apiUrl = new URL('/supplier/toggle-inactive', window.location.origin);
        apiUrl.searchParams.set('show_inactive', this.element.checked ? '1' : '0');

        const response = await fetch(apiUrl.toString(), {
            headers: {
                'Accept': 'text/vnd.turbo-stream.html'
            }
        });

        if (response.ok) {
            const html = await response.text();
            Turbo.renderStreamMessage(html);
        }
    }
}