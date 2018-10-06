import React from 'react'
import PropTypes from 'prop-types'

const imgStyle = {
    width: '100%',
}
const imgContainerStyle = {
    overflow: 'hidden',
    maxHeight: '75px'
}

class Media extends React.Component {
    render() {

        const {media} = this.props

        return <div className="col-6 col-md-4 col-lg-2">
            <a className="card mb-2 mr-2" href={media.url} download={true}>
                <div className="card-img-top text-center" style={imgContainerStyle}>
                    <img style={imgStyle} src={media.url}/>
                </div>
                <div className="card-body p-3 text-truncate">
                    <small className="card-text m-0">{media.name}</small>
                </div>
            </a>
        </div>
    }
}

Media.propTypes = {
    media: PropTypes.any.isRequired
}

export default Media
