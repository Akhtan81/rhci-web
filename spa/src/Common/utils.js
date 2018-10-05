import moment from 'moment-timezone'

export const objectValues = (obj) => obj ? Object.keys(obj).map(i => obj[i]) : []

export const setTitle = value => document.title = value

export const priceFormat = number => numberFormat(number / 100)

export const numberFormat = number => number.toFixed(2)

export const dateFormat = (value, format = 'YYYY-MM-DD HH:mm:ss') => {
    if (!value) return null

    return moment(value, format)
        //.tz(AppParameters.timezone)
        .format(format)
}

export const cid = (length = 5) => Math.random().toString(36).replace(/[^a-z0-9]+/g, '').substr(0, length);
