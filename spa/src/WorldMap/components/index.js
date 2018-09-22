import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import selectors from './selectors';
import GoogleMap from '../../Common/components/GoogleMap';
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";
import {SET_ACTIVE_MARKER} from "../actions";
import FetchItems from "../actions/FetchItems";

class WorldMap extends React.Component {

    componentWillMount() {

        setTitle(translator('navigation_world'))

        this.fetchItems()
    }

    fetchItems = () => {
        const {filter} = this.props.WorldMap

        this.props.dispatch(FetchItems(filter))
    }

    onMarkerOpen = id => {
        this.props.dispatch({
            type: SET_ACTIVE_MARKER,
            payload: id
        })
    }

    onMarkerClose = () => {
        this.props.dispatch({
            type: SET_ACTIVE_MARKER,
            payload: null
        })
    }

    render() {

        const {orders, activeMarker} = this.props.WorldMap

        return <GoogleMap
            activeMarker={activeMarker}
            onMarkerClose={this.onMarkerClose}
            onMarkerOpen={this.onMarkerOpen}
            markers={orders.map(order => ({
                order,
                lat: order.location.lat,
                lng: order.location.lng,
            }))}/>
    }
}

export default withRouter(connect(selectors)(WorldMap))
