function getValueOrDefault(value, defaultValue){
    return (value === undefined) || (value === null) ? defaultValue : value;
}

export default {
    methods: {
        dashIfZero(value) {
            return value === 0 ? '-' : value;
        },
        getValueOrDefault,
    }
}
