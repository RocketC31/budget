module.exports = {
    formattedAmount(amount) {
        return (amount / 100).toLocaleString(undefined, { minimumFractionDigits: 2});
    },
    rangeOfDays(days) {
        return [...Array(days).keys()]
    },
    isDarkMode() {
        const app = document.getElementsByTagName("html")[0];
        return app.classList.contains('dark');
    },
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    },
    capitalize(value) {
        value = value.toString();

        return value.charAt(0).toUpperCase() + value.slice(1);
    },
    tolower(value) {
        value = value.toString();
        return value.toLowerCase();
    },
    changeTheme(theme) {
        let bodyClass = document.getElementById("body").classList;
        bodyClass.remove('theme-light', 'theme-dark');
        bodyClass.add('theme-' + theme);

        let htmlClass = document.getElementsByTagName("html")[0].classList;
        htmlClass.remove('light', 'dark');
        htmlClass.add(theme);
    },
    contains(needle, string) {
        return string.includes(needle);
    }
}
