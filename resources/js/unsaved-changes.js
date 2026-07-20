// Sistema de detección de cambios sin guardar para OlimpicSC
(function() {
    let isSubmitting = false;

    function hasUnsavedChanges() {
        if (isSubmitting) return false;
        const dirtyForm = document.querySelector('form[data-dirty="true"]');
        return !!dirtyForm;
    }

    function markDirty(target) {
        if (!target || !target.form) return;
        const form = target.form;

        // Ignorar formularios de búsqueda (GET) o indicados como no-warn
        if (form.method && form.method.toUpperCase() === 'GET') return;
        if (form.hasAttribute('data-no-warn') || target.hasAttribute('data-no-warn')) return;
        if (target.name === '_token' || target.name === '_method' || target.type === 'submit' || target.type === 'button') return;

        form.dataset.dirty = "true";
    }

    // Escuchar eventos input y change en controles del formulario
    document.addEventListener('input', (e) => markDirty(e.target), true);
    document.addEventListener('change', (e) => markDirty(e.target), true);

    // Al enviar un formulario, limpiar la marca y marcar envío en curso
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form) {
            form.dataset.dirty = "false";
        }
        isSubmitting = true;
    }, true);

    // Restablecer estado al navegar mediante Turbo
    document.addEventListener('turbo:load', () => {
        isSubmitting = false;
        document.querySelectorAll('form[data-dirty="true"]').forEach(f => f.dataset.dirty = "false");
    });

    // Detectar cierre de pestaña / recarga / navegación externa
    window.addEventListener('beforeunload', (e) => {
        if (hasUnsavedChanges()) {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    });

    // Detectar navegación interna con Turbo (sidebar, enlaces, atrás/adelante)
    document.addEventListener('turbo:before-visit', (e) => {
        if (hasUnsavedChanges()) {
            const confirmed = window.confirm('Tienes cambios sin guardar en el formulario. ¿Estás seguro de que deseas salir sin guardar?');
            if (!confirmed) {
                e.preventDefault();
            }
        }
    });

    // Utilidades globales
    window.markFormDirty = function(formOrElement) {
        const form = formOrElement?.form || formOrElement;
        if (form && form.tagName === 'FORM') {
            form.dataset.dirty = "true";
        }
    };
    window.clearFormDirty = function(formOrElement) {
        const form = formOrElement?.form || formOrElement;
        if (form && form.tagName === 'FORM') {
            form.dataset.dirty = "false";
        }
    };
})();
