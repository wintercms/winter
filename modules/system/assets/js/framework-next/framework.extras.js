import Flash from './extras/Flash';
import Transition from './extras/Transition';

((winter) => {
    winter.addModule('transition', Transition)
    winter.addModule('flash', Flash);
})(window.winter);
