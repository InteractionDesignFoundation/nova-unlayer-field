<template>
    <panel-item :field="field">
        <template slot="value">
            <div class="overflow-hidden">
                <iframe :id="iframeId" sandbox="allow-scripts allow-same-origin" importance="low" width="100%"></iframe>
            </div>
        </template>
    </panel-item>
</template>

<script>
    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],

        beforeCreate() {
            const uniqueId = Math.random().toString(36).slice(-5);
            this.iframeId = `previewUnlayerHtmlIframe-${uniqueId}`;
        },

        mounted() {
            let iframe = document.getElementById(this.iframeId);
            this.setIframeContent(iframe, this.field.html);
            this.resizeIFrameToFitContent(iframe)
        },

        methods: {
            /**
             * @param {HTMLIFrameElement} iframe
             * @param {string} htmlContent
             */
            setIframeContent: function(iframe, htmlContent) {
                const container = document.createElement('div');
                container.innerHTML = htmlContent;
                iframe.contentWindow.document.body.appendChild(container);
            },
            /**
             * @param {HTMLIFrameElement} iframe
             */
            resizeIFrameToFitContent: function (iframe) {
                iframe.height = iframe.contentWindow.document.body.scrollHeight;
            }
        },
    }
</script>
