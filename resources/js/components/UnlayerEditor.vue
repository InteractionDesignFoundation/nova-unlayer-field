<template>
    <div id="editor" class="flex" :style="{ minHeight: minHeight }"></div>
</template>

<script>
    export default {
        name: 'unlayer-editor',

        props: {
            options: Object,
            projectId: Number,
            templateId: Number,
            tools: Object,
            appearance: Object,
            locale: String,
            displayMode: {
                type: String,
                default: 'email',
            },
            minHeight: {
                type: String,
                default: '800px',
            },
        },
        created() {
            const unlayerScript = document.createElement('script');
            unlayerScript.setAttribute('src', '//editor.unlayer.com/embed.js');

            unlayerScript.onload = () => {
                this.loadEditor();
            };
            document.head.appendChild(unlayerScript);
        },
        mounted() {
        },
        methods: {
            loadEditor() {
                const options = this.options || {};

                if (this.projectId) {
                    options.projectId = this.projectId
                }

                if (this.templateId) {
                    options.templateId = this.templateId
                }

                if (this.tools) {
                    options.tools = this.tools
                }

                if (this.appearance) {
                    options.appearance = this.appearance
                }

                if (this.locale) {
                    options.locale = this.locale
                }

                const config = {
                    ...options,
                    id: 'editor',
                    displayMode: this.displayMode,
                };

                /* global unlayer */
                unlayer.init(config);

                this.$emit('load');
            },
            loadDesign(design) {
                unlayer.loadDesign(design);
            },
            loadTemplate(templateId) {
                unlayer.loadTemplate(templateId);
            },
            saveDesign(callback) {
                unlayer.saveDesign(callback);
            },
            exportHtml(callback) {
                unlayer.exportHtml(callback);
            },
        },
    }
</script>
