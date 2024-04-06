window.cookieconsent.initialise({
    container: '#page',
    content: {
        message: TYPO3.lang['js.cookieconsent.message']['0']['target'],
        dismiss: TYPO3.lang['js.cookieconsent.dismiss']['0']['target'],
        link: TYPO3.lang['js.cookieconsent.learnMore']['0']['target'],
        href: privacylink,
        target: '_self'
    },
    elements: {
        messagelink: '<span id="cookieconsent:desc" class="cc-message">{{message}} <a aria-label="learn more about cookies" tabindex="0" class="cc-link" href="{{href}}" target="{{target}}">{{link}}</a></span>'
    }
});
