import React from 'react'
import PropTypes from 'prop-types'
import mapStyle from './style'

import {GoogleMap, InfoWindow, Marker, withGoogleMap, withScriptjs,} from "react-google-maps"

import {Link} from "react-router-dom";
import {compose, lifecycle, withProps} from "recompose";

import {MarkerClusterer} from "react-google-maps/lib/components/addons/MarkerClusterer"
import translator from "../../../translations/translator";
import {renderType, renderStatus} from "../../../Order/utils";

const defaultOptions = {
    styles: mapStyle,
    fullscreenControl: false,
    streetViewControl: false,
}

const fullHeightStyle = {height: Math.max(400, (window.innerHeight - 65)) + 'px'}

const defaultCenter = {
    lat: 35.8057953,
    lng: -119.8243901,
}

const MapWrapper = compose(
    withProps({
        googleMapURL: "https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=geometry,drawing,places&key=" + AppParameters.googleMapsApiKey,
        loadingElement: <div/>,
        containerElement: <div className="map-container" style={fullHeightStyle}/>,
        mapElement: <div className="map" style={{height: `100%`}}/>,
    }),
    lifecycle({
        componentDidMount() {

            this.setState({

                zoomToMarkers: map => {

                    if (!map) return

                    const bounds = new window.google.maps.LatLngBounds();
                    const cluster = map.props.children

                    let hasMarkers = false

                    cluster.props.children.forEach((child) => {
                        if (child.type === Marker) {

                            hasMarkers = true

                            bounds.extend(new window.google.maps.LatLng(
                                child.props.position.lat,
                                child.props.position.lng
                            ));
                        }
                    })

                    if (hasMarkers)
                        map.fitBounds(bounds, 5);
                }
            })
        },
    }),
    withScriptjs,
    withGoogleMap
)(props =>
    <GoogleMap
        ref={props.zoomToMarkers}
        defaultZoom={5}
        maxZoom={5}
        options={defaultOptions}
        defaultCenter={defaultCenter}>

        <MarkerClusterer
            averageCenter
            enableRetinaIcons
            gridSize={60}>
            {props.markers.map((marker, i) => (
                <Marker
                    key={i}
                    position={marker}
                    onClick={props.onMarkerOpen.bind(this, marker.order.id)}>
                    {props.activeMarker === marker.order.id
                        ? <InfoWindow
                            onCloseClick={props.onMarkerClose}>
                            <div className="p-2">
                                <div className="table-responsive">
                                    <table className="table table-sm m-0 p-0">
                                        <tbody>
                                        <tr>
                                            <th>{translator('id')}</th>
                                            <td><Link to={"/orders/" + marker.order.id}>#{marker.order.id}</Link></td>
                                        </tr>
                                        <tr>
                                            <th>{translator('created_at')}</th>
                                            <td>{marker.order.createdAt}</td>
                                        </tr>
                                        <tr>
                                            <th>{translator('type')}</th>
                                            <td>{renderType(marker.order.type)}</td>
                                        </tr>
                                        <tr>
                                            <th>{translator('status')}</th>
                                            <td>{renderStatus(marker.order.status)}</td>
                                        </tr>
                                        <tr>
                                            <th>{translator('status_reason')}</th>
                                            <td>{marker.order.statusReason}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </InfoWindow>
                        : null}
                </Marker>
            ))}
        </MarkerClusterer>
    </GoogleMap>
);

MapWrapper.propTypes = {
    activeMarker: PropTypes.any,
    onMarkerOpen: PropTypes.func.isRequired,
    onMarkerClose: PropTypes.func.isRequired,
    markers: PropTypes.array
}

export default MapWrapper