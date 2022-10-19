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
                                    const
                                        objectId = parent.getAttribute('data-object-id'),
                                        objectType = parent.getAttribute('data-object-type'),
                                        isFavorite = parent.getAttribute('data-is-favorite') === 'true'
                                    ;

                                    parent.setAttribute('data-is-favorite', isFavorite ? 'false' : 'true');

                                    const data = new FormData();
                                    data.append('object-type', objectType);
                                    data.append('object-id', objectId);
                                    data.append('action', isFavorite ? 'remove' : 'add');

                                    fetch(
                                        window.apiEndpointFavorites,
                                        {
                                            method: 'POST',
                                            body: data
                                        }
                                    )
                                        .then((response) => response.json())
                                        .then((data) => console.log(data));
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