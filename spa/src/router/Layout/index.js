import React from 'react'
import {connect} from 'react-redux'
import Sidebar from '../../Common/components/Sidebar'
import Header from '../../Common/components/Header'

import {Route, Switch, Redirect, withRouter} from "react-router-dom";
import CategoryRouter from "../Categories/router";
import PartnerIndex from "../Partners/router";
import OrderIndex from "../Orders/router";
// import DistrictIndex from "../../../Districts/router";
import selectors from "./selectors";

class DashboardLayout extends React.Component {
    render() {

        const {isSidebarVisible} = this.props

        return <div className={isSidebarVisible ? 'is-collapsed' : ''}>
            <Sidebar/>
            <div className="page-container">
                <Header/>
                <main className="py-3 bgc-grey-100">
                    <Switch>
                        <Route path='/categories' component={CategoryRouter}/>
                        <Route path='/partners' component={PartnerIndex}/>
                        <Route path='/orders' exact component={OrderIndex}/>
                        {/*<Route path='/districts' exact component={DistrictIndex}/>*/}

                        <Redirect from='/' to="/orders" exact/>
                    </Switch>
                </main>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(DashboardLayout))