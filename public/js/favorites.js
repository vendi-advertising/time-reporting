/* jshint esversion: 6, esversion: 6 */
(function (window) {

    const

        // Variable aliasing
        document = window.document,

        bindToggleFavorites = () => {
            document
                .querySelectorAll('[data-role~=only-show-favorites]')
                .forEach(
                    (button) => {
                        button
                            .addEventListener(
                                'click',
                                () => {
                                    const checked = button.checked;

                                    document
                                        .querySelectorAll('[data-role~=favorite]')
                                        .forEach(
                                            (favorite) => {
                                                const
                                                    details = favorite.closest('details'),
                                                    summary = details.querySelector('summary'),
                                                    childrenThatAreFavorites = details.querySelectorAll('[data-object-type~=project][data-is-favorite~=true]')
                                                ;

                                                details.classList.toggle('hidden', checked && summary.getAttribute('data-is-favorite') === 'false' && childrenThatAreFavorites.length === 0);

                                                if (checked) {
                                                    document
                                                        .querySelectorAll('details:not(.hidden)')
                                                        .forEach(
                                                            (details) => {
                                                                details.setAttribute('open', 'open');
                                                            }
                                                        )
                                                    ;
                                                }
                                            }
                                        )
                                    ;
                                }
                            )
                        ;
                    }
                )
            ;
        },

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
            bindToggleFavorites();
        }
    ;

    // Boot
    run();
}
(window));