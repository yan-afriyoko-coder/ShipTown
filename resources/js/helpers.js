let helpers = {
    isMoreThanPercentageScrolled(percentage) {
        return document.documentElement.scrollTop + window.innerHeight > document.documentElement.offsetHeight * (percentage / 100);
    }
}

export default helpers;
