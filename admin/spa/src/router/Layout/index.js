import React from 'react'
import {connect} from 'react-redux'
import Sidebar from '../../Common/components/Sidebar'
import Header from '../../Common/components/Header'

import {Redirect, Route, Switch, withRouter} from "react-router-dom";
import CategoryRouter from "../Categories/router";
import UnitsRouter from "../Units/router";
import PartnerIndex from "../Partners/router";
import OrderIndex from "../Orders/router";
import ProfileIndex from "../Profile/router";
import WorldMap from "../../WorldMap/components"
import Payment from "../Payment/router"
import selectors from "./selectors";

class DashboardLayout extends React.Component {
    render() {

        const {isSidebarVisible} = this.props

        return <div className={isSidebarVisible ? 'is-collapsed' : ''}>
            <Sidebar/>
            <div className="page-container">
                <Header/>
                <main>
                    <Switch>
                        <Route path='/categories' component={CategoryRouter}/>
                        <Route path='/partners' component={PartnerIndex}/>
                        <Route path='/orders' component={OrderIndex}/>
                        <Route path='/profile' component={ProfileIndex}/>
                        <Route path='/world' component={WorldMap}/>
                        <Route path='/units' component={UnitsRouter}/>
                        <Route path='/payments' component={Payment}/>

                        <Redirect from='/' to="/orders" exact/>
                    </Switch>
                </main>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(DashboardLayout))