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
        }),

        computed: {
            editorHeight() {
                return this.field.height || defaultHeight;
            },
        },

        methods: {
            /**
             * Register listeners, load initial template, etc.
             */
            editorLoaded() {
                if (this.field.value) {
                    this.$refs.editor.loadDesign(JSON.parse(this.field.value));
                }

                /** @see https://docs.unlayer.com/docs/events */
                window.unlayer.addEventListener('design:loaded', this.handleDesignLoaded);
                window.unlayer.addEventListener('design:updated', this.handleDesignUpdated);
                window.unlayer.addEventListener('onImageUpload', this.handleImageUploaded);

                this.loadPlugins(this.field.plugins);
            },

            /**
             * @param {Array} pluginsUrls
             */
            loadPlugins(pluginsUrls) {
                if (window.unlayer.plugins === undefined) {
                    window.unlayer.plugins = [];
                }

                pluginsUrls.forEach(pluginUrl => {
                    const script = document.createElement('script');
                    script.setAttribute('src', pluginUrl);
                    document.head.appendChild(script);
                });
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
                this.$refs.editor.exportHtml((editorData) => {
                    this.design = editorData.design;
                    this.html = editorData.html;
                });

                Nova.$emit('unlayer:design:loaded', {
                    inputName: this.field.attribute,
                    payload: loadedDesign,
                });
            },

            /**
             * Generate a design where we use replace a changed node
             * (usually updated by plugins) by it's new state
             * @param {Object} updatedNode
             * @param {Object} design
             * @returns {Object}
             */
            getDesignWithUpdatedNode(updatedNode, design) {
                const htmlIdOfChangedNode = updatedNode.values._meta.htmlID;

                design.body.rows.find((row, rowIndex) => {
                    return row.columns.find((column, columnIndex) => {
                        return column.contents.find((currentNode, contentIndex) => {
                            if (currentNode.values._meta.htmlID !== htmlIdOfChangedNode) {
                                return false;
                            }

                            design.body.rows[rowIndex].columns[columnIndex].contents[contentIndex] = updatedNode;
                            return true;
                        }) === true;
                    }) === true;
                });

                return design;
            },

            /**
             * @param {{item: Object, type: string, changes: ?Object}} changeLog
             */
            handleDesignUpdated(changeLog) {
                const originalChangedItemAsString = JSON.stringify(changeLog.item);

                /** @type {Object} */
                const updatedByPluginsNode = Object.values(window.unlayer.plugins).reduce((prev, pluginFn) => {
                    return pluginFn(prev, changeLog.type, changeLog.changes);
                }, changeLog.item);

                const updatedChangeLogAsString = JSON.stringify(updatedByPluginsNode);
                if (originalChangedItemAsString !== updatedChangeLogAsString) {
                    this.$refs.editor.exportHtml((editorData) => {
                        const design = this.getDesignWithUpdatedNode(updatedByPluginsNode, editorData.design);
                        this.$refs.editor.loadDesign(design);

                        this.$refs.editor.exportHtml((editorData) => {
                            this.design = editorData.design;
                            this.html = editorData.html;
                        });
                    });
                }

                Nova.$emit('unlayer:design:updated', {
                    inputName: this.field.attribute,
                    payload: changeLog,
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
