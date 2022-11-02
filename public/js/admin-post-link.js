/* jshint esversion: 6, esversion: 6 */
(function (window) {

    const

        // Variable aliasing
        document = window.document,

        disableLink = (link) => {
            link.setAttribute('href', '#');
            link.addEventListener('click', () => window.alert('This link has been disabled for security reasons'));
        },

        setupModal = (link) => {
            link
                .addEventListener(
                    'click',
                    () => {
                        const
                            dialog = document.createElement('dialog'),
                            headerText = link.hasAttribute('data-modal-title') ? link.getAttribute('data-modal-title') : 'Missing title',
                            bodyText = link.hasAttribute('data-modal-content') ? link.getAttribute('data-modal-content') : 'Missing content',
                            buttonText = link.hasAttribute('data-modal-button') ? link.getAttribute('data-modal-button') : 'Missing button',
                            header = document.createElement('div'),
                            body = document.createElement('div'),
                            footer = document.createElement('div'),
                            cancelButton = document.createElement('button'),
                            submitButton = document.createElement('button'),
                            form = document.createElement('form'),
                            formAction = link.getAttribute('data-link'),
                            formCsrfValue = link.getAttribute('data-csrf'),
                            formCsrfId = link.getAttribute('data-csrf-id'),
                            inputCsrf = document.createElement('input'),
                            inputCsrfId = document.createElement('input')
                        ;

                        inputCsrfId.type = 'hidden';
                        inputCsrfId.name = 'token-id';
                        inputCsrfId.value = formCsrfId;

                        inputCsrf.type = 'hidden';
                        inputCsrf.name = 'token';
                        inputCsrf.value = formCsrfValue;

                        form.method = 'post';
                        form.action = formAction;
                        form.append(inputCsrf, inputCsrfId);

                        dialog.classList.add('confirmation')
                        header.classList.add('header');
                        body.classList.add('body');
                        footer.classList.add('footer');

                        cancelButton.append('Cancel');
                        submitButton.append(buttonText)
                        submitButton.type = 'submit';

                        form.append(cancelButton, submitButton);

                        header.append(headerText);
                        body.append(bodyText);
                        footer.append(form);

                        dialog.append(header, body, footer);

                        ['close', 'cancel']
                            .forEach(
                                (eventName) => {
                                    dialog
                                        .addEventListener(
                                            eventName,
                                            () => {
                                                dialog.parentNode.removeChild(dialog);
                                            }
                                        )
                                    ;
                                }
                            )
                        ;

                        cancelButton
                            .addEventListener(
                                'click',
                                (evt) => {
                                    evt.preventDefault();
                                    dialog.close();
                                }
                            )
                        ;

                        document.body.append(dialog);

                        dialog.showModal();
                    }
                )
            ;
        },

        run = () => {
            document
                .querySelectorAll('[data-role~=post-link]')
                .forEach(
                    (link) => {
                        if (link.hasAttribute('href')) {
                            console.warn('Post links cannot have hrefs, it has been disabled')
                            disableLink(link);
                            return;
                        }

                        const anyMissing = ['data-link', 'data-csrf', 'data-csrf-id']
                            .some(
                                (attribute) => {
                                    if (!link.hasAttribute(attribute) || !link.getAttribute(attribute)) {
                                        console.warn('Post links missing attribute: ' + attribute);
                                        return true;
                                    }
                                }
                            )
                        ;

                        if (anyMissing) {
                            disableLink(link);
                        }

                        if (link.getAttribute('data-role').includes('with-modal')) {
                            setupModal(link);
                            return;
                        }

                        link
                            .addEventListener(
                                'click',
                                () => {
                                    window.alert('Um... I\m not really sure what I\'m supposed to do?');
                                }
                            )
                        ;
                    }
                )
            ;
        }
    ;

    // Boot
    run();
}
(window));