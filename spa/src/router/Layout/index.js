import React from 'react'
import {connect} from 'react-redux'
import Sidebar from '../../Common/components/Sidebar'
import Header from '../../Common/components/Header'

import {Route, Switch, Redirect, withRouter} from "react-router-dom";
import CategoryRouter from "../Categories/router";
import PartnerIndex from "../Partners/router";
import OrderIndex from "../Orders/router";
import ProfileIndex from "../Profile/router";
import selectors from "./selectors";

class DashboardLayout extends React.Component {
    render() {

        const {isSidebarVisible} = this.props

        return <div className={isSidebarVisible ? 'is-collapsed' : ''}>
            <Sidebar/>
            <div className="page-container">
                <Header/>
                <main className="py-3">
                    <Switch>
                        <Route path='/categories' component={CategoryRouter}/>
                        <Route path='/partners' component={PartnerIndex}/>
                        <Route path='/orders' component={OrderIndex}/>
                        <Route path='/profile' component={ProfileIndex}/>

                        <Redirect from='/' to="/orders" exact/>
                    </Switch>
                </main>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(DashboardLayout))