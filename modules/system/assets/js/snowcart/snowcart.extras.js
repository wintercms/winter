import Flash from './extras/Flash';
import FlashListener from './extras/FlashListener';
import Transition from './extras/Transition';
import AttachLoading from './extras/AttachLoading';
import StripeLoader from './extras/StripeLoader';

((snowcart) => {
    snowcart.addModule('transition', Transition)
    snowcart.addModule('flash', Flash);
    snowcart.addModule('flashListener', FlashListener);
    snowcart.addModule('attachLoading', AttachLoading);
    snowcart.addModule('stripeLoader', StripeLoader);
})(window.snowcart);
