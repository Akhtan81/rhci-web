import translator from "../translations/translator";
import React from "react";

export const renderPaymentStatus = status => {
    switch (status) {
        case 'created':
            return <div className="badge badge-pill badge-light">
                <i className='fa fa-clock-o'/>&nbsp;{translator('payment_status_created')}
            </div>
        case 'success':
            return <div className="badge badge-pill badge-success">
                <i className='fa fa-thumbs-up'/>&nbsp;{translator('payment_status_success')}
            </div>
        case 'failure':
            return <div className="badge badge-pill badge-danger">
                <i className='fa fa-thumbs-down'/>&nbsp;{translator('payment_status_failure')}
            </div>
        default:
            return status
    }
}