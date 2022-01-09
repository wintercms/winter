import Flash from './extras/Flash';
import FlashListener from './extras/FlashListener';
import Transition from './extras/Transition';
import AttachLoading from './extras/AttachLoading';
import StripeLoader from './extras/StripeLoader';

((snowboard) => {
    snowboard.addModule('transition', Transition)
    snowboard.addModule('flash', Flash);
    snowboard.addModule('flashListener', FlashListener);
    snowboard.addModule('attachLoading', AttachLoading);
    snowboard.addModule('stripeLoader', StripeLoader);
})(window.snowboard);
