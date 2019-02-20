import moment from 'moment-timezone'

const defaultFormat = 'YYYY-MM-DD HH:mm:ss'

const formats = {
    en: 'YYYY-MM-DD HH:mm:ss',
    ru: 'DD.MM.YYYY HH:mm:ss',
    kz: 'DD.MM.YYYY HH:mm:ss',
}

export const objectValues = (obj) => obj ? Object.keys(obj).map(i => obj[i]) : []

export const setTitle = value => document.title = value

export const priceFormat = (number = 0) => numberFormat(number / 100)

export const numberFormat = number => number.toFixed(2)

export const dateFormat = (value, format = null) => {
    if (!value) return null

    if (!format) {
        if (formats[AppParameters.locale] !== undefined) {
            format = formats[AppParameters.locale]
        } else {
            format = formats.en
        }
    }

    return moment(value, defaultFormat)
    //.tz(AppParameters.timezone)
        .format(format)
}

export const cid = (length = 5) => Math.random().toString(36).replace(/[^a-z0-9]+/g, '').substr(0, length);

const windowWidth = 1920;
const mobileWidthMax = 840;

export const isMobile = () => getWindowWidth() <= mobileWidthMax;

export const getWindowWidth = () => {
    if (typeof window === 'undefined') return windowWidth;

    return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
};
