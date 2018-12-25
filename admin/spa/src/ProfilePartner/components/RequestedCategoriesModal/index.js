import React from 'react';
import {connect} from 'react-redux';

import selectors from './selectors';
import Save from '../../actions/Save';
import translator from '../../../translations/translator';
import {ADD_CATEGORY, MODEL_CHANGED, REMOVE_CATEGORY, TOGGLE_REQUESTED_CATEGORIES_MODAL} from "../../actions";
import {cid, objectValues} from "../../../Common/utils";
import FetchCategories from "../../actions/FetchCategories";

class RequestedCategoriesModal extends React.Component {

    componentWillMount() {
        this.props.dispatch(FetchCategories())
    }

    submit = () => {
        const {id} = this.props

        const {requestedCategories} = this.props.RequestedCategories

        const requests = objectValues(requestedCategories)

        this.props.dispatch(Save({
            id: id,
            requestedCategories: requests.map(item => ({
                category: {
                    id: item.category
                }
            }))
        }))
    }

    cancel = () => this.props.dispatch({
        type: TOGGLE_REQUESTED_CATEGORIES_MODAL,
    })

    change = cid => e => {

        this.props.dispatch({
            type: MODEL_CHANGED,
            payload: {
                request: {
                    cid,
                    category: e.target.value.replace(/[^0-9]/g, '')
                }
            }
        })
    }

    removeCategory = cid => () => {

        this.props.dispatch({
            type: REMOVE_CATEGORY,
            payload: {
                cid,
            }
        })
    }

    addCategory = () => {

        this.props.dispatch({
            type: ADD_CATEGORY,
            payload: {
                cid: cid(),
                category: null,
            }
        })
    }

    renderCategories() {

        const {requestedCategories} = this.props.RequestedCategories
        const {items} = this.props.Categories

        const requests = objectValues(requestedCategories)

        return <div className="row">

            {requests.map((request, i) => {

                return <div key={i} className="col-12">

                    <div className="form-group">
                        <div className="input-group">

                            {requests.length > 1 ?
                                <div className="input-group-prepend">
                                    <button className="btn btn-outline-secondary"
                                            onClick={this.removeCategory(request.cid)}>
                                        <i className="fa fa-times"/>
                                    </button>
                                </div> : null}

                            <select name="type"
                                    value={request.category || 'none'}
                                    onChange={this.change(request.cid)}
                                    className="form-control">
                                <option value="none" disabled={true}>{translator("select_type")}</option>

                                {items.map((item, i) => {
                                    let lvl = ''
                                    for (let i = 0; i < item.lvl; i++) {
                                        lvl += ' - '
                                    }

                                    const disabled = !!requests.find(request => request.category === item.id)

                                    return <option
                                        key={i}
                                        disabled={disabled}
                                        value={item.id}>{lvl}{item.name}</option>
                                })}

                            </select>
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
                            <h4 className="modal-title">{translator('partner_register_recycling')}</h4>
                        </div>
                        <div className="modal-body">

                            {this.renderCategories()}

                            <div className="text-right">
                                <button className="btn btn-sm btn-outline-success"
                                        onClick={this.addCategory}>
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

export default connect(selectors)(RequestedCategoriesModal)
