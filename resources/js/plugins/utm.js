/**
 * Example of a plugin to add UTM parameters to internal links.
 */
(function (unlayer, Nova, originOfInternalLinks) {
    unlayer.plugins['utm'] = processNode;

    const supportedEventTypes = ['content:added', 'content:modified'];

    /**
     * @typedef {object} UnlayerNameValueChange
     * @property {string} name
     * @property {string} value
     */

    /**
     * @typedef {object} UnlayerSingleChange
     * @property {string} name
     * @property {object} attrs
     * @property {object} values
     */

    /**
     * @typedef {object} UnlayerChanges
     * @property {string} name
     * @property {UnlayerSingleChange} value
     */

    /**
     * @typedef {object} UnlayerConfigNode
     * @property {string} type
     * @property {object} values
     * @property {object} [changes]
     */

    /**
     * @public
     * @param {UnlayerConfigNode} node
     * @param {string} eventType
     * @param {UnlayerChanges} changes
     * @returns {UnlayerConfigNode}
     */
    function processNode(node, eventType, changes) {
        if (!supportedEventTypes.includes(eventType)) {
            return node;
        }

        if (eventType === 'content:added') {
            if (node.type === 'button') {
                return processAddedButtonNode(node);
            }

            if (node.type === 'html') {
                return processAddedHtmlNode(node);
            }

            if (node.type === 'text') {
                return processAddedTextNode(node);
            }
        }

        if (eventType === 'content:modified') {
            if (node.type === 'button') {
                return processModifiedButtonNode(node, changes);
            }

            if (node.type === 'html') {
                return processModifiedHtmlNode(node, changes);
            }

            if (node.type === 'text') {
                return processModifiedTextNode(node, changes);
            }
        }

        return node;
    }

    /**
     * @private
     * @param {UnlayerConfigNode} node
     * @param {UnlayerChanges} changes
     * @returns {UnlayerConfigNode}
     */
    function processModifiedButtonNode(node, changes) {
        if (changes.name !== 'href') {
            return node;
        }

        /** @type {string} */
        const url = changes.value.values.href;
        if (!url || !isInternalUrl(url)) {
            return node;
        }

        node.values.href.values.href = addUtmParametersToUrl(url, getUtmParameters());
        return node;
    }

    /**
     * @private
     * @param {UnlayerConfigNode} node
     * @returns {UnlayerConfigNode}
     */
    function processAddedHtmlNode(node) {
        node.values.text = updateHtmlToAddUtmParametersToInternalLinks(node.values.html, getUtmParameters());
        return node;
    }

    /**
     * @private
     * @param {UnlayerConfigNode} node
     * @param {UnlayerChanges} changes
     * @returns {UnlayerConfigNode}
     */
    function processModifiedHtmlNode(node, changes) {
        if (changes.name !== 'html') {
            return node;
        }

        if (!changes.value.includes(originOfInternalLinks) && !node.values.html.includes(originOfInternalLinks)) {
            return node;
        }

        node.values.html = updateHtmlToAddUtmParametersToInternalLinks(changes.value, getUtmParameters());
        return node;
    }

    /**
     * @private
     * @param {UnlayerConfigNode} node
     * @returns {UnlayerConfigNode}
     */
    function processAddedTextNode(node) {
        node.values.text = updateHtmlToAddUtmParametersToInternalLinks(node.values.text, getUtmParameters());
        return node;
    }

    /**
     * @private
     * @param {UnlayerConfigNode} node
     * @param {UnlayerNameValueChange} changes
     * @returns {UnlayerConfigNode}
     */
    function processModifiedTextNode(node, changes) {
        if (changes.name !== 'text') {
            return node;
        }

        if (!changes.value.includes(originOfInternalLinks) && !node.values.text.includes(originOfInternalLinks)) {
            return node;
        }

        node.values.text = updateHtmlToAddUtmParametersToInternalLinks(changes.value, getUtmParameters());
        return node;
    }

    /**
     * @private
     * @param {UnlayerConfigNode} node
     * @returns {UnlayerConfigNode}
     */
    function processAddedButtonNode(node) {
        /** @type {string} */
        const url = node.values.href.values.href;
        if (!url || !isInternalUrl(url)) {
            return node;
        }

        node.values.href.values.href = addUtmParametersToUrl(url, getUtmParameters());
        return node;
    }

    /**
     * @private
     * @returns {URLSearchParams}
     */
    function getUtmParameters() {
        const currentDate = new Date().toISOString().slice(0, 10); // example: "2017-02-01"

        const segment = document.querySelector('#segment').value;
        if (!segment) {
            Nova.app.$toasted.info(
                '"Segment" input is empty, can not set "utm_campaign" parameter. Please select a group and edit this link again.',
                {type: 'error'}
            );
        }

        const urlSearchParameters = new URLSearchParams();
        urlSearchParameters.set('utm_source', 'newsletter');
        urlSearchParameters.set('utm_medium', 'email');
        urlSearchParameters.set('utm_content', `letter-${currentDate}`);
        if (segment) {
            urlSearchParameters.set('utm_campaign', segment);
        }

        return urlSearchParameters;
    }

    /**
     * @private
     * @param {string} url
     * @param {URLSearchParams} utmParameters
     * @returns {string}
     */
    function addUtmParametersToUrl(url, utmParameters) {
        const urlObject = new URL(url);
        let parameters = urlObject.searchParams;
        for (const [key, value] of utmParameters) {
            parameters.set(key, value);
        }

        return urlObject.toString();
    }

    /**
     * @private
     * @param {string} url
     * @returns {boolean}
     */
    function isInternalUrl(url) {
        return url.startsWith(originOfInternalLinks);
    }

    /**
     * Adds UTM parameters to all internal links.
     * At this point for 'utm_content' parameter we use stub value
     * that should be updated right before submitting the email
     *
     * @private
     * @param {string} htmlContent - html generated by RTE
     * @param {URLSearchParams} utmParameters - An object with UTM tags
     * @returns {string} html code with UTM parameters
     */
    function updateHtmlToAddUtmParametersToInternalLinks(htmlContent, utmParameters) {
        /**
         * @param {string} fullMatchedString
         * @param {string} url
         * @param {string} quoteType
         * @returns {string}
         */
        function urlReplacer(fullMatchedString, url, quoteType) {
            const urlWithUtmParameters = addUtmParametersToUrl(url, utmParameters);
            return ` href=${quoteType}${urlWithUtmParameters}${quoteType}`;
        }

        const internalOriginEscaped = escapeStringForRegExp(originOfInternalLinks);
        const regExp = new RegExp(`href=["'](${internalOriginEscaped}.*?)(["'])`, 'giu');

        return htmlContent.replace(regExp, urlReplacer);
    }

    /**
     * @private
     * @param {string} string
     * @returns {string}
     */
    function escapeStringForRegExp(string) {
        return string.replaceAll(/[$()*+./?[\\\]^{|}]/g, '\\$&'); // $& means the whole matched string
    }
})(window.unlayer, window.Nova, location.origin);
