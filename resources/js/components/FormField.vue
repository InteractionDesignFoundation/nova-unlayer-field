<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <div :id=containerId :style="{height: field.height || '800px'}"></div>
            <p v-if="hasError" class="my-2 text-danger">
                {{ firstError }}
            </p>
        </template>
    </default-field>
</template>

<script>
    import { FormField, HandlesValidationErrors } from 'laravel-nova'

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        created() {
            this.injectUnlayerScript(this.initEditor);
        },

        computed: {
            containerId: function () {
                return `${this.field.attribute}--editorContainer`;
            }
        },

        methods: {
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = JSON.parse(this.field.value) || {};
            },

            /**
             * Fill the given FormData object with the field's internal value.
             * @property {FormData} formData
             */
            fill(formData) {
                formData.append(this.field.attribute, JSON.stringify(this.value));
                formData.append(`${this.field.attribute}_html`, this.finalHtml);
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = value
            },

            /**
             * @param {Function} onLoadCallback
             */
            injectUnlayerScript(onLoadCallback) {
                const unlayerScript = document.createElement('script');
                unlayerScript.setAttribute('src', '//editor.unlayer.com/embed.js');
                unlayerScript.onload = onLoadCallback;
                document.head.appendChild(unlayerScript);
            },
            initEditor() {
                const unlayerConfig = this.field.config;
                unlayerConfig.id = this.containerId;
                const editExistDesign = this.value && Object.keys(this.value).length;
                if (editExistDesign && unlayerConfig.templateId) {
                    this.templateId = unlayerConfig.templateId;
                    delete unlayerConfig.templateId;
                }

                window.unlayer.init(unlayerConfig);

                if (editExistDesign) {
                    window.unlayer.loadDesign(this.value);
                }

                window.unlayer.addEventListener('design:loaded', this.designLoaded);
                window.unlayer.addEventListener('design:updated', this.designUpdated);
            },

            /**
             * @param {{design: Object}} loadedDesign
             */
            designLoaded(loadedDesign) {
                window.unlayer.exportHtml((editorData) => {
                    this.finalHtml = editorData.html;
                    this.value = editorData.design;
                });
            },

            /**
             * @param {{item: Object, type: string}} changeLog
             */
            designUpdated(changeLog) {
                window.unlayer.exportHtml((editorData) => {
                    this.finalHtml = editorData.html;
                    this.value = editorData.design;
                });
            },
        },
    }
</script>
