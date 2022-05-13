/* global Nova */
import UnlayerEditor from './components/UnlayerEditor.vue';
import IndexField from './components/IndexField.vue';
import DetailField from './components/DetailField.vue';
import FormField from './components/FormField.vue';

Nova.booting((app, store) => {
    app.component('unlayer-editor', UnlayerEditor);

    app.component('index-nova-unlayer-field', IndexField);
    app.component('detail-nova-unlayer-field', DetailField);
    app.component('form-nova-unlayer-field', FormField);
})
