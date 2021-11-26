const transition = (element, transition, callback) => {
    if (element instanceof HTMLElement === false && typeof element['hasClass'] !== 'function') {
        throw new Error('"element" must be a HTMLElement or a Cash.js object');
    }

    if (typeof transition !== 'string') {
        throw new Error('"transition" must be a string');
    }

    if (callback && typeof callback !== 'function') {
        throw new Error('"callback" must be a valid callback function.');
    }

    // Class set
    const eventClass = {
        enter: `${transition}`,
        enterActive: `${transition}-active`,
        enterTo: `${transition}-to`,
    };

    // Reset classes
    Object.values(eventClass).forEach((eventClass) => {
        element.classList.remove(eventClass);
    });

    // Start transition
    [eventClass.enter, eventClass.enterActive].forEach((eventClass) => {
        element.classList.add(eventClass);
    });

    window.requestAnimationFrame(() => {
        if (window.getComputedStyle(element).transition || window.getComputedStyle(element)['transition-property']) {
            element.addEventListener('transitionend', () => {
                [eventClass.enterTo, eventClass.enterActive].forEach((eventClass) => {
                    element.classList.remove(eventClass);
                });

                if (callback) {
                    callback.apply(element);
                }
            }, {
                once: true
            });
            window.requestAnimationFrame(() => {
                element.classList.remove(eventClass.enter);
                element.classList.add(eventClass.enterTo);
            });
        } else {
            Object.values(eventClass).forEach((eventClass) => {
                element.classList.remove(eventClass);
            });

            if (callback) {
                callback.apply(element);
            }
        }
    });
};

export default transition;
