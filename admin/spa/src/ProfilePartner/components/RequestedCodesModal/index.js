import React from 'react';
import {connect} from 'react-redux';

import selectors from './selectors';
import Save from '../../actions/Save';
import translator from '../../../translations/translator';
import {MODEL_CHANGED, ADD_POSTAL_CODE, REMOVE_POSTAL_CODE} from "../../actions";
import {cid, objectValues} from "../../../Common/utils";

class Index extends React.Component {

    submit = () => {
        const {id} = this.props

        const {requestedPostalCodes} = this.props.RequestedCodes

        const requests = objectValues(requestedPostalCodes)

        this.props.dispatch(Save({
            id: id,
            requestedPostalCodes: requests
        }))
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeRequestType = (cid) => e => {

        let value = e.target.value
        if (value === 'none') value = null

        this.props.dispatch({
            type: MODEL_CHANGED,
            payload: {
                request: {
                    cid,
                    type: value
                }
            }
        })
    }

    changeRequestPostalCode = cid => e => {

        this.props.dispatch({
            type: MODEL_CHANGED,
            payload: {
                request: {
                    cid,
                    postalCode: e.target.value.replace(/[^0-9]/g, '')
                }
            }
        })
    }

    removePostalCode = cid => () => {

        this.props.dispatch({
            type: REMOVE_POSTAL_CODE,
            payload: {
                cid,
            }
        })
    }

    addPostalCode = () => {

        this.props.dispatch({
            type: ADD_POSTAL_CODE,
            payload: {
                cid: cid(),
                postalCode: null,
                type: null
            }
        })
    }

    renderPostalCodes() {

        const {requestedPostalCodes} = this.props.RequestedCodes

        const requests = objectValues(requestedPostalCodes)

        return <div className="row">

            {requests.map((request, i) => {

                return <div key={i} className="col-12">

                    <div className="form-group">
                        <div className="input-group">

                            {requests.length > 1 ?
                                <div className="input-group-prepend">
                                    <button className="btn btn-outline-secondary"
                                            onClick={this.removePostalCode(request.cid)}>
                                        <i className="fa fa-times"/>
                                    </button>
                                </div> : null}

                            <input type="text"
                                   name="postalCode"
                                   className="form-control"
                                   placeholder={translator('postal_code')}
                                   onChange={this.changeRequestPostalCode(request.cid)}
                                   value={request.postalCode || ""}/>

                            <div className="input-group-append">
                                <select name="type"
                                        value={request.type || 'none'}
                                        onChange={this.changeRequestType(request.cid)}
                                        className="form-control">
                                    <option value="none" disabled={true}>{translator("select_type")}</option>
                                    <option value="junk_removal">{translator("order_types_junk_removal")}</option>
                                    <option value="recycling">{translator("order_types_recycling")}</option>
                                    <option value="donation">{translator("order_types_donation")}</option>
                                    <option disabled={true}
                                            value="shredding">{translator("order_types_shredding")}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            })}
        </div>
    }

    render() {

        return <div className="modal-open">
            <div className="modal fade show d-block">

                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h4 className="modal-title">{translator('partner_postal_codes')}</h4>
                        </div>
                        <div className="modal-body">

                            {this.renderPostalCodes()}

                            <div className="text-right">
                                <button className="btn btn-sm btn-outline-success"
                                        onClick={this.addPostalCode}>
                                    <i className="fa fa-plus"/>&nbsp;{translator('add')}
                                </button>
                            </div>

                        </div>
                        <div className="modal-footer">
                            <button className="btn btn-sm btn-primary"
                                    onClick={this.submit}>
                                <i className="fa fa-check"/>&nbsp;{translator('save')}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    }
}

export default connect(selectors)(Index)
