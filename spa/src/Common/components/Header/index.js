import React from 'react'
import {connect} from 'react-redux'
import {TOGGLE_SIDEBAR} from "../../actions";
import selectors from "./selectors";

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
        return <div className="header navbar w-100" style={{position: 'initial'}}>
            <div className="header-container">
                <ul className="nav-left">
                    <li>
                        <a className="sidebar-toggle"
                           onClick={this.toggleSidebar}>
                            <i className="ti-menu"/>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    }
}

export default connect(selectors)(Sidebar)