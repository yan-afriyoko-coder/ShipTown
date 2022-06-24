import beep from "./beep";

function getValueOrDefault(value, defaultValue)
{
    return (value === undefined) || (value === null) ? defaultValue : value;
}

export default {
    mixins: [beep],

        methods: {
            copyToClipBoard(textToCopy){
                const tmpTextField = document.createElement("textarea")
                tmpTextField.textContent = textToCopy
                tmpTextField.setAttribute("style","position:absolute; right:200%;")
                document.body.appendChild(tmpTextField)
                tmpTextField.select()
                tmpTextField.setSelectionRange(0, 99999) /*For mobile devices*/
                document.execCommand("copy")
                tmpTextField.remove()
            },

            toNumberOrDash(value) {
                return this.dashIfZero(Number(value));
            },

            dashIfZero(value) {
                return (value && value !== 0) ? value : '-';
            },

            setFocus: function (input, autoSelectAll = false, hideOnScreenKeyboard = false, delay = 1) {
                setTimeout(() => {
                    if (hideOnScreenKeyboard) {
                        // this simple hack of setting focus when field is read only will
                        // prevent showing on screen keyboard on mobile devices
                        input.readOnly = true;
                    }

                    input.focus();

                    if (hideOnScreenKeyboard) {
                        input.readOnly = false;
                    }

                    if (autoSelectAll) {
                        document.execCommand('selectall');
                    }

                    }, delay);
            },

            setFocusElementById(delay = 1, elementId, autoSelectAll = false, hideOnScreenKeyboard = false) {
                setTimeout(() => {
                    this.setFocus(document.getElementById(elementId), autoSelectAll, hideOnScreenKeyboard);
                }, delay);
            },

            isMoreThanPercentageScrolled: function (percentage) {
                return document.documentElement.scrollTop + window.innerHeight > document.documentElement.offsetHeight * (percentage / 100);
            },

            getValueOrDefault,

            showException: function (exception, options) {
                const defaultOptions = {
                    closeOnClick: true,
                    timeout: 0,
                    buttons: [
                    {text: 'OK', action: null},
                    ]
                };

                this.$snotify.error(exception.response.data, options ?? defaultOptions);
            },

            showError: function (message, options) {
                const defaultOptions = {
                    closeOnClick: true,
                    timeout: 15 * 1000,
                    showProgressBar: true,
                    buttons: [
                        {text: 'OK', action: null},
                    ]
                };

                this.$snotify.error(message, options ?? defaultOptions);
            },

            notifyError: function (message, options = null) {
                this.showError(message, options);
                this.errorBeep();
            },

            notifySuccess: function (message = null, beep = true) {
                if (message) {
                    this.$snotify.success(message);
                }

                if (beep) {
                    this.beep();
                }
            },
    }
}
