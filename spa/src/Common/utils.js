import moment from 'moment-timezone'

export const numberFormat = number => (number / 100).toFixed(2)

export const dateFormat = (value, format = 'YYYY-MM-DD HH:mm:ss') => {
    if (!value) return null

    return moment(value, format)
        .tz(AppParameters.timezone)
        .format(format)
}
