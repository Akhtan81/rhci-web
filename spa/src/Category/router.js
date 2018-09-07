import React from 'react';
import {Route, Switch} from "react-router-dom";
import CategoryList from "./components/CategoryList";
import CategoryEdit from "../CategoryEdit/components";

const CategoryIndex = () => <div className="container-fluid">
    <div className="row">
        <div className="col">
            <Switch>
                <Route exact path='/categories' component={CategoryList}/>
                <Route exact path={'/categories/new'} component={CategoryEdit}/>
                <Route path={'/categories/:id'} component={CategoryEdit}/>
            </Switch>
        </div>
    </div>
</div>

export default CategoryIndex
