import { getLocale } from "matice";

export function changeTheme(theme) {
    let bodyClass = document.getElementById("body").classList;
    bodyClass.remove('theme-light', 'theme-dark');
    bodyClass.add('theme-' + theme);

    let htmlClass = document.getElementsByTagName("html")[0].classList;
    htmlClass.remove('light', 'dark');
    htmlClass.add(theme);
}

export function formattedAmount(amount) {
    return (amount / 100).toLocaleString(getLocale(), { minimumFractionDigits: 2});
}

export function  rangeOfDays(days) {
    return [...Array(days).keys()]
}

export function  isDarkMode() {
    const app = document.getElementsByTagName("html")[0];
    return app.classList.contains('dark');
}

export function  formatDate(dateString, opt) {
    const options = opt || { dateStyle: 'full' };
    const date = new Date(dateString);
    return date.toLocaleString(getLocale(), options);
}

export function formatDateTime(dateTimeString, opt) {
    const options = opt || { dateStyle: 'full', timeStyle: 'short' };
    const date = new Date(dateTimeString);
    return date.toLocaleString(getLocale(), options);
}
export function capitalize(value) {
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1);
}
export function tolower(value) {
    value = value.toString();
    return value.toLowerCase();
}

export function contains(needle, string) {
    return string.includes(needle);
}
export function redirect(link) {
    window.location.href = link;
}
export function truncate (text, num) {
    if (text.length > num) {
        return text.split("").slice(0, num).join("") + "...";
    }
    return text;
}

export default {
    changeTheme,
    isDarkMode,
    formattedAmount,
    rangeOfDays,
    truncate,
    redirect,
    contains,
    tolower,
    capitalize,
    formatDateTime,
    formatDate
}
