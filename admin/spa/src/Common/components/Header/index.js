import React from 'react'
import {connect} from 'react-redux'
import {Link} from 'react-router-dom'
import {TOGGLE_SIDEBAR} from "../../actions";
import selectors from "./selectors";
import translator from "../../../translations/translator";
import Lang from "../Lang";

class Sidebar extends React.Component {

    toggleSidebar = () => {
        this.props.dispatch({
            type: TOGGLE_SIDEBAR,
            payload: {
                isSidebarVisible: !this.props.isSidebarVisible
            }
        })
    }

    render() {

        const {user} = this.props

        return <div className="header navbar w-100 position-relative">
            <div className="header-container">
                <ul className="nav-left">
                    <li>
                        <a className="sidebar-toggle"
                           onClick={this.toggleSidebar}>
                            <i className="ti-menu"/>
                        </a>
                    </li>
                </ul>
                <ul className="nav-right">
                    <li>
                        <Link to={'/profile'} className="peers">
                            <span className="peer text-truncate">{user.name || ''}</span>
                        </Link>
                    </li>
                    <li>
                        <div className="mt-3">
                            <Lang/>
                        </div>
                    </li>
                    <li>
                        <a className="text-muted" href="/logout" title={translator('logout')}>
                            <i className="fa fa-sign-out"/>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    }
}

export default connect(selectors)(Sidebar)
