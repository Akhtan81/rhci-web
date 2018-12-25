import React from 'react';
import {connect} from 'react-redux';

import selectors from './selectors';
import translator from '../../../translations/translator';
import {ADD_POSTAL_CODE, MODEL_CHANGED, REMOVE_POSTAL_CODE, TOGGLE_REQUESTED_CODES_MODAL} from "../../actions";
import {cid, objectValues} from "../../../Common/utils";

import FetchOrderTypes from "../../actions/FetchOrderTypes";
import Save from '../../actions/Save';

class Index extends React.Component {

    componentWillMount() {
        this.props.dispatch(FetchOrderTypes())
    }

    submit = () => {
        const {id} = this.props

        const {requestedPostalCodes} = this.props.RequestedCodes

        const requests = objectValues(requestedPostalCodes)

        this.props.dispatch(Save({
            id: id,
            requestedPostalCodes: requests
        }))
    }

    cancel = () => this.props.dispatch({
        type: TOGGLE_REQUESTED_CODES_MODAL,
    })

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
        const {items} = this.props.OrderTypes

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
                                    {items.map((item, i) =>
                                        <option
                                            key={i}
                                            value={item.key}
                                            disabled={item.disabled === true}>{item.name}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            })}
        </div>
    }

    render() {

        const {isLoading} = this.props

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
                            <button className="btn btn-sm btn-default"
                                    onClick={this.cancel}>
                                <i className="fa fa-ban"/>&nbsp;{translator('cancel')}
                            </button>
                            <button className="btn btn-sm btn-primary"
                                    disabled={isLoading}
                                    onClick={this.submit}>
                                <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                                &nbsp;{translator('save')}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    }
}

export default connect(selectors)(Index)
