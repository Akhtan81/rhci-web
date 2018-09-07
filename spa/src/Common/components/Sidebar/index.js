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

        const {isAdmin, isPartner} = this.props

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
                    <li className="nav-item mT-30 active">
                        <Link className="sidebar-link" to="/orders">
                            <span className="icon-holder"><i className="c-red-500 fa fa-cart-arrow-down"/></span>
                            <span className="title">{translator('navigation_orders')}</span>
                        </Link>
                    </li>

                    {isAdmin && <li className="nav-item">
                        <Link className="sidebar-link" to="/partners">
                            <span className="icon-holder"><i className="c-green-500 fa fa-child"/></span>
                            <span className="title">{translator('navigation_partners')}</span>
                        </Link>
                    </li>}

                    {isAdmin && <li className="nav-item">
                        <Link className="sidebar-link" to="/categories">
                            <span className="icon-holder"><i className="c-purple-500 fa fa-code-branch"/></span>
                            <span className="title">{translator('navigation_categories')}</span>
                        </Link>
                    </li>}

                    {isAdmin && <li className="nav-item">
                        <Link className="sidebar-link" to="/districts">
                            <span className="icon-holder"><i className="c-blue-500 fa fa-map-marker"/></span>
                            <span className="title">{translator('navigation_districts')}</span>
                        </Link>
                    </li>}

                    {isPartner && <li className="nav-item">
                        <Link className="sidebar-link" to="/profile">
                            <span className="icon-holder"><i className="fa fa-user"/></span>
                            <span className="title">{translator('navigation_stripe')}</span>
                        </Link>
                    </li>}
                </ul>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(Sidebar))