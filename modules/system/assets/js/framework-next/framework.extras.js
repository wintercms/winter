import Flash from './extras/Flash';
import FlashListener from './extras/FlashListener';
import Transition from './extras/Transition';
import AttachLoading from './extras/AttachLoading';
import StripeLoader from './extras/StripeLoader';

((winter) => {
    winter.addModule('transition', Transition)
    winter.addModule('flash', Flash);
    winter.addModule('flashListener', FlashListener);
    winter.addModule('attachLoading', AttachLoading);
    winter.addModule('stripeLoader', StripeLoader);
})(window.winter);
