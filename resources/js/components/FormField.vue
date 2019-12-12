<template>
    <default-field :field="field" :errors="errors" :full-width-content="true">
        <template slot="field">
            <div class="unlayerControls flex">
                <button
                        id="fullscreenToggleButton"
                        class="text-xs bg-90 hover:bg-black text-white font-semibold rounded-sm px-4 py-1 m-1 border"
                        @click="toggleFullscreen"
                        type="button">
                    {{ fullscreenButtonText.on }}
                </button>
            </div>
            <div :id=containerId :style="{height: field.height || '800px'}" class="form-input-bordered"></div>
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

        data: () => ({
            fullscreenButtonText: {
                on: '▶ Enter fullscreen',
                off: '✖︎ Exit fullscreen',
            },
        }),

        created() {
            this.injectUnlayerScript(this.initEditor);
        },

        computed: {
            containerId: function () {
                return `${this.field.attribute}--editorContainer`;
            }
        },

        methods: {
            toggleFullscreen() {
                // toggle scrolling of the page
                document.body.classList.toggle('overflow-hidden');

                const unlayerEditorContainer = this.$el.querySelector(`#${this.containerId}`);
                unlayerEditorContainer.classList.toggle('z-50');
                unlayerEditorContainer.classList.toggle('fullscreen');

                const controls = this.$el.querySelector('.unlayerControls');
                controls.classList.toggle('stickyControls');

                const toggleButton = controls.querySelector(`#fullscreenToggleButton`);
                unlayerEditorContainer.classList.contains('fullscreen')
                    ? toggleButton.innerText = this.fullscreenButtonText.off
                    : toggleButton.innerText = this.fullscreenButtonText.on;
            },

            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value ? JSON.parse(this.field.value) : {};
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

<style scoped>
    .fullscreen {
        position: fixed;
        left: 1vw;
        top: 1vh;
        width: 98vw !important;
        height: 98vh !important;
    }

    .stickyControls {
        position: fixed;
        left: 1vw;
        top: 1vh;
        z-index: 51;
    }
</style>
