<template>
    <default-field
        :field="field"
        :errors="errors"
        :full-width-content="true"
    >
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

            <unlayer-editor
                class="form-input-bordered"
                ref="editor"
                @load="editorLoaded"
                :minHeight=editorHeight
                :locale=field.config.locale
                :projectId=field.config.projectId
                :templateId="field.value ? null : field.config.templateId"
                :style="{height: editorHeight}"
            />
        </template>
    </default-field>
</template>

<script>
    import EmailEditor from './UnlayerEditor'
    import { FormField, HandlesValidationErrors } from 'laravel-nova'

    const defaultHeight = '800px';

    export default {
        mixins: [FormField, HandlesValidationErrors],

        components: {
            EmailEditor
        },

        props: ['resourceName', 'resourceId', 'field'],

        data: () => ({
            fullscreenButtonText: {
                on: '▶ Enter fullscreen',
                off: '✖︎ Exit fullscreen',
            },
            loadedPlugins: [],
        }),

        computed: {
            editorHeight() {
                return this.field.height || defaultHeight;
            },
        },

        mounted() {
            this.loadPlugins(this.field.plugins);
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

            /**
             * Register listeners, load initial template, etc.
             */
            editorLoaded() {
                if (this.field.value) {
                    this.$refs.editor.loadDesign(JSON.parse(this.field.value));
                }

                /** @see https://docs.unlayer.com/docs/events */
                this.$refs.editor.addEventListener('design:loaded', this.handleDesignLoaded);
                this.$refs.editor.addEventListener('design:updated', this.handleDesignUpdated);
                this.$refs.editor.addEventListener('onImageUpload', this.handleImageUploaded);
            },

            /**
             * Fill the given FormData object with the field's internal value.
             * Nova runs it before submission.
             * @property {FormData} formData
             */
            fill(formData) {
                formData.append(this.field.attribute, JSON.stringify(this.design));
                formData.append(`${this.field.attribute}_html`, this.html);
            },

            /**
             * @param {{design: Object}} loadedDesign
             */
            handleDesignLoaded(loadedDesign) {
                Nova.$emit('unlayer:design:loaded', {
                    inputName: this.field.attribute,
                    payload: loadedDesign,
                });

                this.$refs.editor.exportHtml((editorData) => {
                    this.design = editorData.design;
                    this.html = editorData.html;
                });
            },

            /**
             * @param {{item: Object, type: string}} changeLog
             */
            handleDesignUpdated(changeLog) {
                Nova.$emit('unlayer:design:updated', {
                    inputName: this.field.attribute,
                    payload: changeLog,
                });

                this.$refs.editor.exportHtml((editorData) => {
                    const originalDesignAsString = JSON.stringify(editorData.design);
                    /** @type {string} */
                    const updatedDesignAsString = this.loadedPlugins.reduce((prev, plugin) => {
                        return plugin.process(prev, changeLog.type);
                    }, originalDesignAsString);

                    if (updatedDesignAsString !== originalDesignAsString) {
                        const updatedDesign = JSON.parse(updatedDesignAsString);
                        this.$refs.editor.loadDesign(updatedDesign);

                        this.design = editorData.design;
                        this.html = editorData.html;
                    }
                });
            },

            /**
             * @param {Object} imageData
             */
            handleImageUploaded(imageData) {
                Nova.$emit('unlayer:image:uploaded', {
                    inputName: this.field.attribute,
                    payload: imageData,
                });
            },

            /**
             * @param {Array} plugins
             */
            loadPlugins(plugins) {
                const importPromises = [];
                plugins.forEach(pluginUrl => {
                    // Use native browser's import because we don't know anything about path to the module
                    // at the moment of JS transpiling by webpack and babel.
                    importPromises.push(import(/* webpackIgnore: true */ pluginUrl));
                });

                Promise.all(importPromises).then(loadedPlugins => {
                    this.loadedPlugins = loadedPlugins;
                }).catch(this.$toasted.show(
                    'Could not load one or more Unlayer plugins. Unlayer editor loaded without any plugins.',
                    { type: 'error' })
                );
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
