/* jshint esversion: 6, esversion: 6 */
(function (window) {

    const

        // Variable aliasing
        document = window.document,

        bindAllInputs = () => {
            document
                .querySelectorAll('[data-role~=time-entry-field]')
                .forEach(
                    (input) => {

                        input
                            .addEventListener(
                                'blur',
                                () => {

                                    const data = new FormData();
                                    data.append('field', input.getAttribute('name'));
                                    data.append('value', input.value);

                                    fetch(
                                        window.apiEndpointTimeEntry,
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
            bindAllInputs();
        }
    ;

    // Boot
    run();
}
(window));