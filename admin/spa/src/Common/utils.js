import moment from 'moment-timezone'

const defaultFormat = 'YYYY-MM-DD HH:mm:ss'

const formats = {
    en: 'YY-MM-DD HH:mm:ss',
    ru: 'DD.MM.YY HH:mm:ss',
}

export const objectValues = (obj) => obj ? Object.keys(obj).map(i => obj[i]) : []

export const setTitle = value => document.title = value

export const priceFormat = (number = 0) => numberFormat(number / 100)

export const numberFormat = number => number.toFixed(2)

export const dateFormat = (value, format = null) => {
    if (!value) return null

    if (!format) {
        format = formats[AppParameters.locale]
    }

    return moment(value, defaultFormat)
    //.tz(AppParameters.timezone)
        .format(format)
}

export const cid = (length = 5) => Math.random().toString(36).replace(/[^a-z0-9]+/g, '').substr(0, length);
