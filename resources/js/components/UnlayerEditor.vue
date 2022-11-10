<template>
    <div :id=id class="flex h-full unlayerIframeContainer"></div>
</template>

<script>
    export default {
        props: {
            options: Object,
            projectId: {
                type: Number,
                required: true,
            },
            templateId: Number,
            tools: Object,
            appearance: Object,
            locale: String,
            displayMode: String,
            id: {
                type: String,
                default: 'editor',
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
                    id: this.id,
                };

                window.unlayer.init(config);

                this.$emit('load');
            },
            loadDesign(design) {
                window.unlayer.loadDesign(design);
            },
            loadTemplate(templateId) {
                window.unlayer.loadTemplate(templateId);
            },
            saveDesign(callback) {
                window.unlayer.saveDesign(callback);
            },
            exportHtml(callback) {
                window.unlayer.exportHtml(callback);
            },
        },
    }
</script>

<!--Please don't use "scoped" option: it doesn't work with responsible iframe containers-->
<style>
    .unlayerIframeContainer {
        position: relative;
    }

    .unlayerIframeContainer > iframe {
        border: 0;
        height: 100%;
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
    }
</style>
