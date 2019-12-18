/**
 * Example of simple plugin to change default font-size of a just added text content.
 */
(function(unlayer) {
    unlayer.plugins['change-fontsize'] = processNode;

    const supportedEventTypes = ['content:added'];
    const defaultFontSizeInPx = 16;

    /**
     * @typedef {UnlayerConfigNode} UnlayerConfigNode
     * @property {string} type
     * @property {Object} values
     * @property {Object} [changes]
     */

    /**
     * @param {UnlayerConfigNode} node
     * @param {string} eventType
     * @param {Object} changes
     * @returns {UnlayerConfigNode}
     */
    function processNode(node, eventType, changes) {
        if (!supportedEventTypes.includes(eventType)) {
            return node;
        }

        if (node.type === 'text') {
            return processAddedTextNode(node);
        }

        return node;
    }

    /**
     * @param {UnlayerConfigNode} node
     * @returns {UnlayerConfigNode}
     */
    function processAddedTextNode(node) {
        node.values.text = node.values.text.replace('font-size: 14px', `font-size: ${defaultFontSizeInPx}px`);
        return node;
    }
})(window.unlayer);
