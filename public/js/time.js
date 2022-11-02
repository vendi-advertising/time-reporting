/* jshint esversion: 6, esversion: 6 */
(function (window) {

    const

        // Variable aliasing
        document = window.document,

        setupHeaders = () => {
            document
                .querySelectorAll('details')
                .forEach(
                    (detail) => {

                        detail
                            .querySelectorAll('summary .time-entry-header')
                            .forEach(
                                (header) => {

                                    let value = 0;

                                    detail
                                        .querySelectorAll(`input[data-date="${header.getAttribute('data-date')}"]`)
                                        .forEach(
                                            (thing) => {
                                                if (thing.value) {
                                                    value += parseFloat(thing.value);
                                                }
                                            }
                                        )
                                    ;

                                    header.innerHTML = value;
                                }
                            )
                        ;
                    }
                )
            ;
        },

        bindAllInputs = () => {
            document
                .querySelectorAll('[data-role~=time-entry-field], [data-role~=time-entry-comment]')
                .forEach(
                    (input) => {

                        input
                            .addEventListener(
                                'blur',
                                () => {

                                    const data = new FormData();
                                    data.append('field', input.getAttribute('name'));
                                    data.append('value', input.value);

                                    const endpoint = input.getAttribute('data-role').includes('time-entry-field') ? window.apiEndpointTimeEntry : window.apiEndpointComment;

                                    fetch(
                                        endpoint,
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
            setupHeaders();
        }
    ;

    // Boot
    run();
}
(window));