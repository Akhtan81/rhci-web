import React from 'react'
import {connect} from 'react-redux'
import Sidebar from '../Sidebar'

import {Route, Switch, Redirect} from "react-router-dom";
import CategoryIndex from "../../../Category/components";
import OrderIndex from "../../../Order/components";
import PartnerIndex from "../../../Partner/components";
import DistrictIndex from "../../../District/components";
import selectors from "./selectors";

class DashboardLayout extends React.Component {
    render() {

        const {isSidebarVisible} = this.props

        return <div className={isSidebarVisible ? 'is-collapsed' : ''}>
            <Sidebar/>
            <div className="page-container">
                <main className="py-3 bgc-grey-100">
                    <Switch>
                        <Route path='/partners' exact component={PartnerIndex}/>
                        <Route path='/partners/:id' exact component={PartnerIndex}/>
                        <Route path='/partners/new' exact component={PartnerIndex}/>

                        <Route path='/categories' exact component={CategoryIndex}/>
                        <Route path='/categories/:id' exact component={CategoryIndex}/>
                        <Route path='/categories/new' exact component={CategoryIndex}/>

                        <Route path='/districts' exact component={DistrictIndex}/>
                        <Route path='/districts/:id' exact component={DistrictIndex}/>
                        <Route path='/districts/new' exact component={DistrictIndex}/>

                        <Route path='/orders' exact component={OrderIndex}/>
                        <Route path='/orders/:id' exact component={OrderIndex}/>

                        <Redirect from='/' to="/orders" exact/>
                    </Switch>
                </main>
            </div>
        </div>
    }
}

export default connect(selectors)(DashboardLayout)