import Module from './Module';

/**
 * Singleton module abstract.
 *
 * This is a special definition class that the Winter framework will use to interpret the current module as a
 * "singleton". This will ensure that only one instance of the module class is used across the board.
 *
 * Singletons are initialised on the "domReady" event.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Singleton extends Module {
}

