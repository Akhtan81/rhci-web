import React from 'react'
import {connect} from 'react-redux'
import Sidebar from '../Sidebar'
import Header from '../Header'

import {Route, Switch, Redirect, withRouter} from "react-router-dom";
import CategoryRouter from "../../../Category/router";
// import OrderIndex from "../../../Order/router";
// import PartnerIndex from "../../../Partner/router";
// import DistrictIndex from "../../../District/router";
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
                        {/*<Route path='/partners' exact component={PartnerIndex}/>*/}
                        {/*<Route path='/districts' exact component={DistrictIndex}/>*/}
                        {/*<Route path='/orders' exact component={OrderIndex}/>*/}

                        {/*<Redirect from='/' to="/orders" exact/>*/}
                    </Switch>
                </main>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(DashboardLayout))