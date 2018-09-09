import React from 'react'
import {connect} from 'react-redux'
import {TOGGLE_SIDEBAR} from "../../actions";
import selectors from "./selectors";
import translator from "../../../translations/translator";

const iconStyle = {fontSize: '10px'}
const aStyle = {lineHeight: 'initial'}

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

        const {avatar, isAuthenticated, name} = this.props

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
                <ul className="nav-right">
                    <li>
                        <a className="peers pt-3" style={aStyle}>
                            {avatar
                                ? <div className="peer mR-10">
                                    <img className="w-2r bdrs-50p" src={avatar.url}/>
                                </div>
                                : null}

                            <div className="peer text-truncate">
                                <span>{name}</span><br/>

                                {isAuthenticated
                                    ? <small className="text-muted">
                                        <i className={"fa fa-circle c-green-500"} style={iconStyle}/>
                                        &nbsp;{translator('online')}
                                    </small>
                                    : <small className="text-muted">
                                        <i className={"fa fa-circle c-red-500"} style={iconStyle}/>
                                        &nbsp;{translator('offline')}
                                    </small>
                                }

                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    }
}

export default connect(selectors)(Sidebar)