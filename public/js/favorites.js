/* jshint esversion: 6, esversion: 6 */
(function (window) {

    const

        // Variable aliasing
        document = window.document,

        bindAllFavoritesButtons = () => {
            document
                .querySelectorAll('[data-role~=favorite]')
                .forEach(
                    (button) => {
                        const parent = button.closest('[data-object-id]');

                        if (!parent) {
                            console.warn('Warning: Could not find parent of button');
                            return;
                        }

                        button
                            .addEventListener(
                                'click',
                                () => {
                                    console.log(parent);
                                }
                            )
                        ;
                    }
                )
            ;
        },

        run = () => {
            bindAllFavoritesButtons();
        }
    ;

    // Boot
    run();
}
(window));