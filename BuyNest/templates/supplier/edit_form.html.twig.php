{{ form_start(form, {'attr': {'id': 'editForm'}}) }}
    <div class="space-y-4">
        {{ form_row(form.name, {
            'label': 'Nome:',
            'attr': {
                'class': 'w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500'
            }
        }) }}

        {{ form_row(form.company, {
            'label': 'Razão Social:',
            'attr': {
                'class': 'w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500'
            }
        }) }}

        {{ form_row(form.cpf_cnpj, {
            'label': 'CPF/CNPJ:',
            'attr': {
                'class': 'w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500'
            }
        }) }}

        {# ... adicione os outros campos do formulário ... #}

        <div class="flex items-center justify-end space-x-3 mt-4">
            <button type="button" 
                    onclick="closeEditModal()" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Cancelar
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Salvar
            </button>
        </div>
    </div>
{{ form_end(form) }}