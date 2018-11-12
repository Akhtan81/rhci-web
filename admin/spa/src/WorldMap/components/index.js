import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import selectors from './selectors';
import GoogleMap from '../../Common/components/GoogleMap';
import translator from '../../translations/translator';
import {numberFormat, setTitle} from "../../Common/utils";
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

        const total = orders.length
        const count1 = orders.filter(order => order.type === 'recycling').length
        const count2 = orders.filter(order => order.type === 'junk_removal').length
        const count3 = orders.filter(order => order.type === 'shredding').length

        const count4 = orders.filter(order => order.status === 'rejected').length
        const count5 = orders.filter(order => order.status === 'failed').length
        const count6 = orders.filter(order => order.status === 'canceled').length

        const totalBadStatuses = count4 + count5 + count6

        return <div>

            <div className="row no-gutters bg-white">
                <div className="col-12 col-md-3">
                    <div className={"p-2 text-center" + (total > 0 ? " c-red-500" : "")}>
                        {translator('total')}:&nbsp;{total}
                    </div>
                </div>
                <div className="col-12 col-md-9">

                    <div className="row no-gutters">
                        <div className="col">
                            <div className={"p-2 text-center" + (count1 > 0 ? " c-red-500" : "")}>
                                <i className="fa fa-recycle"/>&nbsp;{translator('order_types_recycling')}
                                :&nbsp;{count1}&nbsp;({numberFormat(total > 0 ? (100 * count1 / total) : 0)}%)
                            </div>
                        </div>
                        <div className="col">
                            <div className={"p-2 text-center" + (count2 > 0 ? " c-red-500" : "")}>
                                <i className="fa fa-cubes"/>&nbsp;{translator('order_types_junk_removal')}
                                :&nbsp;{count2}&nbsp;({numberFormat(total > 0 ? (100 * count2 / total) : 0)}%)
                            </div>
                        </div>
                        <div className="col">
                            <div className={"p-2 text-center" + (count3 > 0 ? " c-red-500" : "")}>
                                <i className="fa fa-stack-overflow"/>&nbsp;{translator('order_types_shredding')}
                                :&nbsp;{count3}&nbsp;({numberFormat(total > 0 ? (100 * count3 / total) : 0)}%)
                            </div>
                        </div>
                    </div>

                    <div className="row no-gutters">
                        <div className="col">
                            <div className={"p-2 text-center" + (count6 > 0 ? " c-red-500" : "")}>
                                <i className='fa fa-ban'/>&nbsp;{translator('order_status_canceled')}
                                :&nbsp;{count6}&nbsp;({numberFormat(totalBadStatuses > 0 ? (100 * count6 / totalBadStatuses) : 0)}%)
                            </div>
                        </div>
                        <div className="col">
                            <div className={"p-2 text-center" + (count4 > 0 ? " c-red-500" : "")}>
                                <i className='fa fa-times'/>&nbsp;{translator('order_status_rejected')}
                                :&nbsp;{count4}&nbsp;({numberFormat(totalBadStatuses > 0 ? (100 * count4 / totalBadStatuses) : 0)}%)
                            </div>
                        </div>
                        <div className="col">
                            <div className={"p-2 text-center" + (count5 > 0 ? " c-red-500" : "")}>
                                <i className='fa fa-warning'/>&nbsp;{translator('order_status_failed')}
                                :&nbsp;{count5}&nbsp;({numberFormat(totalBadStatuses > 0 ? (100 * count5 / totalBadStatuses) : 0)}%)
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <GoogleMap
                containerElement={<div className="map-container"
                                       style={{height: Math.max(400, (window.innerHeight - 65 - 44)) + 'px'}}/>}
                activeMarker={activeMarker}
                onMarkerClose={this.onMarkerClose}
                onMarkerOpen={this.onMarkerOpen}
                markers={orders.map(order => ({
                    order,
                    lat: order.location.lat,
                    lng: order.location.lng,
                }))}/>
        </div>
    }
}

export default withRouter(connect(selectors)(WorldMap))
