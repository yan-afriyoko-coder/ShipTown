function getValueOrDefault(value, defaultValue){
    return (value === undefined) || (value === null) ? defaultValue : value;
}

export default {
    methods: {
        getValueOrDefault,
    }
}
