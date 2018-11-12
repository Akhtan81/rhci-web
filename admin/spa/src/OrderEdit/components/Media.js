import React from 'react'
import PropTypes from 'prop-types'
import {connect} from 'react-redux'
import selectors from './selectors'
import {TOGGLE_GALLERY} from '../actions'

const imgStyle = {
    width: '100%',
}
const imgContainerStyle = {
    overflow: 'hidden',
    maxHeight: '75px'
}

class Media extends React.Component {

    toggleGallery = () => {
        const {images} = this.props.OrderEdit.Gallery
        const {media} = this.props

        const image = images.find(item => item.id === media.id)

        this.props.dispatch({
            type: TOGGLE_GALLERY,
            payload: images.indexOf(image)
        })
    }

    render() {

        const {media} = this.props

        return <div className="col-6 col-md-4 col-lg-2">
            <div className="card mb-2 mr-2" onClick={this.toggleGallery}>
                <div className="card-img-top text-center" style={imgContainerStyle}>
                    <img style={imgStyle} src={media.url}/>
                </div>
                <div className="card-body p-3 text-truncate">
                    <a href={media.url} download={true}>
                        <small className="card-text m-0">{media.name}</small>
                    </a>
                </div>
            </div>
        </div>
    }
}

Media.propTypes = {
    media: PropTypes.any.isRequired
}

export default connect(selectors)(Media)
