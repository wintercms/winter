import Flash from './extras/Flash';
import FlashListener from './extras/FlashListener';
import Transition from './extras/Transition';
import AttachLoading from './extras/AttachLoading';
import StripeLoader from './extras/StripeLoader';

((Snowboard) => {
    Snowboard.addPlugin('transition', Transition)
    Snowboard.addPlugin('flash', Flash);
    Snowboard.addPlugin('flashListener', FlashListener);
    Snowboard.addPlugin('attachLoading', AttachLoading);
    Snowboard.addPlugin('stripeLoader', StripeLoader);
})(window.Snowboard);
