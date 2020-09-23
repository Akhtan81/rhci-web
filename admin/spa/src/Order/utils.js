import translator from "../translations/translator";
import React from "react";

export const renderType = type => {

    switch (type) {
        case 'recycling':
            return <div className="badge badge-pill badge-success">
                <i className="fa fa-recycle"/>&nbsp;{translator('order_types_recycling')}
            </div>
        case 'junk_removal':
            return <div className="badge badge-pill badge-warning">
                <i className="fa fa-cubes"/>&nbsp;{translator('order_types_junk_removal')}
            </div>
        case 'shredding':
            return <div className="badge badge-pill badge-primary">
                <i className="fa fa-stack-overflow"/>&nbsp;{translator('order_types_shredding')}
            </div>
        case 'donation':
            return <div className="badge badge-pill badge-info">
                <i className="fa fa-gift"/>&nbsp;{translator('order_types_donation')}
            </div>
        case 'busybee':
            return <div className="badge badge-pill badge-success">
                <i className="fa fa-recycle"/>&nbsp;{translator('order_types_busybee')}
            </div>
        case 'moving':
            return <div className="badge badge-pill badge-success">
                <i className="fa fa-recycle"/>&nbsp;{translator('order_types_moving')}
            </div>
        default:
            return type
    }
}

export const renderSubscriptionStatus = status => {

    switch (status) {
        case 'created':
            return <div className="badge badge-pill badge-light">
                <i className="fa fa-clock-o"/>&nbsp;{translator('subscription_type_created')}
            </div>
        case 'active': //fas - fa solid
            return <div className="badge badge-pill badge-success">
                <i className="fas fa-crown"/>&nbsp;{translator('subscription_type_active')}
            </div>
        case 'completed':
            return <div className="badge badge-pill badge-primary">
                <i className="fa fa-check"/>&nbsp;{translator('subscription_type_completed')}
            </div>
        case 'canceled':
            return <div className="badge badge-pill badge-dark">
                <i className="fa fa-ban"/>&nbsp;{translator('subscription_type_canceled')}
            </div>
        default:
            return status
    }
}

export const renderStatus = status => {
    switch (status) {
        case 'created':
            return <div className="badge badge-pill badge-light">
                {translator('order_status_created')}
            </div>
        case 'approved':
            return <div className="badge badge-pill badge-success">
                <i className='fa fa-thumbs-up'/>&nbsp;{translator('order_status_approved')}
            </div>
        case 'rejected':
            return <div className="badge badge-pill badge-danger">
                <i className='fa fa-times'/>&nbsp;{translator('order_status_rejected')}
            </div>
        case 'in_progress':
            return <div className="badge badge-pill badge-warning">
                <i className='fa fa-bolt'/>&nbsp;{translator('order_status_in_progress')}
            </div>
        case 'done':
            return <div className="badge badge-pill badge-primary">
                <i className='fa fa-check'/>&nbsp;{translator('order_status_done')}
            </div>
        case 'canceled':
            return <div className="badge badge-pill badge-dark">
                <i className='fa fa-ban'/>&nbsp;{translator('order_status_canceled')}
            </div>
        case 'failed':
            return <div className="badge badge-pill badge-dark">
                <i className='fa fa-warning'/>&nbsp;{translator('order_status_failed')}
            </div>
        default:
            return status
    }
}