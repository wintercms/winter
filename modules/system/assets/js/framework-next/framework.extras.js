import Flash from './extras/Flash';
import FlashListener from './extras/FlashListener';
import Transition from './extras/Transition';

((winter) => {
    winter.addModule('transition', Transition)
    winter.addModule('flash', Flash);
    winter.addModule('flashListener', FlashListener);
})(window.winter);
