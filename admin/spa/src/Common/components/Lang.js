import React from 'react'
import {connect} from 'react-redux'
import Cookie from 'js-cookie'

class Lang extends React.Component {

    setLocale = e => {
        if (AppParameters.locales.indexOf(e.target.value) === -1) return

        Cookie.set('locale', e.target.value)

        window.location.reload()
    }

    render() {

        if (AppParameters.locales.length < 2) return null

        return <select
            name="locale"
            className="form-control form-control-sm"
            value={AppParameters.locale}
            onChange={this.setLocale}>
            {AppParameters.locales.map((locale, key) =>
                <option key={key} value={locale}>{locale}</option>
            )}
        </select>
    }
}

export default connect()(Lang)
