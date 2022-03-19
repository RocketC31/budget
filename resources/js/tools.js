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
    }
}
