export default {
    get(target, prop, receiver) {
        if (typeof prop === 'string') {
            const propLower = prop.toLowerCase();

            if (target.hasPlugin(propLower)) {
                return (...params) => Reflect.get(target, 'plugins')[propLower].getInstance(...params);
            }
        }

        return Reflect.get(target, prop, receiver);
    },

    has(target, prop) {
        if (typeof prop === 'string') {
            const propLower = prop.toLowerCase();

            if (target.hasPlugin(propLower)) {
                return true;
            }
        }

        return Reflect.has(target, prop);
    },
};
