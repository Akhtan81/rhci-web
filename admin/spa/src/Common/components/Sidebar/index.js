import React from 'react'
import {connect} from 'react-redux'
import {Link, withRouter} from 'react-router-dom'
import {TOGGLE_SIDEBAR} from "../../actions";
import selectors from "./selectors";
import translator from "../../../translations/translator";

const logoStyle = {width: '70px', height: '65px', overflow: 'hidden'}

class Sidebar extends React.Component {

    state = {
        avatar: null
    }

    resetAvatar = () => {
        this.setState({
            avatar: "/img/logo.png"
        })
    }

    toggleSidebar = () => {
        this.props.dispatch({
            type: TOGGLE_SIDEBAR,
            payload: {
                isSidebarVisible: !this.props.isSidebarVisible
            }
        })
    }

    render() {

        const {isAdmin, isPartner, user} = this.props

        let avatar = this.state.avatar
        if (!avatar) {
            if (user.avatar) {
                avatar = user.avatar.url
            } else {
                avatar = "/img/logo.png"
            }
        }

        return <div className="sidebar">
            <div className="sidebar-inner">
                <div className="sidebar-logo">
                    <div className="peers ai-c fxw-nw">
                        <div className="peer peer-greed">
                            <div className="sidebar-link td-n">
                                <div className="peers ai-c fxw-nw">
                                    <div className="peer">
                                        <div className="logo" style={logoStyle} onClick={this.toggleSidebar}>
                                            <img src={avatar} onError={this.resetAvatar} className="img-fluid"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul className="sidebar-menu scrollable pos-r ps">

                    {(isAdmin || isPartner) && <li className="nav-item my-2">
                        <Link className="sidebar-link" to="/orders">
                            <span className="icon-holder"><i className="c-green-500 fa fa-cart-arrow-down"/></span>
                            <span className="title">{translator('navigation_orders')}</span>
                        </Link>
                    </li>}

                    {isAdmin && <li className="nav-item my-2">
                        <Link className="sidebar-link" to="/payments">
                            <span className="icon-holder"><i className="c-red-500 fa fa-money"/></span>
                            <span className="title">{translator('navigation_payments')}</span>
                        </Link>
                    </li>}

                    {isAdmin && <li className="nav-item my-2">
                        <Link className="sidebar-link" to="/world">
                            <span className="icon-holder"><i className="c-red-500 fa fa-map-marker"/></span>
                            <span className="title">{translator('navigation_world')}</span>
                        </Link>
                    </li>}

                    {isAdmin && <li className="nav-item my-2">
                        <Link className="sidebar-link" to="/partners">
                            <span className="icon-holder"><i className="c-blue-500 fa fa-child"/></span>
                            <span className="title">{translator('navigation_partners')}</span>
                        </Link>
                    </li>}

                    {(isAdmin || isPartner) && <li className="nav-item">
                        <Link className="sidebar-link my-2" to="/categories">
                            <span className="icon-holder"><i className="c-purple-500 fa fa-code-branch"/></span>
                            <span className="title">{translator('navigation_categories')}</span>
                        </Link>
                    </li>}

                    {isAdmin && <li className="nav-item my-2">
                        <Link className="sidebar-link" to="/units">
                            <span className="icon-holder"><i className="c-orange-500 fa fa-cubes"/></span>
                            <span className="title">{translator('navigation_units')}</span>
                        </Link>
                    </li>}

                    <li className="nav-item my-2">
                        <Link className="sidebar-link" to="/profile">
                            <span className="icon-holder"><i className="fa fa-user-circle"/></span>
                            <span className="title">{translator('navigation_profile')}</span>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(Sidebar))