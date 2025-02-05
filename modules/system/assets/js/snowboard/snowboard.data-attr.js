import AttributeRequest from './ajax/handlers/AttributeRequest';

if (window.Snowboard === undefined) {
    throw new Error('Snowboard must be loaded in order to use the HTML data attribute AJAX request feature.');
}

((Snowboard) => {
    Snowboard.addPlugin('attributeRequest', AttributeRequest);
})(window.Snowboard);
