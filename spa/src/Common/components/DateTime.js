import 'react-dates/initialize';
import React from 'react'

import {SingleDatePicker} from 'react-dates'

class Datetime extends React.Component {

    constructor() {
        super()
        this.setFocus = this.setFocus.bind(this)
    }

    state = {
        focused: false
    }

    setFocus({focused}) {
        this.setState({
            focused
        })
    }

    render() {
        return <SingleDatePicker
            showClearDate={true}
            displayFormat="DD.MM.YYYY"
            placeholder={"Select date..."}
            focused={this.state.focused}
            onFocusChange={this.setFocus}
            date={this.props.value}
            onDateChange={this.props.onChange}/>
    }
}

export default Datetime