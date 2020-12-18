function getValueOrDefault(value, defaultValue){
    return (value === undefined) || (value === null) ? defaultValue : value;
}

export default {
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
                console.log('setFocus');
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

        isBottomOfTheWindow: function () {
            return document.documentElement.scrollTop + window.innerHeight > document.documentElement.offsetHeight - 10;
        },

        getValueOrDefault,
    }
}
