import React from 'react'
import PropTypes from 'prop-types'
import {withScriptjs, withGoogleMap, GoogleMap} from "react-google-maps"

const Map = withScriptjs(withGoogleMap((props) =>
    <GoogleMap
        defaultZoom={8}
        defaultCenter={{
            lat: props.lat,
            lng: props.lng
        }}
    />
))

const MapWrapper = (props) => <Map
    googleMapURL={"https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=geometry,drawing,places&key=" + AppParameters.googleMapsAccessToken}
    loadingElement={<div style={{height: `100%`}}/>}
    containerElement={<div style={{height: `400px`}}/>}
    mapElement={<div style={{height: `100%`}}/>}
    {...props}/>

MapWrapper.propTypes = {
    lat: PropTypes.number.isRequired,
    lng: PropTypes.number.isRequired,
}

export default MapWrapper