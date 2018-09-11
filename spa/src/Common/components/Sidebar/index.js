import React from 'react'
import {connect} from 'react-redux'
import {withRouter, Link} from 'react-router-dom'
import {TOGGLE_SIDEBAR} from "../../actions";
import selectors from "./selectors";
import translator from "../../../translations/translator";

const logoStyle = {width: '70px', height: '65px', overflow: 'hidden'}

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

        const {isAdmin, isPartner, partner} = this.props

        const hasAccountId = isPartner && partner.accountId

        const isOrdersEnabled = isAdmin || hasAccountId

        return <div className="sidebar">
            <div className="sidebar-inner">
                <div className="sidebar-logo">
                    <div className="peers ai-c fxw-nw">
                        <div className="peer peer-greed">
                            <div className="sidebar-link td-n">
                                <div className="peers ai-c fxw-nw">
                                    <div className="peer">
                                        <div className="logo" style={logoStyle} onClick={this.toggleSidebar}>
                                            <img src="/img/favicon/apple-touch-icon-72x72.png"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul className="sidebar-menu scrollable pos-r ps">
                    {isOrdersEnabled ? <li className="nav-item my-2">
                        <Link className="sidebar-link" to="/orders">
                            <span className="icon-holder"><i className="fa fa-lock"/></span>
                            <span className="title">{translator('navigation_orders')}</span>
                        </Link>
                    </li> : null}

                    {isAdmin && <li className="nav-item my-2">
                        <Link className="sidebar-link" to="/partners">
                            <span className="icon-holder"><i className="c-green-500 fa fa-child"/></span>
                            <span className="title">{translator('navigation_partners')}</span>
                        </Link>
                    </li>}

                    <li className="nav-item">
                        <Link className="sidebar-link my-2" to="/categories">
                            <span className="icon-holder"><i className="c-purple-500 fa fa-code-branch"/></span>
                            <span className="title">{translator('navigation_categories')}</span>
                        </Link>
                    </li>

                    {isPartner && <li className="nav-item my-2">
                        <Link className="sidebar-link" to="/profile">
                            <span className="icon-holder"><i className="fa fa-user-circle"/></span>
                            <span className="title">
                                {!hasAccountId ? <span><i className="fa fa-warning c-yellow-500"/>&nbsp;</span> : null}
                                {translator('navigation_profile')}
                            </span>
                        </Link>
                    </li>}
                </ul>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(Sidebar))