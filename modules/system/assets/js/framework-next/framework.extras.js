import Flash from './extras/Flash';
import FlashListener from './extras/FlashListener';
import Transition from './extras/Transition';
import AttachLoading from './extras/AttachLoading';

((winter) => {
    winter.addModule('transition', Transition)
    winter.addModule('flash', Flash);
    winter.addModule('flashListener', FlashListener);
    winter.addModule('attachLoading', AttachLoading);
})(window.winter);
