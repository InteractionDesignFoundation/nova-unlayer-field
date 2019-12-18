(function(unlayer) {
    unlayer.plugins['example'] = process;

    const supportedEventTypes = [
        'content:added',
        'content:modified',
        'content:removed',
        'row:added',
        'row:removed',
        'row:removed',
        'body:modified',
    ];

    /**
     * @param {string} designAsString
     * @param {string} type
     * @returns {string}
     */
    function process(designAsString, type) {
        if (!supportedEventTypes.includes(type)) {
            return designAsString;
        }

        designAsString = designAsString.replace('example', 'It works! ❤️');

        return designAsString;
    }
})(window.unlayer);
