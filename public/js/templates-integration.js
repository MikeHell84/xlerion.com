// Integration bridge between global template picker event and the page editor
(function(){
    // Assumes existing editor exposes a global `Editor` object with insertHTML or API method
    async function onInsert(e){
        const template = e.detail; // { id, name, data }
        // Convert template data to HTML - basic example
        const html = renderTemplateDataToHtml(template.data || {});
        if (window.Editor && typeof window.Editor.insertHTML === 'function') {
            window.Editor.insertHTML(html);
        } else if (window.tinymce) {
            tinymce.activeEditor.execCommand('mceInsertContent', false, html);
        } else if (document.querySelector('[contenteditable]')) {
            document.querySelector('[contenteditable]').focus();
            document.execCommand('insertHTML', false, html);
        } else {
            console.warn('No editor integration available');
        }
    }

    function renderTemplateDataToHtml(data){
        // data example: { regions: { header: '<h1>..</h1>', content: '<p>..</p>', footer: '...' } }
        const regions = data.regions || {};
        let html = '<div class="template-wrapper">';
        html += `<header class="tpl-header">${regions.header||''}</header>`;
        html += `<main class="tpl-content">${regions.content||''}</main>`;
        html += `<footer class="tpl-footer">${regions.footer||''}</footer>`;
        html += '</div>';
        return html;
    }

    window.addEventListener('template:insert', onInsert);
    window.TemplatesIntegration = { renderTemplateDataToHtml };
})();
