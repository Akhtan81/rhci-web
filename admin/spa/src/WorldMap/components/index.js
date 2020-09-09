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
        const countRecycling = orders.filter(order => order.type === 'recycling').length
        const countJunk = orders.filter(order => order.type === 'junk_removal').length
        const countDonation = orders.filter(order => order.type === 'donation').length
        const countShredding = orders.filter(order => order.type === 'shredding').length
        const countbusybee = orders.filter(order => order.type === 'busybee').length
        const countRejected = orders.filter(order => order.status === 'rejected').length
        const countFailed = orders.filter(order => order.status === 'failed').length
        const countCanceled = orders.filter(order => order.status === 'canceled').length

        const totalBadStatuses = countRejected + countFailed + countCanceled

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
                            <div className={"p-2 text-center" + (countRecycling > 0 ? " c-red-500" : "")}>
                                <i className="fa fa-recycle"/>&nbsp;{translator('order_types_recycling')}
                                :&nbsp;{countRecycling}&nbsp;({numberFormat(total > 0 ? (100 * countRecycling / total) : 0)}%)
                            </div>
                        </div>
                        <div className="col">
                            <div className={"p-2 text-center" + (countJunk > 0 ? " c-red-500" : "")}>
                                <i className="fa fa-cubes"/>&nbsp;{translator('order_types_junk_removal')}
                                :&nbsp;{countJunk}&nbsp;({numberFormat(total > 0 ? (100 * countJunk / total) : 0)}%)
                            </div>
                        </div>
                        <div className="col">
                            <div className={"p-2 text-center" + (countDonation > 0 ? " c-red-500" : "")}>
                                <i className="fa fa-gift"/>&nbsp;{translator('order_types_donation')}
                                :&nbsp;{countDonation}&nbsp;({numberFormat(total > 0 ? (100 * countDonation / total) : 0)}%)
                            </div>
                        </div>
                        <div className="col">
                            <div className={"p-2 text-center" + (countShredding > 0 ? " c-red-500" : "")}>
                                <i className="fa fa-stack-overflow"/>&nbsp;{translator('order_types_shredding')}
                                :&nbsp;{countShredding}&nbsp;({numberFormat(total > 0 ? (100 * countShredding / total) : 0)}%)
                            </div>
                        </div>
                        <div className="col">
                            <div className={"p-2 text-center" + (countbusybee > 0 ? " c-red-500" : "")}>
                                <i className="fa fa-gift"/>&nbsp;{translator('order_types_busybee')}
                                :&nbsp;{countbusybee}&nbsp;({numberFormat(total > 0 ? (100 * countbusybee / total) : 0)}%)
                            </div>
                        </div>
                    </div>

                    <div className="row no-gutters">
                        <div className="col">
                            <div className={"p-2 text-center" + (countCanceled > 0 ? " c-red-500" : "")}>
                                <i className='fa fa-ban'/>&nbsp;{translator('order_status_canceled')}
                                :&nbsp;{countFailed}&nbsp;({numberFormat(totalBadStatuses > 0 ? (100 * countCanceled / totalBadStatuses) : 0)}%)
                            </div>
                        </div>
                        <div className="col">
                            <div className={"p-2 text-center" + (countRejected > 0 ? " c-red-500" : "")}>
                                <i className='fa fa-times'/>&nbsp;{translator('order_status_rejected')}
                                :&nbsp;{countShredding}&nbsp;({numberFormat(totalBadStatuses > 0 ? (100 * countRejected / totalBadStatuses) : 0)}%)
                            </div>
                        </div>
                        <div className="col">
                            <div className={"p-2 text-center" + (countFailed > 0 ? " c-red-500" : "")}>
                                <i className='fa fa-warning'/>&nbsp;{translator('order_status_failed')}
                                :&nbsp;{countRejected}&nbsp;({numberFormat(totalBadStatuses > 0 ? (100 * countFailed / totalBadStatuses) : 0)}%)
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
