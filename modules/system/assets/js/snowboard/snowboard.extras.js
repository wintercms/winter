import Flash from './extras/Flash';
import FlashListener from './extras/FlashListener';
import FormValidation from './extras/FormValidation';
import Transition from './extras/Transition';
import AttachLoading from './extras/AttachLoading';
import StripeLoader from './extras/StripeLoader';
import StylesheetLoader from './extras/StylesheetLoader';

if (window.Snowboard === undefined) {
    throw new Error('Snowboard must be loaded in order to use the extra plugins.');
}

((Snowboard) => {
    Snowboard.addPlugin('extrasStyles', StylesheetLoader);
    Snowboard.addPlugin('transition', Transition);
    Snowboard.addPlugin('flash', Flash);
    Snowboard.addPlugin('flashListener', FlashListener);
    Snowboard.addPlugin('formValidation', FormValidation);
    Snowboard.addPlugin('attachLoading', AttachLoading);
    Snowboard.addPlugin('stripeLoader', StripeLoader);
})(window.Snowboard);
