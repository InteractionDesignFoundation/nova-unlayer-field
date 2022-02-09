import UnlayerEditor from './components/UnlayerEditor.vue';
import IndexField from './components/IndexField.vue';
import DetailField from './components/DetailField.vue';
import FormField from './components/FormField.vue';

Nova.booting((Vue) => {
  Vue.component('unlayer-editor', UnlayerEditor);
  Vue.component('index-nova-unlayer-field', IndexField);
  Vue.component('detail-nova-unlayer-field', DetailField);
  Vue.component('form-nova-unlayer-field', FormField);
});
