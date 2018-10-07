import 'react-datetime/css/react-datetime.css'

import React from 'react'
import PropType from 'prop-types'

import DateTime from 'react-datetime'
import translator from '../../translations/translator'

class DateTimeWrapper extends React.Component {

    onChange = e => {
        const value = typeof e === 'string' ? null : e.format('YYYY-MM-DD HH:mm:00')
        this.props.onChange(value)
    }

    render() {
        return <DateTime
            closeOnSelect={true}
            viewMode="time"
            inputProps={{placeholder: translator('select_date')}}
            timeFormat={'HH:mm'}
            dateFormat={'YYYY-MM-DD'}
            {...this.props}
            value={this.props.value}
            onChange={this.onChange}/>
    }
}

DateTimeWrapper.propTypes = {
    value: PropType.any,
    onChange: PropType.func.isRequired,
}

export default DateTimeWrapper