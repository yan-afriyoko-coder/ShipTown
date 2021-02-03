import beep from "./beep";

function getValueOrDefault(value, defaultValue){
    return (value === undefined) || (value === null) ? defaultValue : value;
}

export default {
    mixins: [beep],

    methods: {
        toNumberOrDash(value) {
            return this.dashIfZero(Number(value));
        },

        dashIfZero(value) {
            return (value && value !== 0) ? value : '-';
        },

        setFocus: function (input, autoSelectAll = false, hideOnScreenKeyboard = false) {
            setTimeout(
            () => {
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
            },
            1
            );
        },

        isMoreThanPercentageScrolled: function (percentage) {
            return document.documentElement.scrollTop + window.innerHeight > document.documentElement.offsetHeight * (percentage / 100);
        },

        getValueOrDefault,

        notifyError: function (message) {
            this.$snotify.error(message, {timeout: 5000});
            this.errorBeep();
        },

        notifySuccess: function (message = null, beep = true) {
            if(message) {
                this.$snotify.success(message);
            }

            if (beep) {
                this.beep();
            }
        },
    }
}
